<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository) {}

    /**
     * Fetch all categories as a recursive tree using a single flat query.
     * Each root node has its children set recursively via setRelation().
     */
    public function getNestedCategories(): Collection
    {
        $all = $this->categoryRepository->all();
        return $this->buildTree($all, null);
    }

    public function store(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function update(int $id, array $data): Category
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    private function buildTree(Collection $all, ?int $parentId): Collection
    {
        return $all
            ->where('parent_id', $parentId)
            ->values()
            ->each(function (Category $category) use ($all): void {
                $category->setRelation('children', $this->buildTree($all, $category->id));
            });
    }
}
