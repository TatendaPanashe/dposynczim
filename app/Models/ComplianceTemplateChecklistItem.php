<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['compliance_obligation_template_id', 'title', 'sort_order'])]
class ComplianceTemplateChecklistItem extends Model
{
    public function template(): BelongsTo
    {
        return $this->belongsTo(ComplianceObligationTemplate::class, 'compliance_obligation_template_id');
    }
}
