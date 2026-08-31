<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case ReadyForDispatch = 'ready_for_dispatch';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case PickedUp = 'picked_up';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    /** Payment != revenue until fulfilled - the SRS's own key rule. */
    public function isRevenueRecognised(): bool
    {
        return $this === self::Delivered || $this === self::PickedUp;
    }

    public function isFulfilled(): bool
    {
        return $this === self::Delivered || $this === self::PickedUp;
    }

    public function isCancellable(): bool
    {
        return in_array($this, [self::PendingPayment, self::Confirmed, self::Preparing], true);
    }
}
