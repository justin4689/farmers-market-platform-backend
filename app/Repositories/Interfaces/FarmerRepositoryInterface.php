<?php

namespace App\Repositories\Interfaces;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Collection;

interface FarmerRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Farmer;
    public function findByIdentifier(string $identifier): ?Farmer;
    public function create(array $data): Farmer;
    public function update(int $id, array $data): Farmer;
    public function delete(int $id): bool;
    public function getTotalOpenDebt(int $farmerId): int;
    public function getOpenDebtsOrderedByDate(int $farmerId): Collection;
}
