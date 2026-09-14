<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id', 'user_id', 'month', 'amount_cents', 'currency', 'status',
    'merchant_reference', 'pesepay_reference', 'redirect_url', 'metadata', 'paid_at',
])]
class MonthlyPayment extends Model
{
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
