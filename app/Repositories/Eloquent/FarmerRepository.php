<?php

namespace App\Repositories\Eloquent;

use App\Models\Farmer;
use App\Repositories\Interfaces\FarmerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FarmerRepository implements FarmerRepositoryInterface
{
    public function __construct(private readonly Farmer $model) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Farmer
    {
        return $this->model->find($id);
    }

    public function findByIdentifier(string $identifier): ?Farmer
    {
        return $this->model->where('identifier', $identifier)->first();
    }

    public function create(array $data): Farmer
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Farmer
    {
        $farmer = $this->model->findOrFail($id);
        $farmer->update($data);
        return $farmer->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    public function getTotalOpenDebt(int $farmerId): int
    {
        return $this->model->findOrFail($farmerId)
            ->debts()
            ->whereIn('status', ['open', 'partial'])
            ->sum('remaining_fcfa');
    }

    public function getOpenDebtsOrderedByDate(int $farmerId): Collection
    {
        return $this->model->findOrFail($farmerId)
            ->debts()
            ->whereIn('status', ['open', 'partial'])
            ->orderBy('created_at')
            ->get();
    }
}
