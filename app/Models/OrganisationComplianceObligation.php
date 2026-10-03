<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'organization_id', 'compliance_obligation_template_id', 'parent_obligation_id',
    'assigned_user_id', 'reviewer_user_id', 'completed_by_user_id', 'title',
    'short_code', 'description', 'category', 'regulator', 'jurisdiction', 'frequency',
    'reminder_days', 'evidence_required', 'risk_level', 'legal_reference', 'status',
    'due_at', 'first_due_at', 'completed_at', 'reviewed_at', 'waiver_reason',
    'waived_at', 'linked_record_type', 'linked_record_id', 'linked_record_snapshot',
    'notes',
])]
class OrganisationComplianceObligation extends Model
{
    use BelongsToOrganization, SoftDeletes;

    public const STATUSES = ['not_started', 'in_progress', 'submitted', 'completed', 'overdue', 'waived'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ComplianceObligationTemplate::class, 'compliance_obligation_template_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ComplianceObligationChecklistItem::class)->orderBy('sort_order');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(ComplianceObligationEvidence::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ComplianceObligationActivityLog::class)->latest();
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ComplianceReminderLog::class);
    }

    public function scopeDueBetween(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('due_at', [$start, $end]);
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->isAdmin()
            || $this->organization_id === $user->activeOrganization()?->getKey()
            || $this->assigned_user_id === $user->getKey()
            || $this->reviewer_user_id === $user->getKey();
    }

    protected function casts(): array
    {
        return [
            'reminder_days' => 'array',
            'evidence_required' => 'boolean',
            'due_at' => 'date',
            'first_due_at' => 'date',
            'completed_at' => 'date',
            'reviewed_at' => 'datetime',
            'waived_at' => 'date',
            'linked_record_snapshot' => 'array',
        ];
    }
}
