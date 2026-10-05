<?php

namespace App\Models;

use App\Casts\ResilientEncrypted;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'business_function', 'storage_location', 'processing_activity',
    'purpose', 'data_subject_categories', 'personal_data_categories', 'legal_basis',
    'controller_name', 'representative_name', 'recipients', 'retention_period',
    'retention_basis', 'data_classification', 'processor_name', 'third_party_agreement',
    'security_measures', 'cross_border_transfer', 'transfer_security_measures',
    'protection_assessment', 'data_collection_method', 'consent_evidence',
    'legitimate_interest_assessment', 'data_volume', 'dpia_record', 'data_risks',
    'risk_impact', 'data_breaches', 'breach_notification', 'risk_actions',
    'action_owner', 'action_due_date', 'owner', 'reviewed_at',
])]
class RopaRecord extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'data_subject_categories' => ResilientEncrypted::class.':array',
            'personal_data_categories' => ResilientEncrypted::class.':array',
            'recipients' => ResilientEncrypted::class.':array',
            'security_measures' => ResilientEncrypted::class.':array',
            'cross_border_transfer' => ResilientEncrypted::class.':array',
            'action_due_date' => 'date',
            'reviewed_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
