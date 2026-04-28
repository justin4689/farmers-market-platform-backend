<?php

namespace App\Services;

use App\Models\Farmer;
use App\Repositories\Interfaces\FarmerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FarmerService
{
    public function __construct(private readonly FarmerRepositoryInterface $farmerRepository) {}

    public function search(string $query): Collection
    {
        return $this->farmerRepository->search($query);
    }

    public function getProfile(int $id): ?Farmer
    {
        return $this->farmerRepository->findWithDebt($id);
    }

    public function store(array $data): Farmer
    {
        $farmer = $this->farmerRepository->create($data);

        // Fresh instance with debt aggregate initialised to 0 for consistent resource output
        $farmer->total_outstanding_debt = 0;

        return $farmer;
    }
}
