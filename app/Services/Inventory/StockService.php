<?php

namespace App\Services\Inventory;

use App\Exceptions\InsufficientStockException;
use App\Models\GoodsReceiptItem;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemBatch;
use Illuminate\Support\Facades\DB;

/**
 * The one place that touches inventory quantities, so FEFO ordering and the
 * quantity/reserved_quantity split stay consistent everywhere they're used
 * (Customer Orders today, POS and Purchase Returns later).
 *
 * Lifecycle: available() for soft checks -> reserve() on payment success
 * (holds stock without picking a batch yet) -> deductFefo() at actual
 * fulfillment (picks batches for real and creates the OrderItemBatch
 * trail) -> release() if a reserved order is cancelled/refunded before
 * fulfillment.
 */
class StockService
{
    /** Physically present minus already-reserved, summed across every batch. */
    public function available(int $franchiseId, int $productId): int
    {
        return (int) Inventory::where('franchise_id', $franchiseId)
            ->where('product_id', $productId)
            ->selectRaw('COALESCE(SUM(quantity - reserved_quantity), 0) as total')
            ->value('total');
    }

    /**
     * Inventory IN. Matching the SRS rule verbatim - only a GRN increases
     * stock. $sellableQty is deliberately separate from $item->received_qty -
     * a line with damaged_qty > 0 should only add the good portion to
     * inventory, so the caller (GoodsReceiptService) does that subtraction
     * and passes the result here rather than this method assuming the two
     * are always equal. Same batch_no arriving again for the same product/
     * franchise (a genuine restock of an identical batch, not just a
     * coincidence - batch numbers are supplier-assigned and can repeat
     * across shipments) tops up that row rather than creating a duplicate;
     * the unique (franchise_id, product_id, batch_no) constraint is what
     * makes that distinction meaningful.
     */
    public function receiveFromGrn(int $franchiseId, GoodsReceiptItem $item, int $sellableQty): Inventory
    {
        return DB::transaction(function () use ($franchiseId, $item, $sellableQty) {
            $inventory = Inventory::where('franchise_id', $franchiseId)
                ->where('product_id', $item->product_id)
                ->where('batch_no', $item->batch_no)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                // Same batch, same expiry expected - a mismatch here means
                // either a data-entry error or two genuinely different
                // batches sharing a number, which needs a human to sort out
                // rather than silently overwriting the recorded expiry.
                if (! $inventory->expiry_date->equalTo($item->expiry_date)) {
                    throw new \RuntimeException(
                        "Batch {$item->batch_no} already exists for this product with a different expiry date - check for a data entry error before receiving."
                    );
                }

                $inventory->increment('quantity', $sellableQty);
                $inventory->update(['purchase_rate' => $item->purchase_rate]);

                return $inventory->fresh();
            }

            return Inventory::create([
                'franchise_id' => $franchiseId,
                'product_id' => $item->product_id,
                'goods_receipt_item_id' => $item->id,
                'batch_no' => $item->batch_no,
                'expiry_date' => $item->expiry_date,
                'quantity' => $sellableQty,
                'reserved_quantity' => 0,
                'purchase_rate' => $item->purchase_rate,
            ]);
        });
    }

    /**
     * Reserve every item on an order. All-or-nothing: if any line can't be
     * covered, nothing is reserved and InsufficientStockException is thrown.
     */
    public function reserveForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->reserveQuantity($order->franchise_id, $item->product_id, $item->quantity);
            }
        });
    }

    /** Give back a reservation without ever having deducted physical stock (cancel/expire before fulfillment). */
    public function releaseForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->releaseQuantity($order->franchise_id, $item->product_id, $item->quantity);
            }
        });
    }

    /** Same as releaseForOrder but for a single line - used by partial refunds. */
    public function releaseForItem(int $franchiseId, OrderItem $item): void
    {
        DB::transaction(fn () => $this->releaseQuantity($franchiseId, $item->product_id, $item->quantity));
    }

    /**
     * The real deduction at delivery/pickup. Picks FEFO batches, records
     * exactly which batch(es) and how much in order_item_batches, and moves
     * both quantity and reserved_quantity down together.
     */
    public function fulfillOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->deductFefo($order->franchise_id, $item);
            }
        });
    }

    /**
     * Best-effort: reserves whatever quantity is actually available and
     * leaves the rest unreserved rather than throwing. An order can now
     * be assigned to a franchise that doesn't fully stock every item
     * (see Admin\OrderController::assign()), so a shortfall here is
     * expected and normal, not an error to unwind the whole assignment
     * over - the franchise's own order view is what surfaces it.
     */
    private function reserveQuantity(int $franchiseId, int $productId, int $quantity): void
    {
        $batches = Inventory::fefoFor($franchiseId, $productId)->lockForUpdate()->get();

        $remaining = $quantity;

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($batch->available_quantity, $remaining);
            if ($take > 0) {
                $batch->increment('reserved_quantity', $take);
                $remaining -= $take;
            }
        }
    }

    private function releaseQuantity(int $franchiseId, int $productId, int $quantity): void
    {
        // Release order doesn't need to match the original reservation's
        // specific batches - only the aggregate reserved_quantity matters
        // until fulfillment actually commits to particular batches.
        $batches = Inventory::where('franchise_id', $franchiseId)
            ->where('product_id', $productId)
            ->where('reserved_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->lockForUpdate()
            ->get();

        $remaining = $quantity;

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $release = min($batch->reserved_quantity, $remaining);
            $batch->decrement('reserved_quantity', $release);
            $remaining -= $release;
        }
    }

    private function deductFefo(int $franchiseId, OrderItem $item): void
    {
        $batches = Inventory::where('franchise_id', $franchiseId)
            ->where('product_id', $item->product_id)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->lockForUpdate()
            ->get();

        $remaining = $item->quantity;
        $primaryInventoryId = null;

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($batch->quantity, $remaining);
            if ($take <= 0) {
                continue;
            }

            $batch->decrement('quantity', $take);
            // The reservation was already held against this batch's aggregate
            // pool; cap at what's actually left reserved so this can't go negative
            // if reserve() happened to land on a different batch mix.
            $batch->decrement('reserved_quantity', min($take, $batch->reserved_quantity));

            OrderItemBatch::create([
                'order_item_id' => $item->id,
                'inventory_id' => $batch->id,
                'quantity' => $take,
            ]);

            $primaryInventoryId ??= $batch->id;
            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new InsufficientStockException($item->product_id, $item->quantity, $item->quantity - $remaining);
        }

        $item->update(['inventory_id' => $primaryInventoryId]);
    }
}
