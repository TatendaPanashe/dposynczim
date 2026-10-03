<?php

namespace App\Services;

use App\Models\OrganisationComplianceObligation;
use Carbon\CarbonImmutable;

class ComplianceRecurrenceService
{
    public function nextDueDate(OrganisationComplianceObligation $obligation): ?CarbonImmutable
    {
        $dueAt = CarbonImmutable::parse($obligation->due_at);

        return match ($obligation->frequency) {
            'weekly' => $dueAt->addWeek(),
            'monthly' => $dueAt->addMonthNoOverflow(),
            'quarterly' => $dueAt->addMonthsNoOverflow(3),
            'semi-annual' => $dueAt->addMonthsNoOverflow(6),
            'annual' => $dueAt->addYearNoOverflow(),
            default => null,
        };
    }

    public function createNextOccurrence(OrganisationComplianceObligation $obligation): ?OrganisationComplianceObligation
    {
        $nextDueDate = $this->nextDueDate($obligation);

        if ($nextDueDate === null) {
            return null;
        }

        $next = $obligation->replicate([
            'status', 'completed_at', 'completed_by_user_id', 'reviewed_at',
            'waiver_reason', 'waived_at', 'notes',
        ]);
        $next->parent_obligation_id = $obligation->parent_obligation_id ?? $obligation->getKey();
        $next->status = 'not_started';
        $next->due_at = $nextDueDate->toDateString();
        $next->completed_at = null;
        $next->completed_by_user_id = null;
        $next->reviewed_at = null;
        $next->waiver_reason = null;
        $next->waived_at = null;
        $next->notes = null;
        $next->save();

        foreach ($obligation->checklistItems as $item) {
            $next->checklistItems()->create([
                'title' => $item->title,
                'sort_order' => $item->sort_order,
            ]);
        }

        $next->activityLogs()->create([
            'action' => 'created_from_recurrence',
            'properties' => ['previous_obligation_id' => $obligation->getKey()],
        ]);

        return $next;
    }
}
