<?php

namespace App\Notifications;

use App\Models\OrganisationComplianceObligation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplianceObligationReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public OrganisationComplianceObligation $obligation,
        public string $reminderKey,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Compliance reminder: '.$this->obligation->title)
            ->greeting('Hello '.$notifiable->name)
            ->line($this->obligation->title.' is due on '.$this->obligation->due_at->format('d M Y').'.')
            ->line('Status: '.str($this->obligation->status)->headline())
            ->action('Open obligation', route('compliance.calendar.show', $this->obligation))
            ->line('This reminder was generated from the compliance calendar.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'obligation_id' => $this->obligation->getKey(),
            'title' => $this->obligation->title,
            'due_at' => $this->obligation->due_at?->toDateString(),
            'reminder_key' => $this->reminderKey,
        ];
    }
}
