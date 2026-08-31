<?php

namespace App\Services\Purchasing;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseOrderService
{
    /**
     * @param  array<int, array{product_id: int, ordered_qty: int, expected_rate: float}>  $items
     */
    public function create(User $creator, int $franchiseId, int $supplierId, array $items, ?string $expectedDate): PurchaseOrder
    {
        if (empty($items)) {
            throw new RuntimeException('A purchase order needs at least one line item.');
        }

        return DB::transaction(function () use ($creator, $franchiseId, $supplierId, $items, $expectedDate) {
            $po = PurchaseOrder::create([
                'supplier_id' => $supplierId,
                'franchise_id' => $franchiseId,
                'created_by' => $creator->id,
                'status' => PurchaseOrderStatus::PendingApproval,
                'po_date' => now()->toDateString(),
                'expected_date' => $expectedDate,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'ordered_qty' => $item['ordered_qty'],
                    'expected_rate' => $item['expected_rate'],
                ]);

                $total += $item['ordered_qty'] * $item['expected_rate'];
            }

            $po->update(['total_amount' => $total]);

            return $po->fresh(['items.product', 'supplier']);
        });
    }

    public function approve(PurchaseOrder $po, User $approver): PurchaseOrder
    {
        $this->assertPending($po);

        $po->update([
            'status' => PurchaseOrderStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $po->fresh();
    }

    public function reject(PurchaseOrder $po, User $approver): PurchaseOrder
    {
        $this->assertPending($po);

        $po->update([
            'status' => PurchaseOrderStatus::Rejected,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $po->fresh();
    }

    private function assertPending(PurchaseOrder $po): void
    {
        if ($po->status !== PurchaseOrderStatus::PendingApproval) {
            throw new RuntimeException("This purchase order is \"{$po->status->value}\", not pending approval.");
        }
    }
}
