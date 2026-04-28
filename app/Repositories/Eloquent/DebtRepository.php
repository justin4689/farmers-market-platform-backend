<?php

namespace App\Repositories\Eloquent;

use App\Models\Debt;
use App\Repositories\Interfaces\DebtRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DebtRepository implements DebtRepositoryInterface
{
    public function __construct(private readonly Debt $model) {}

    public function find(int $id): ?Debt
    {
        return $this->model->find($id);
    }

    public function create(array $data): Debt
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Debt
    {
        $debt = $this->model->findOrFail($id);
        $debt->update($data);
        return $debt->fresh();
    }

    public function save(Debt $debt): void
    {
        $debt->save();
    }

    public function findByFarmer(int $farmerId): Collection
    {
        return $this->model->where('farmer_id', $farmerId)->with('transaction')->get();
    }

    // FIFO: oldest open/partial debts first
    public function getOpenDebtsByFarmerFifo(int $farmerId): Collection
    {
        return $this->model->where('farmer_id', $farmerId)
            ->whereIn('status', ['open', 'partial'])
            ->orderBy('created_at')
            ->get();
    }

    public function getTotalRemainingByFarmer(int $farmerId): int
    {
        return (int) $this->model->where('farmer_id', $farmerId)
            ->whereIn('status', ['open', 'partial'])
            ->sum('remaining_fcfa');
    }
}
