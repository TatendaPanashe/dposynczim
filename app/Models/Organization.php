<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'registration_number', 'slug'])]
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

    public function dp1s(): HasMany
    {
        return $this->hasMany(FormDp1::class);
    }
}
