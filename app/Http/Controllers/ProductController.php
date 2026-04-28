<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        return $this->success(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return $this->error('Product not found.', [], 404);
        }

        return $this->success(new ProductResource($product), 'Product retrieved successfully.');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->store($request->validated());
        $product->load('category');

        return $this->success(new ProductResource($product), 'Product created successfully.', 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->update($id, $request->validated());
        $product->load('category');

        return $this->success(new ProductResource($product), 'Product updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return $this->error('Product not found.', [], 404);
        }

        $this->productService->delete($id);

        return $this->success([], 'Product deleted successfully.');
    }
}
