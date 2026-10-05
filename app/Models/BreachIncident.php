<?php

namespace App\Models;

use App\Casts\ResilientEncrypted;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'reference', 'title', 'description', 'occurred_at',
    'detected_at', 'sla_due_at', 'severity', 'status', 'affected_data_subjects',
    'dp3_submitted_at', 'lessons_learned',
])]
class BreachIncident extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'description' => ResilientEncrypted::class,
            'affected_data_subjects' => ResilientEncrypted::class.':array',
            'lessons_learned' => ResilientEncrypted::class,
            'occurred_at' => 'datetime',
            'detected_at' => 'datetime',
            'sla_due_at' => 'datetime',
            'dp3_submitted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
