<?php

namespace App\Models;

use App\Casts\ResilientEncrypted;
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
            'qualifications' => ResilientEncrypted::class.':array',
            'controller_physical_address' => ResilientEncrypted::class,
            'controller_postal_address' => ResilientEncrypted::class,
            'business_scope' => ResilientEncrypted::class,
            'dpo_address' => ResilientEncrypted::class,
            'appointment_declaration' => ResilientEncrypted::class,
            'appointed_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
