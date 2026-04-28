<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(private readonly ProductRepositoryInterface $productRepository) {}

    public function getAll(): Collection
    {
        return $this->productRepository->withCategory();
    }

    public function getById(int $id): ?Product
    {
        return $this->productRepository->findWithCategory($id);
    }

    public function store(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(int $id, array $data): Product
    {
        return $this->productRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
