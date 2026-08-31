<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two changes to support walk-in POS sales:
     *   - user_id becomes nullable - a walk-in customer doesn't need an
     *     account (mirrors real pharmacy retail: most POS sales are
     *     anonymous cash transactions). walk_in_customer_name/phone are a
     *     lightweight record for cases needing a name on file (Schedule
     *     H1-style traceability) without forcing full registration.
     *   - fulfillment_type gains a 'pos' value alongside delivery/pickup.
     *
     * Raw ALTER TABLE rather than Laravel's ->change() - modifying an
     * existing column's nullability/enum values via ->change() needs
     * doctrine/dbal, which isn't part of this stack.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('walk_in_customer_name')->nullable()->after('user_id');
            $table->string('walk_in_customer_phone', 15)->nullable()->after('walk_in_customer_name');
            // POS-specific compliance record: which pharmacist verified a
            // prescription drug in person, and what they noted (doctor/reg
            // number) - see PosSaleService::guardPrescriptionRequirement().
            $table->string('prescription_note')->nullable()->after('requires_prescription');
        });

        DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement("ALTER TABLE orders MODIFY fulfillment_type ENUM('delivery', 'pickup', 'pos') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY fulfillment_type ENUM('delivery', 'pickup') NOT NULL");
        DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['walk_in_customer_name', 'walk_in_customer_phone', 'prescription_note']);
        });
    }
};
