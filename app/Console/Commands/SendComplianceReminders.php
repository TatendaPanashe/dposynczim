<?php

namespace App\Console\Commands;

use App\Models\ComplianceReminderLog;
use App\Models\OrganisationComplianceObligation;
use App\Models\User;
use App\Notifications\ComplianceObligationReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('compliance:send-reminders')]
#[Description('Send due-soon and overdue compliance obligation reminders.')]
class SendComplianceReminders extends Command
{
    public function handle(): int
    {
        $sent = 0;

        OrganisationComplianceObligation::with(['assignedUser', 'reviewer'])
            ->whereNotIn('status', ['completed', 'waived'])
            ->whereDate('due_at', '<=', today()->addDays(90))
            ->chunkById(100, function ($obligations) use (&$sent): void {
                foreach ($obligations as $obligation) {
                    if ($obligation->due_at->isPast() && $obligation->status !== 'overdue') {
                        $obligation->update(['status' => 'overdue']);
                        $obligation->activityLogs()->create(['action' => 'marked_overdue']);
                    }

                    $daysUntilDue = today()->diffInDays($obligation->due_at, false);
                    $reminderDays = collect($obligation->reminder_days ?? [30, 14, 7, 1])->map(fn ($day): int => (int) $day);
                    $shouldSendDueSoon = $reminderDays->contains($daysUntilDue);
                    $shouldSendOverdue = $daysUntilDue < 0;

                    if (! $shouldSendDueSoon && ! $shouldSendOverdue) {
                        continue;
                    }

                    $reminderKey = $shouldSendOverdue ? 'overdue:'.abs($daysUntilDue) : 'due:'.$daysUntilDue;
                    $recipients = collect([$obligation->assignedUser, $shouldSendOverdue ? $obligation->reviewer : null])->filter();

                    foreach ($recipients as $recipient) {
                        if (! $recipient instanceof User) {
                            continue;
                        }

                        $log = ComplianceReminderLog::firstOrCreate([
                            'organisation_compliance_obligation_id' => $obligation->getKey(),
                            'user_id' => $recipient->getKey(),
                            'reminder_key' => $reminderKey,
                        ], [
                            'days_before_due' => $daysUntilDue,
                            'sent_at' => now(),
                            'channels' => ['mail', 'database'],
                        ]);

                        if ($log->wasRecentlyCreated) {
                            $recipient->notify(new ComplianceObligationReminder($obligation, $reminderKey));
                            $obligation->activityLogs()->create([
                                'user_id' => $recipient->getKey(),
                                'action' => 'reminder_sent',
                                'properties' => ['reminder_key' => $reminderKey],
                            ]);
                            $sent++;
                        }
                    }
                }
            });

        $this->info("Sent {$sent} compliance reminder(s).");

        return self::SUCCESS;
    }
}
