<?php

namespace App\Models;

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
            'description' => 'encrypted',
            'affected_data_subjects' => 'encrypted:array',
            'lessons_learned' => 'encrypted',
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
