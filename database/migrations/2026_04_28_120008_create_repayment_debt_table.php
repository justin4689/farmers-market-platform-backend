<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repayment_debt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repayment_id')->constrained('repayments')->cascadeOnDelete();
            $table->foreignId('debt_id')->constrained('debts')->cascadeOnDelete();
            $table->unsignedInteger('amount_applied_fcfa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayment_debt');
    }
};
