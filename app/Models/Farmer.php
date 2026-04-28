<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    protected $fillable = [
        'identifier',
        'firstname',
        'lastname',
        'phone_number',
        'credit_limit_fcfa',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit_fcfa' => 'integer',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }

    public function openDebts(): HasMany
    {
        return $this->hasMany(Debt::class)->whereIn('status', ['open', 'partial'])->orderBy('created_at');
    }
}
