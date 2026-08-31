<?php

namespace App\Services\Purchasing;

use App\Enums\PurchaseOrderStatus;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GoodsReceiptService
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * @param  array<int, array{product_id: int, batch_no: string, expiry_date: string, received_qty: int, damaged_qty?: int, purchase_rate: float, mrp?: float}>  $items
     */
    public function receive(User $receiver, PurchaseOrder $po, array $items, string $receivedDate): GoodsReceipt
    {
        if (! $po->status->acceptsGoodsReceipt()) {
            throw new RuntimeException(
                "This purchase order is \"{$po->status->value}\" - only an approved PO can receive goods."
            );
        }

        if (empty($items)) {
            throw new RuntimeException('A goods receipt needs at least one line item.');
        }

        return DB::transaction(function () use ($receiver, $po, $items, $receivedDate) {
            $grn = GoodsReceipt::create([
                'purchase_order_id' => $po->id,
                'franchise_id' => $po->franchise_id,
                'received_by' => $receiver->id,
                'received_date' => $receivedDate,
            ]);

            foreach ($items as $itemData) {
                $damagedQty = $itemData['damaged_qty'] ?? 0;

                $grnItem = GoodsReceiptItem::create([
                    'goods_receipt_id' => $grn->id,
                    'product_id' => $itemData['product_id'],
                    'batch_no' => $itemData['batch_no'],
                    'expiry_date' => $itemData['expiry_date'],
                    'received_qty' => $itemData['received_qty'],
                    'damaged_qty' => $damagedQty,
                    'purchase_rate' => $itemData['purchase_rate'],
                    'mrp' => $itemData['mrp'] ?? null,
                ]);

                // Only the good portion becomes sellable stock - damage is
                // recorded on the GRN line for the record, but doesn't
                // inflate what's actually available to sell.
                $sellableQty = $grnItem->received_qty - $damagedQty;

                if ($sellableQty > 0) {
                    $this->stock->receiveFromGrn($po->franchise_id, $grnItem, $sellableQty);
                }
            }

            if ($this->isFullyReceived($po)) {
                $po->update(['status' => PurchaseOrderStatus::Completed]);
            }

            return $grn->fresh(['items.product', 'purchaseOrder']);
        });
    }

    /** Real deliveries often arrive in more than one shipment - the PO only closes out once every line is fully covered. */
    private function isFullyReceived(PurchaseOrder $po): bool
    {
        foreach ($po->items as $poItem) {
            $received = GoodsReceiptItem::whereHas(
                'goodsReceipt',
                fn ($query) => $query->where('purchase_order_id', $po->id)
            )->where('product_id', $poItem->product_id)->sum('received_qty');

            if ($received < $poItem->ordered_qty) {
                return false;
            }
        }

        return true;
    }
}
