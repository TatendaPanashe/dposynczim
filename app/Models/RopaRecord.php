<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'processing_activity', 'purpose', 'data_subject_categories',
    'personal_data_categories', 'legal_basis', 'recipients', 'retention_period',
    'security_measures', 'cross_border_transfer', 'owner', 'reviewed_at',
])]
class RopaRecord extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'data_subject_categories' => 'encrypted:array',
            'personal_data_categories' => 'encrypted:array',
            'recipients' => 'encrypted:array',
            'security_measures' => 'encrypted:array',
            'cross_border_transfer' => 'encrypted:array',
            'reviewed_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
