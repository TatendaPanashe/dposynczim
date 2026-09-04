<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder): void {
            $user = auth()->user();
            $organizationId = $user?->activeOrganization()?->getKey();

            if ($organizationId === null) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->where(
                $builder->getModel()->qualifyColumn('organization_id'),
                $organizationId,
            );
        });

        static::creating(function (Model $model): void {
            if ($model->getAttribute('organization_id') === null) {
                $model->setAttribute('organization_id', auth()->user()?->activeOrganization()?->getKey());
            }
        });
    }

    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->withoutGlobalScope('organization')
            ->where($query->getModel()->qualifyColumn('organization_id'), $organizationId);
    }
}
