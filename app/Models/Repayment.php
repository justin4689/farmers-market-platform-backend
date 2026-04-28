<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Repayment extends Model
{
    protected $fillable = [
        'farmer_id',
        'operator_id',
        'kg_received',
        'commodity_rate_fcfa',
        'total_fcfa_credited',
    ];

    protected function casts(): array
    {
        return [
            'kg_received' => 'decimal:3',
            'commodity_rate_fcfa' => 'integer',
            'total_fcfa_credited' => 'integer',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function debts(): BelongsToMany
    {
        return $this->belongsToMany(Debt::class, 'repayment_debt')
            ->withPivot('amount_applied_fcfa')
            ->withTimestamps();
    }
}
