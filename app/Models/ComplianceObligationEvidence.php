<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organisation_compliance_obligation_id', 'uploaded_by_user_id', 'label',
    'disk', 'path', 'linked_record_type', 'linked_record_id', 'notes',
])]
class ComplianceObligationEvidence extends Model
{
    protected $table = 'compliance_obligation_evidence';

    public function obligation(): BelongsTo
    {
        return $this->belongsTo(OrganisationComplianceObligation::class, 'organisation_compliance_obligation_id');
    }
}
