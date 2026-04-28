<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebtResource;
use App\Repositories\Interfaces\DebtRepositoryInterface;
use App\Repositories\Interfaces\FarmerRepositoryInterface;
use Illuminate\Http\JsonResponse;

class DebtController extends Controller
{
    public function __construct(
        private readonly DebtRepositoryInterface   $debtRepository,
        private readonly FarmerRepositoryInterface $farmerRepository,
    ) {}

    public function index(int $id): JsonResponse
    {
        if (!$this->farmerRepository->find($id)) {
            return $this->error('Farmer not found.', [], 404);
        }

        $debts = $this->debtRepository->getOpenDebtsByFarmerFifo($id);

        return $this->success(
            DebtResource::collection($debts),
            'Open debts retrieved successfully.'
        );
    }
}
