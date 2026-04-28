<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(private readonly Transaction $model) {}

    public function all(): Collection
    {
        return $this->model->with(['farmer', 'operator'])->get();
    }

    public function find(int $id): ?Transaction
    {
        return $this->model->find($id);
    }

    public function create(array $data): Transaction
    {
        return $this->model->create($data);
    }

    public function findByFarmer(int $farmerId): Collection
    {
        return $this->model->where('farmer_id', $farmerId)
            ->with(['operator', 'items.product'])
            ->get();
    }

    public function findByOperator(int $operatorId): Collection
    {
        return $this->model->where('operator_id', $operatorId)
            ->with(['farmer', 'items.product'])
            ->get();
    }

    public function findWithItems(int $id): ?Transaction
    {
        return $this->model->with(['farmer', 'operator', 'items.product', 'debt'])->find($id);
    }
}
