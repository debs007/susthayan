<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY type ENUM('order', 'health', 'offer', 'alert', 'admin') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY type ENUM('order', 'health', 'offer', 'alert') NOT NULL");
    }
};
