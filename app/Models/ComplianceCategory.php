<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'is_active'])]
class ComplianceCategory extends Model
{
    use SoftDeletes;

    public function templates(): HasMany
    {
        return $this->hasMany(ComplianceObligationTemplate::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
