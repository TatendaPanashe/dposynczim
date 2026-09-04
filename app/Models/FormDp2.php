<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'full_name', 'controller_license_number', 'controller_name',
    'controller_physical_address', 'controller_postal_address', 'controller_telephone',
    'controller_fax', 'controller_email', 'business_scope', 'dpo_registration_number',
    'dpo_address', 'qualifications', 'certification_status', 'reporting_line',
    'official_email', 'official_phone', 'dpo_mobile', 'appointment_declaration',
    'appointed_at', 'status',
])]
class FormDp2 extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'qualifications' => 'encrypted:array',
            'controller_physical_address' => 'encrypted',
            'controller_postal_address' => 'encrypted',
            'business_scope' => 'encrypted',
            'dpo_address' => 'encrypted',
            'appointment_declaration' => 'encrypted',
            'appointed_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
