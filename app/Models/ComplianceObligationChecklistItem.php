<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organisation_compliance_obligation_id', 'title', 'is_completed',
    'completed_at', 'completed_by_user_id', 'sort_order',
])]
class ComplianceObligationChecklistItem extends Model
{
    public function obligation(): BelongsTo
    {
        return $this->belongsTo(OrganisationComplianceObligation::class, 'organisation_compliance_obligation_id');
    }

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }
}
