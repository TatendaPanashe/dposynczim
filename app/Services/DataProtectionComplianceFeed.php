<?php

namespace App\Services;

use App\Models\BreachIncident;
use App\Models\ComplianceForm;
use App\Models\FormDp1;
use App\Models\FormDp2;
use App\Models\Organization;
use App\Models\RopaRecord;
use Illuminate\Support\Collection;

class DataProtectionComplianceFeed
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function linkedItemsFor(Organization $organization): Collection
    {
        return collect()
            ->merge(FormDp1::forOrganization($organization->getKey())->whereNotNull('renewal_due_at')->get()->map(fn (FormDp1 $record): array => [
                'type' => FormDp1::class,
                'id' => $record->getKey(),
                'title' => 'POTRAZ/data-protection licence renewal',
                'status' => $record->status,
                'due_at' => $record->renewal_due_at,
            ]))
            ->merge(FormDp2::forOrganization($organization->getKey())->get()->map(fn (FormDp2 $record): array => [
                'type' => FormDp2::class,
                'id' => $record->getKey(),
                'title' => 'DPO appointment/review',
                'status' => $record->status,
                'due_at' => $record->appointed_at?->copy()->addYear(),
            ]))
            ->merge(RopaRecord::forOrganization($organization->getKey())->whereNotNull('action_due_date')->get()->map(fn (RopaRecord $record): array => [
                'type' => RopaRecord::class,
                'id' => $record->getKey(),
                'title' => $record->processing_activity,
                'status' => $record->risk_impact ?? 'recorded',
                'due_at' => $record->action_due_date,
            ]))
            ->merge(BreachIncident::forOrganization($organization->getKey())->get()->map(fn (BreachIncident $record): array => [
                'type' => BreachIncident::class,
                'id' => $record->getKey(),
                'title' => $record->title,
                'status' => $record->status,
                'due_at' => $record->sla_due_at?->toDateString(),
            ]))
            ->merge(ComplianceForm::forOrganization($organization->getKey())->whereNotNull('review_due_at')->get()->map(fn (ComplianceForm $record): array => [
                'type' => ComplianceForm::class,
                'id' => $record->getKey(),
                'title' => $record->title,
                'status' => $record->computedStatus(),
                'due_at' => $record->review_due_at,
            ]))
            ->filter(fn (array $item): bool => filled($item['due_at']))
            ->values();
    }
}
