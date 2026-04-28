<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    public function index(): JsonResponse
    {
        $tree = $this->categoryService->getNestedCategories();

        return $this->success(CategoryResource::collection($tree), 'Categories retrieved successfully.');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->store($request->validated());

        return $this->success(new CategoryResource($category), 'Category created successfully.', 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->update($id, $request->validated());

        return $this->success(new CategoryResource($category), 'Category updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->delete($id);

        return $this->success([], 'Category deleted successfully.');
    }
}
