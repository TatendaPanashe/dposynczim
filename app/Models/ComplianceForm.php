<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'type', 'title', 'status', 'owner', 'reference',
    'effective_at', 'expires_at', 'review_due_at', 'purpose', 'data_subjects',
    'legal_basis', 'authorisation_details', 'safeguards', 'notes',
])]
class ComplianceForm extends Model
{
    use BelongsToOrganization;

    public const TYPES = [
        'consent' => 'Consent form',
        'cross_border_authorisation' => 'Cross-border authorisation',
        'general' => 'General compliance form',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'active' => 'Active',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'expired' => 'Expired',
        'needs_review' => 'Needs review',
    ];

    protected function casts(): array
    {
        return [
            'effective_at' => 'date',
            'expires_at' => 'date',
            'review_due_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? str($this->type)->headline()->toString();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->computedStatus()] ?? str($this->computedStatus())->headline()->toString();
    }

    public function computedStatus(): string
    {
        if ($this->expires_at?->isPast()) {
            return 'expired';
        }

        if ($this->review_due_at?->isPast() && in_array($this->status, ['active', 'approved', 'submitted'], true)) {
            return 'needs_review';
        }

        return $this->status;
    }

    public function statusTone(): string
    {
        return match ($this->computedStatus()) {
            'active', 'approved' => 'green',
            'submitted' => 'cyan',
            'draft', 'needs_review' => 'amber',
            'expired' => 'red',
            default => 'slate',
        };
    }
}
