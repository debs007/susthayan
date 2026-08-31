<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franchise_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->restrictOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gross_sales', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0);
            $table->enum('status', ['draft', 'generated', 'paid'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->string('payout_reference')->nullable(); // e.g. RazorpayX payout id
            $table->timestamps();

            $table->index(['franchise_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_settlements');
    }
};
