<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('email', 'operator1@xpertbot.com')->firstOrFail();

        $farmer1 = Farmer::where('identifier', 'CI-2024-001')->firstOrFail(); // credit limit 100 000
        $farmer2 = Farmer::where('identifier', 'CI-2024-002')->firstOrFail(); // credit limit 150 000
        $farmer3 = Farmer::where('identifier', 'CI-2024-003')->firstOrFail(); // credit limit  50 000
        $farmer4 = Farmer::where('identifier', 'CI-2024-004')->firstOrFail(); // credit limit 200 000

        $roundup   = Product::where('name', 'Roundup 1L')->firstOrFail();               // 8 500
        $npk151515 = Product::where('name', 'NPK 15-15-15 25kg')->firstOrFail();         // 18 000
        $maïsIkenne = Product::where('name', 'Semence Maïs IKENNE 5kg')->firstOrFail();  // 6 500
        $uree      = Product::where('name', "Urée 46% 25kg")->firstOrFail();             // 16 500
        $lambda    = Product::where('name', 'Lambda-Cyhalothrine 50ml')->firstOrFail();  // 3 500
        $compost   = Product::where('name', 'Compost Premium 50kg')->firstOrFail();      // 9 500
        $ridomil   = Product::where('name', 'Ridomil Gold 1kg')->firstOrFail();          // 12 000
        $cacao82   = Product::where('name', 'Semence Cacao T82 100g')->firstOrFail();    // 15 000
        $npk02319  = Product::where('name', 'NPK 0-23-19 25kg')->firstOrFail();          // 20 000

        $now = Carbon::now();

        // ─────────────────────────────────────────────────────────────
        // CASH TRANSACTIONS (no debt created)
        // ─────────────────────────────────────────────────────────────

        // Cash #1 – Farmer 1: Roundup ×2 + NPK 15-15-15 ×1 = 35 000 FCFA
        $this->makeCashTransaction(
            $farmer1->id, $operator->id,
            [
                ['product' => $roundup,   'qty' => 2],  // 17 000
                ['product' => $npk151515, 'qty' => 1],  // 18 000
            ],
            $now->copy()->subDays(30)
        );

        // Cash #2 – Farmer 2: Maïs IKENNE ×3 + Urée ×1 = 36 000 FCFA
        $this->makeCashTransaction(
            $farmer2->id, $operator->id,
            [
                ['product' => $maïsIkenne, 'qty' => 3], // 19 500
                ['product' => $uree,       'qty' => 1], // 16 500
            ],
            $now->copy()->subDays(25)
        );

        // Cash #3 – Farmer 3: Lambda ×2 + Compost ×1 = 16 500 FCFA
        $this->makeCashTransaction(
            $farmer3->id, $operator->id,
            [
                ['product' => $lambda,  'qty' => 2], // 7 000
                ['product' => $compost, 'qty' => 1], // 9 500
            ],
            $now->copy()->subDays(20)
        );

        // ─────────────────────────────────────────────────────────────
        // CREDIT TRANSACTIONS (debts created — FIFO seed order matters)
        // ─────────────────────────────────────────────────────────────

        // Credit #1 – Farmer 1: Ridomil ×2 = subtotal 24 000, 10% interest
        //   interest = 2 400 → total = 26 400 FCFA
        //   → Will be PARTIALLY repaid (20 kg × 1 000 = 20 000 applied)
        //   → Remaining after repayment: 6 400 FCFA, status = partial
        $this->makeCreditTransaction(
            $farmer1->id, $operator->id,
            [['product' => $ridomil, 'qty' => 2]], // 24 000
            0.10,
            $now->copy()->subDays(15)
        );

        // Credit #2 – Farmer 2: Cacao T82 ×2 = subtotal 30 000, 15% interest
        //   interest = 4 500 → total = 34 500 FCFA
        //   → Will be FULLY repaid (40 kg × 1 000 = 40 000, surplus ignored)
        //   → Remaining after repayment: 0 FCFA, status = paid
        $this->makeCreditTransaction(
            $farmer2->id, $operator->id,
            [['product' => $cacao82, 'qty' => 2]], // 30 000
            0.15,
            $now->copy()->subDays(10)
        );

        // Credit #3 – Farmer 4: NPK 0-23-19 ×3 = subtotal 60 000, 20% interest
        //   interest = 12 000 → total = 72 000 FCFA
        //   → No repayment seeded → status remains open
        $this->makeCreditTransaction(
            $farmer4->id, $operator->id,
            [['product' => $npk02319, 'qty' => 3]], // 60 000
            0.20,
            $now->copy()->subDays(5)
        );
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────

    private function makeCashTransaction(int $farmerId, int $operatorId, array $lines, Carbon $at): Transaction
    {
        $total = 0;
        $itemsData = [];
        foreach ($lines as $line) {
            $price  = $line['product']->price_fcfa;
            $total += $line['qty'] * $price;
            $itemsData[] = [
                'product_id'      => $line['product']->id,
                'quantity'        => $line['qty'],
                'unit_price_fcfa' => $price,
            ];
        }

        $tx = Transaction::create([
            'farmer_id'            => $farmerId,
            'operator_id'          => $operatorId,
            'total_fcfa'           => $total,
            'payment_method'       => 'cash',
            'interest_rate'        => null,
            'interest_amount_fcfa' => null,
            'created_at'           => $at,
            'updated_at'           => $at,
        ]);

        foreach ($itemsData as $item) {
            TransactionItem::create(array_merge($item, [
                'transaction_id' => $tx->id,
                'created_at'     => $at,
                'updated_at'     => $at,
            ]));
        }

        return $tx;
    }

    private function makeCreditTransaction(
        int    $farmerId,
        int    $operatorId,
        array  $lines,
        float  $interestRate,
        Carbon $at
    ): Transaction {
        $subtotal  = 0;
        $itemsData = [];

        foreach ($lines as $line) {
            $price     = $line['product']->price_fcfa;
            $subtotal += $line['qty'] * $price;
            $itemsData[] = [
                'product_id'      => $line['product']->id,
                'quantity'        => $line['qty'],
                'unit_price_fcfa' => $price,
            ];
        }

        $interestAmount = (int) round($subtotal * $interestRate);
        $total          = $subtotal + $interestAmount;

        $tx = Transaction::create([
            'farmer_id'            => $farmerId,
            'operator_id'          => $operatorId,
            'total_fcfa'           => $total,
            'payment_method'       => 'credit',
            'interest_rate'        => $interestRate,
            'interest_amount_fcfa' => $interestAmount,
            'created_at'           => $at,
            'updated_at'           => $at,
        ]);

        foreach ($itemsData as $item) {
            TransactionItem::create(array_merge($item, [
                'transaction_id' => $tx->id,
                'created_at'     => $at,
                'updated_at'     => $at,
            ]));
        }

        Debt::create([
            'transaction_id' => $tx->id,
            'farmer_id'      => $farmerId,
            'amount_fcfa'    => $total,
            'remaining_fcfa' => $total,
            'status'         => 'open',
            'created_at'     => $at,
            'updated_at'     => $at,
        ]);

        return $tx;
    }
}
