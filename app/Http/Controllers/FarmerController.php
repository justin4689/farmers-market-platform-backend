<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFarmerRequest;
use App\Http\Resources\FarmerResource;
use App\Services\FarmerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    public function __construct(private readonly FarmerService $farmerService) {}

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return $this->error('Search query parameter "q" is required.', [], 422);
        }

        $farmers = $this->farmerService->search($query);

        return $this->success(FarmerResource::collection($farmers), 'Search results retrieved.');
    }

    public function show(int $id): JsonResponse
    {
        $farmer = $this->farmerService->getProfile($id);

        if (!$farmer) {
            return $this->error('Farmer not found.', [], 404);
        }

        return $this->success(new FarmerResource($farmer), 'Farmer profile retrieved.');
    }

    public function store(StoreFarmerRequest $request): JsonResponse
    {
        $farmer = $this->farmerService->store($request->validated());

        return $this->success(new FarmerResource($farmer), 'Farmer created successfully.', 201);
    }
}
