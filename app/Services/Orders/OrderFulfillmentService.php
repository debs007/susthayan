<?php

namespace App\Services\Orders;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Services\Invoicing\InvoiceService;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The single place an order's status is allowed to move - originally lived
 * directly in Api\Franchise\OrderFulfillmentController, pulled out once
 * Delivery Agent actions (picked-up -> out_for_delivery, delivered ->
 * delivered) needed to drive the exact same transitions, FEFO deduction,
 * and invoice generation rather than a second copy of that logic.
 */
class OrderFulfillmentService
{
    /** Which statuses a given transition is allowed to start from. */
    private const array ALLOWED_FROM = [
        'preparing' => ['confirmed'],
        'ready_for_dispatch' => ['preparing'],
        'out_for_delivery' => ['ready_for_dispatch'],
        'delivered' => ['out_for_delivery', 'ready_for_dispatch'],
        'picked_up' => ['ready_for_dispatch'],
    ];

    public function __construct(
        private readonly StockService $stock,
        private readonly InvoiceService $invoices,
    ) {}

    /** What this order's status could validly move to next - lets the UI only ever offer transitions that would actually succeed. */
    public function validNextStatuses(Order $order): array
    {
        return array_keys(array_filter(
            self::ALLOWED_FROM,
            fn (array $from) => in_array($order->status->value, $from, true)
        ));
    }

    /**
     * @throws RuntimeException if the transition isn't valid from the order's current status
     * @throws \App\Exceptions\InsufficientStockException if stock moved between reservation and fulfillment
     */
    public function transition(Order $order, string $newStatus): Order
    {
        $allowedFrom = self::ALLOWED_FROM[$newStatus] ?? [];

        if (! in_array($order->status->value, $allowedFrom, true)) {
            throw new RuntimeException("Can't move from \"{$order->status->value}\" to \"{$newStatus}\".");
        }

        DB::transaction(function () use ($order, $newStatus) {
            // Deduct the moment items physically leave the franchise, not
            // whenever the order is later marked complete - out_for_delivery
            // (or picked_up, which has no separate "en route" stage) is
            // that moment. delivered only deducts too when this order
            // skipped out_for_delivery entirely (ALLOWED_FROM permits
            // ready_for_dispatch -> delivered directly) - otherwise it
            // already happened at out_for_delivery and doing it again here
            // would double-deduct. Deliberately separate from
            // $isFulfilling below - stock leaves the franchise earlier
            // than revenue is recognised, and only the former is what
            // changed here.
            $isDispatching = in_array($newStatus, ['out_for_delivery', 'picked_up'], true)
                || ($newStatus === 'delivered' && $order->status->value === 'ready_for_dispatch');

            if ($isDispatching) {
                // The real deduction: FEFO batch selection + order_item_batches trail.
                $this->stock->fulfillOrder($order);
            }

            $isFulfilling = in_array($newStatus, ['delivered', 'picked_up'], true);

            $timestamps = match ($newStatus) {
                'preparing' => ['prepared_at' => now()],
                'out_for_delivery' => ['out_for_delivery_at' => now()],
                // Reused for both terminal states rather than a separate
                // picked_up_at column for what's functionally the same
                // "fulfillment completed" moment.
                'delivered', 'picked_up' => ['delivered_at' => now()],
                default => [],
            };

            $order->update([...$timestamps, 'status' => $newStatus]);

            if ($isFulfilling) {
                // Revenue recognised + GST invoice, matching the SRS's own
                // ordering: fulfillment is what turns payment into revenue.
                $this->invoices->generateFor($order->fresh());
            }
        });

        $order = $order->fresh(['items.product', 'items.batches']);

        OrderStatusUpdated::dispatch($order);

        return $order;
    }
}
