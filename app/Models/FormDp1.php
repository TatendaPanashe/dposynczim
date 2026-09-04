<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'status', 'data_subject_count', 'tier', 'registration_fee',
    'application_fee', 'total_fee', 'entity_profile', 'processing_details',
    'sensitive_data_details', 'processors', 'cross_border_transfers',
    'security_measures', 'attachments', 'submitted_at', 'renewal_due_at',
])]
class FormDp1 extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'entity_profile' => 'encrypted:array',
            'processing_details' => 'encrypted:array',
            'sensitive_data_details' => 'encrypted:array',
            'processors' => 'encrypted:array',
            'cross_border_transfers' => 'encrypted:array',
            'security_measures' => 'encrypted:array',
            'attachments' => 'array',
            'submitted_at' => 'datetime',
            'renewal_due_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
