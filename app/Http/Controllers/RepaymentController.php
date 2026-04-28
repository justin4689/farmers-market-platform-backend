<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepaymentRequest;
use App\Http\Resources\RepaymentResource;
use App\Services\RepaymentService;
use Illuminate\Http\JsonResponse;

class RepaymentController extends Controller
{
    public function __construct(private readonly RepaymentService $repaymentService) {}

    public function store(StoreRepaymentRequest $request): JsonResponse
    {
        $repayment = $this->repaymentService->repay(
            $request->validated(),
            $request->user()
        );

        return $this->success(
            new RepaymentResource($repayment),
            'Repayment recorded successfully.',
            201
        );
    }
}
