<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Transaction;
    public function create(array $data): Transaction;
    public function findByFarmer(int $farmerId): Collection;
    public function findByOperator(int $operatorId): Collection;
    public function findWithItems(int $id): ?Transaction;
}
