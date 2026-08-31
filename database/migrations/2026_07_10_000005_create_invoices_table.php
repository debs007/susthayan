<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data record only for now - generated at fulfillment, matching
     * "Invoice generated (GST)" in the SRS flow. Rendering an actual PDF is
     * a separate pass (there's a whole skill for that; not rushing it here).
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->restrictOnDelete();
            // Set in a follow-up update once the row's own auto-increment id is
            // known (see InvoiceService) - keeps numbering collision-free without
            // a separate counter table. Nullable is deliberate: a unique index
            // allows any number of NULLs, so two concurrent invoice creations
            // can't collide on a shared placeholder string the way they would
            // with e.g. invoice_number = 'PENDING' for both.
            $table->string('invoice_number')->nullable()->unique();
            $table->decimal('subtotal_amount', 10, 2);
            $table->decimal('gst_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
