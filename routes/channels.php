<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Reverb only ever sends a private-channel message to sockets that pass
| the matching check below, so these double as the authorization rules for
| who's allowed to watch what.
*/

// One channel per order - the owning customer, staff at the fulfilling
// franchise, or admin can watch it. Not every franchise-scoped role: a
// Franchise Staff member at a DIFFERENT store still can't listen in.
Broadcast::channel('order.{orderId}', function ($user, int $orderId) {
    $order = Order::find($orderId);

    if (! $order) {
        return false;
    }

    if ($user->id === $order->user_id) {
        return true;
    }

    if ($user->hasAnyRole(['Super Admin', 'Accountant'])) {
        return true;
    }

    return $user->franchise_id !== null && (int) $user->franchise_id === (int) $order->franchise_id;
});

// A franchise's own live order queue - its own staff, or admin.
Broadcast::channel('franchise.{franchiseId}', function ($user, int $franchiseId) {
    if ($user->hasAnyRole(['Super Admin', 'Accountant'])) {
        return true;
    }

    return $user->franchise_id !== null && (int) $user->franchise_id === (int) $franchiseId;
});
