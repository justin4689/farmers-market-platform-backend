<?php

namespace App\Services;

use App\Models\Repayment;
use App\Models\User;
use App\Repositories\Interfaces\DebtRepositoryInterface;
use App\Repositories\Interfaces\RepaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RepaymentService
{
    public function __construct(
        private readonly RepaymentRepositoryInterface $repaymentRepository,
        private readonly DebtRepositoryInterface      $debtRepository,
    ) {}

    public function repay(array $data, User $operator): Repayment
    {
        return DB::transaction(function () use ($data, $operator) {

            $totalCredited = (int) round($data['kg_received'] * $data['commodity_rate_fcfa']);

            // Load farmer's unpaid debts oldest-first (FIFO)
            $debts = $this->debtRepository->getOpenDebtsByFarmerFifo($data['farmer_id']);

            $remainingToApply = $totalCredited;
            $debtAmounts      = []; // [debt_id => amount_applied_fcfa]

            foreach ($debts as $debt) {
                if ($remainingToApply <= 0) {
                    break;
                }

                if ($remainingToApply >= $debt->remaining_fcfa) {
                    // This debt is fully settled
                    $applied             = $debt->remaining_fcfa;
                    $debt->remaining_fcfa = 0;
                    $debt->status        = 'paid';
                } else {
                    // Partial payment — debt stays open with reduced balance
                    $applied             = $remainingToApply;
                    $debt->remaining_fcfa -= $applied;
                    $debt->status        = 'partial';
                }

                $this->debtRepository->save($debt);

                $debtAmounts[$debt->id] = $applied;
                $remainingToApply      -= $applied;
            }
            // Any surplus (remainingToApply > 0) is silently ignored per spec

            // Persist the repayment record
            $repayment = $this->repaymentRepository->create([
                'farmer_id'           => $data['farmer_id'],
                'operator_id'         => $operator->id,
                'kg_received'         => $data['kg_received'],
                'commodity_rate_fcfa' => $data['commodity_rate_fcfa'],
                'total_fcfa_credited' => $totalCredited,
            ]);

            // Attach all affected debts in one operation
            if (!empty($debtAmounts)) {
                $this->repaymentRepository->attachDebts($repayment->id, $debtAmounts);
            }

            // Return with debts relationship loaded (includes pivot amount_applied_fcfa)
            return $this->repaymentRepository->findWithDebts($repayment->id);
        });
    }
}
