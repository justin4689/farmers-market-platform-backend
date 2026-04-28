<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Repayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RepaymentSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('email', 'operator1@xpertbot.com')->firstOrFail();
        $now      = Carbon::now();

        // ─────────────────────────────────────────────────────────────
        // Repayment #1 – Farmer 1 (CI-2024-001)
        //   Debt: 26 400 FCFA open
        //   Payment: 20 kg × 1 000 FCFA/kg = 20 000 FCFA
        //   Result: remaining = 6 400 FCFA, status = partial
        // ─────────────────────────────────────────────────────────────
        $farmer1 = Farmer::where('identifier', 'CI-2024-001')->firstOrFail();

        $debt1 = Debt::where('farmer_id', $farmer1->id)
            ->where('status', 'open')
            ->orderBy('created_at')
            ->firstOrFail();

        $kgR1   = 20.000;
        $rateR1 = 1000;
        $totalR1 = (int) round($kgR1 * $rateR1); // 20 000

        $repayment1 = Repayment::create([
            'farmer_id'           => $farmer1->id,
            'operator_id'         => $operator->id,
            'kg_received'         => $kgR1,
            'commodity_rate_fcfa' => $rateR1,
            'total_fcfa_credited' => $totalR1,
            'created_at'          => $now->copy()->subDays(7),
            'updated_at'          => $now->copy()->subDays(7),
        ]);

        // Apply 20 000 to the 26 400 debt → partial
        $applied1 = $totalR1; // 20 000 < 26 400
        $debt1->remaining_fcfa -= $applied1;
        $debt1->status = 'partial';
        $debt1->save();

        DB::table('repayment_debt')->insert([
            'repayment_id'       => $repayment1->id,
            'debt_id'            => $debt1->id,
            'amount_applied_fcfa' => $applied1,
            'created_at'         => $now->copy()->subDays(7),
            'updated_at'         => $now->copy()->subDays(7),
        ]);

        // ─────────────────────────────────────────────────────────────
        // Repayment #2 – Farmer 2 (CI-2024-002)
        //   Debt: 34 500 FCFA open
        //   Payment: 40 kg × 1 000 FCFA/kg = 40 000 FCFA
        //   Result: debt fully paid (surplus 5 500 ignored)
        // ─────────────────────────────────────────────────────────────
        $farmer2 = Farmer::where('identifier', 'CI-2024-002')->firstOrFail();

        $debt2 = Debt::where('farmer_id', $farmer2->id)
            ->where('status', 'open')
            ->orderBy('created_at')
            ->firstOrFail();

        $kgR2    = 40.000;
        $rateR2  = 1000;
        $totalR2 = (int) round($kgR2 * $rateR2); // 40 000

        $repayment2 = Repayment::create([
            'farmer_id'           => $farmer2->id,
            'operator_id'         => $operator->id,
            'kg_received'         => $kgR2,
            'commodity_rate_fcfa' => $rateR2,
            'total_fcfa_credited' => $totalR2,
            'created_at'          => $now->copy()->subDays(3),
            'updated_at'          => $now->copy()->subDays(3),
        ]);

        // Apply full remaining (34 500) – surplus 5 500 is ignored per spec
        $applied2 = $debt2->remaining_fcfa; // 34 500
        $debt2->remaining_fcfa = 0;
        $debt2->status = 'paid';
        $debt2->save();

        DB::table('repayment_debt')->insert([
            'repayment_id'        => $repayment2->id,
            'debt_id'             => $debt2->id,
            'amount_applied_fcfa' => $applied2,
            'created_at'          => $now->copy()->subDays(3),
            'updated_at'          => $now->copy()->subDays(3),
        ]);
    }
}
