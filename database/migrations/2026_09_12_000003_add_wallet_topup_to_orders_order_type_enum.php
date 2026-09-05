<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY order_type ENUM('product', 'lab_test', 'appointment', 'wallet_topup') NOT NULL DEFAULT 'product'");
    }

    public function down(): void
    {
        // Reverting would fail if any 'wallet_topup' rows already exist by
        // this point - deliberately left for a human to handle rather
        // than silently deleting/reassigning that data on rollback.
        DB::statement("ALTER TABLE orders MODIFY order_type ENUM('product', 'lab_test', 'appointment') NOT NULL DEFAULT 'product'");
    }
};
