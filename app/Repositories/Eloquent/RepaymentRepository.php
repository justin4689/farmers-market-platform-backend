<?php

namespace App\Repositories\Eloquent;

use App\Models\Repayment;
use App\Repositories\Interfaces\RepaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RepaymentRepository implements RepaymentRepositoryInterface
{
    public function __construct(private readonly Repayment $model) {}

    public function all(): Collection
    {
        return $this->model->with(['farmer', 'operator'])->get();
    }

    public function find(int $id): ?Repayment
    {
        return $this->model->find($id);
    }

    public function create(array $data): Repayment
    {
        return $this->model->create($data);
    }

    public function findByFarmer(int $farmerId): Collection
    {
        return $this->model->where('farmer_id', $farmerId)
            ->with(['operator', 'debts'])
            ->get();
    }

    public function findWithDebts(int $id): ?Repayment
    {
        return $this->model->with(['farmer', 'operator', 'debts'])->find($id);
    }

    public function attachDebt(int $repaymentId, int $debtId, int $amountApplied): void
    {
        $repayment = $this->model->findOrFail($repaymentId);
        $repayment->debts()->attach($debtId, ['amount_applied_fcfa' => $amountApplied]);
    }
}
