<?php

namespace App\Repositories\Interfaces;

use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Collection;

interface TransactionItemRepositoryInterface
{
    public function createMany(int $transactionId, array $items): Collection;
    public function findByTransaction(int $transactionId): Collection;
}
