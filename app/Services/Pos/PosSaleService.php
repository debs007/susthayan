<?php

namespace App\Services\Pos;

use App\Enums\FulfillmentType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\NewOrderPlaced;
use App\Exceptions\InsufficientStockException;
use App\Models\CustomerPayment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Invoicing\InvoiceService;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * "POS sales are real-time final sales" - the SRS's own words. Unlike
 * Customer Orders (soft-check at checkout, reserve on payment, deduct on
 * delivery, three separate moments), a POS sale does the stock check,
 * FEFO deduction, payment, and invoice all in a single transaction. There
 * is no reservation phase to skip - StockService::fulfillOrder() is called
 * directly.
 */
class PosSaleService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly InvoiceService $invoices,
    ) {}

    /**
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     */
    public function sell(
        User $staff,
        array $items,
        string $paymentMode,
        ?int $customerId,
        ?string $walkInName,
        ?string $walkInPhone,
        ?string $prescriptionNote,
    ): Order {
        if (empty($items)) {
            throw new RuntimeException('A sale needs at least one item.');
        }

        $franchiseId = $staff->franchise_id;

        return DB::transaction(function () use (
            $staff, $items, $paymentMode, $customerId, $walkInName, $walkInPhone, $prescriptionNote, $franchiseId
        ) {
            $lines = $this->priceAndCheckLines($franchiseId, $items);
            $requiresRx = collect($lines)->contains('prescription_required', true);

            if ($requiresRx) {
                $this->guardPrescriptionRequirement($staff, $prescriptionNote);
            }

            $order = Order::create([
                'user_id' => $customerId,
                'franchise_id' => $franchiseId,
                'fulfillment_type' => FulfillmentType::Pos,
                // Immediate terminal state - see the comment on
                // Order::isRevenueRecognised() for why PickedUp is reused
                // here rather than adding a dedicated POS status.
                'status' => OrderStatus::PickedUp,
                'requires_prescription' => $requiresRx,
                'prescription_note' => $requiresRx ? $prescriptionNote : null,
                'walk_in_customer_name' => $customerId ? null : $walkInName,
                'walk_in_customer_phone' => $customerId ? null : $walkInPhone,
                'confirmed_at' => now(),
                'delivered_at' => now(),
            ]);

            $subtotal = 0.0;
            $tax = 0.0;

            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'tax_percentage' => $line['tax_percentage'],
                    'total_price' => $line['line_total'],
                ]);

                $subtotal += $line['line_subtotal'];
                $tax += $line['line_tax'];
            }

            $order->update([
                'subtotal_amount' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $subtotal + $tax,
            ]);

            // No reservation step - straight to the real FEFO deduction.
            $this->stock->fulfillOrder($order->fresh('items'));

            CustomerPayment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'payment_mode' => $paymentMode,
                'gateway' => null, // cash/card-machine/UPI-at-counter - no online gateway involved
                'status' => PaymentStatus::Success,
            ]);

            $this->invoices->generateFor($order->fresh());

            $order = $order->fresh(['items.product', 'items.batches', 'payments']);

            NewOrderPlaced::dispatch($order);

            return $order;
        });
    }

    /**
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     * @return array<int, array{product_id: int, quantity: int, unit_price: float, tax_percentage: float, line_subtotal: float, line_tax: float, line_total: float, prescription_required: bool}>
     */
    private function priceAndCheckLines(int $franchiseId, array $items): array
    {
        $lines = [];

        foreach ($items as $item) {
            $product = Product::with('prices')->find($item['product_id']);

            if (! $product) {
                throw new RuntimeException("Product #{$item['product_id']} doesn't exist.");
            }

            $available = $this->stock->available($franchiseId, $product->id);

            if ($available < $item['quantity']) {
                throw new InsufficientStockException($product->id, $item['quantity'], $available);
            }

            $price = $product->priceFor($franchiseId);

            if (! $price) {
                throw new RuntimeException("No price configured for product #{$product->id}.");
            }

            $lineSubtotal = (float) $price->selling_price * $item['quantity'];
            $lineTax = round($lineSubtotal * (float) $price->tax_percentage / 100, 2);

            $lines[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $price->selling_price,
                'tax_percentage' => $price->tax_percentage,
                'line_subtotal' => $lineSubtotal,
                'line_tax' => $lineTax,
                'line_total' => $lineSubtotal + $lineTax,
                'prescription_required' => $product->prescription_required,
            ];
        }

        return $lines;
    }

    /**
     * Unlike the online flow (upload -> separate pharmacist review, async),
     * for POS the person ringing up the sale is standing in front of the
     * physical prescription right now - so the gate is (a) only a
     * Pharmacist can process this specific sale, and (b) they leave a note
     * for the audit trail, rather than a separate approval step.
     */
    private function guardPrescriptionRequirement(User $staff, ?string $prescriptionNote): void
    {
        if (! $staff->hasRole('Pharmacist')) {
            throw new RuntimeException(
                'This sale includes a prescription medicine - only a pharmacist can process it.'
            );
        }

        if (! $prescriptionNote) {
            throw new RuntimeException(
                'Add a prescription note (doctor/registration reference) before completing this sale.'
            );
        }
    }
}
