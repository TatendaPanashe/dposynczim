<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'compliance_category_id', 'title', 'short_code', 'description', 'regulator',
    'jurisdiction', 'business_type', 'frequency', 'due_date_rule', 'manual_due_date',
    'reminder_days', 'evidence_required', 'is_active', 'risk_level', 'legal_reference',
    'is_configurable_template',
])]
class ComplianceObligationTemplate extends Model
{
    use SoftDeletes;

    public const FREQUENCIES = ['once-off', 'weekly', 'monthly', 'quarterly', 'semi-annual', 'annual', 'custom'];

    public const RISK_LEVELS = ['low', 'medium', 'high', 'critical'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplianceCategory::class, 'compliance_category_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ComplianceTemplateChecklistItem::class)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'due_date_rule' => 'array',
            'manual_due_date' => 'date',
            'reminder_days' => 'array',
            'evidence_required' => 'boolean',
            'is_active' => 'boolean',
            'is_configurable_template' => 'boolean',
        ];
    }
}
