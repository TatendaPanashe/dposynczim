<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'name', 'registration_number', 'slug', 'business_sector', 'legal_structure',
    'physical_address', 'postal_address', 'telephone', 'fax', 'email', 'business_scope',
])]
class Organization extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function dpos(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function dp1s(): HasMany
    {
        return $this->hasMany(FormDp1::class);
    }

    public function activeDpoAppointment(): HasOne
    {
        return $this->hasOne(FormDp2::class)->where('status', 'active')->latestOfMany();
    }

    public function complianceForms(): HasMany
    {
        return $this->hasMany(ComplianceForm::class);
    }

    public function complianceObligations(): HasMany
    {
        return $this->hasMany(OrganisationComplianceObligation::class);
    }
}
