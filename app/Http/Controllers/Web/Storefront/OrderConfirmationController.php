<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderConfirmationController extends Controller
{
    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth('web')->id(), 403);

        $order->load(['items.product', 'franchise', 'address']);

        return view('storefront.orders.confirmation', compact('order'));
    }
}
