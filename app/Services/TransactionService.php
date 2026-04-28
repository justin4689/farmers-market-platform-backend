<?php

namespace App\Services;

use App\Exceptions\CreditLimitExceededException;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\Interfaces\DebtRepositoryInterface;
use App\Repositories\Interfaces\FarmerRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\TransactionItemRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private readonly TransactionRepositoryInterface     $transactionRepository,
        private readonly TransactionItemRepositoryInterface $transactionItemRepository,
        private readonly DebtRepositoryInterface            $debtRepository,
        private readonly FarmerRepositoryInterface          $farmerRepository,
        private readonly ProductRepositoryInterface         $productRepository,
    ) {}

    public function checkout(array $data, User $operator): Transaction
    {
        return DB::transaction(function () use ($data, $operator) {

            // 1. Batch-load all products in one query, keyed by id
            $productIds = array_column($data['items'], 'product_id');
            $products   = $this->productRepository->findByIds($productIds)->keyBy('id');

            // 2. Build item records and calculate subtotal
            $subtotal  = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $unitPrice = $products->get($item['product_id'])->price_fcfa;
                $subtotal += $item['quantity'] * $unitPrice;

                $itemsData[] = [
                    'product_id'      => $item['product_id'],
                    'quantity'        => $item['quantity'],
                    'unit_price_fcfa' => $unitPrice,
                ];
            }

            // 3. Compute totals based on payment method
            if ($data['payment_method'] === 'credit') {
                $interestRate   = (float) $data['interest_rate'];
                $interestAmount = (int) round($subtotal * $interestRate);
                $total          = $subtotal + $interestAmount;
            } else {
                $interestRate   = null;
                $interestAmount = null;
                $total          = $subtotal;
            }

            // 4. Credit limit check — only for credit purchases
            if ($data['payment_method'] === 'credit') {
                $farmer      = $this->farmerRepository->find($data['farmer_id']);
                $currentDebt = $this->debtRepository->getTotalRemainingByFarmer($data['farmer_id']);
                $available   = $farmer->credit_limit_fcfa - $currentDebt;

                if (($currentDebt + $total) > $farmer->credit_limit_fcfa) {
                    throw new CreditLimitExceededException(max(0, $available));
                }
            }

            // 5. Persist transaction
            $transaction = $this->transactionRepository->create([
                'farmer_id'            => $data['farmer_id'],
                'operator_id'          => $operator->id,
                'total_fcfa'           => $total,
                'payment_method'       => $data['payment_method'],
                'interest_rate'        => $interestRate,
                'interest_amount_fcfa' => $interestAmount,
            ]);

            // 6. Persist line items
            $this->transactionItemRepository->createMany($transaction->id, $itemsData);

            // 7. Create debt record for credit purchases
            if ($data['payment_method'] === 'credit') {
                $this->debtRepository->create([
                    'transaction_id' => $transaction->id,
                    'farmer_id'      => $data['farmer_id'],
                    'amount_fcfa'    => $total,
                    'remaining_fcfa' => $total,
                    'status'         => 'open',
                ]);
            }

            // 8. Return fully-loaded transaction for the response
            return $this->transactionRepository->findWithItems($transaction->id);
        });
    }
}
