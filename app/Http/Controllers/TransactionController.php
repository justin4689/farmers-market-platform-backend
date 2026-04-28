<?php

namespace App\Http\Controllers;

use App\Exceptions\CreditLimitExceededException;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService) {}

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->checkout(
                $request->validated(),
                $request->user()
            );

            return $this->success(
                new TransactionResource($transaction),
                'Transaction completed successfully.',
                201
            );
        } catch (CreditLimitExceededException $e) {
            return $this->error($e->getMessage(), [], 422);
        }
    }
}
