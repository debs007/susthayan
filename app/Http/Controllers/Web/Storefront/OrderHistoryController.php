<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    /** Excludes pending_payment - same reasoning as the admin dashboard's recent-orders widget: an abandoned, never-paid checkout attempt isn't a real order from the customer's own point of view. */
    public function index(): View
    {
        $orders = Order::where('user_id', auth('web')->id())
            ->whereNotIn('status', ['pending_payment'])
            ->with(['items.product', 'labTestBooking.labTest', 'appointmentBooking.doctor'])
            ->latest()
            ->paginate(15);

        return view('storefront.orders.index', compact('orders'));
    }
}
