<?php

namespace App\Models;

use App\Casts\ResilientEncrypted;
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
            'entity_profile' => ResilientEncrypted::class.':array',
            'processing_details' => ResilientEncrypted::class.':array',
            'sensitive_data_details' => ResilientEncrypted::class.':array',
            'processors' => ResilientEncrypted::class.':array',
            'cross_border_transfers' => ResilientEncrypted::class.':array',
            'security_measures' => ResilientEncrypted::class.':array',
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
