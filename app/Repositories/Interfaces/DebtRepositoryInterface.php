<?php

namespace App\Repositories\Interfaces;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Collection;

interface DebtRepositoryInterface
{
    public function find(int $id): ?Debt;
    public function create(array $data): Debt;
    public function update(int $id, array $data): Debt;
    public function findByFarmer(int $farmerId): Collection;
    public function getOpenDebtsByFarmerFifo(int $farmerId): Collection;
    public function getTotalRemainingByFarmer(int $farmerId): int;
}
