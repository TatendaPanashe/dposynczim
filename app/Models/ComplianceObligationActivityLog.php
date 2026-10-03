<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organisation_compliance_obligation_id', 'user_id', 'action', 'properties'])]
class ComplianceObligationActivityLog extends Model
{
    public function obligation(): BelongsTo
    {
        return $this->belongsTo(OrganisationComplianceObligation::class, 'organisation_compliance_obligation_id');
    }

    protected function casts(): array
    {
        return ['properties' => 'array'];
    }
}
