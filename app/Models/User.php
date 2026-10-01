<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'organization_id', 'name', 'email', 'password', 'is_admin',
    'email_verified_at', 'google_id',
    'dpo_registration_number', 'dpo_address', 'dpo_qualifications',
    'dpo_certification_status', 'dpo_reporting_line', 'dpo_official_phone',
    'dpo_mobile',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withPivot('role')->withTimestamps();
    }

    public function activeOrganization(): ?Organization
    {
        if ($this->isAdmin() && session('active_organization_id') === null) {
            return null;
        }

        $activeOrganizationId = session('active_organization_id', $this->organization_id);

        if ($activeOrganizationId === null) {
            return null;
        }

        return $this->organizations()->find($activeOrganizationId)
            ?? ($this->organization_id === $activeOrganizationId ? $this->organization : null);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function hasCompletedDpoProfile(): bool
    {
        return filled($this->name)
            && filled($this->email)
            && filled($this->dpo_address)
            && filled($this->dpo_qualifications)
            && filled($this->dpo_certification_status)
            && filled($this->dpo_reporting_line)
            && filled($this->dpo_official_phone)
            && filled($this->dpo_mobile);
    }

    public function hasAppointedDpoForActiveOrganization(): bool
    {
        $activeOrganization = $this->activeOrganization();

        if ($activeOrganization === null) {
            return false;
        }

        return FormDp2::forOrganization($activeOrganization->getKey())
            ->where('status', 'active')
            ->exists();
    }

    public function needsDpoProfileSetup(): bool
    {
        return ! $this->hasCompletedDpoProfile()
            && ! $this->hasAppointedDpoForActiveOrganization();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'dpo_qualifications' => 'array',
        ];
    }
}
