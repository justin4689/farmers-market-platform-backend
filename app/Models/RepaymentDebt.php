<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RepaymentDebt extends Pivot
{
    protected $table = 'repayment_debt';

    protected $fillable = [
        'repayment_id',
        'debt_id',
        'amount_applied_fcfa',
    ];

    protected function casts(): array
    {
        return [
            'amount_applied_fcfa' => 'integer',
        ];
    }

    public function repayment(): BelongsTo
    {
        return $this->belongsTo(Repayment::class);
    }

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }
}
