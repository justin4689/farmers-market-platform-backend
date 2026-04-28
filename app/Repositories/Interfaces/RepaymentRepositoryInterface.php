<?php

namespace App\Repositories\Interfaces;

use App\Models\Repayment;
use Illuminate\Database\Eloquent\Collection;

interface RepaymentRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Repayment;
    public function create(array $data): Repayment;
    public function findByFarmer(int $farmerId): Collection;
    public function findWithDebts(int $id): ?Repayment;
    public function attachDebt(int $repaymentId, int $debtId, int $amountApplied): void;
}
