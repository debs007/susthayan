<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /** Unlike franchise.orders.index (scoped to one store), this spans every franchise - the actual cross-store overview that didn't exist anywhere before. */
    public function index(Request $request): View
    {
        $orders = Order::with(['user:id,name,mobile', 'franchise:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('order_type'), fn ($q) => $q->where('order_type', $request->string('order_type')))
            ->when($request->filled('franchise_id'), fn ($q) => $q->where('franchise_id', $request->integer('franchise_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';
                $query->where(fn ($q) => $q->where('id', $request->string('search'))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('mobile', 'like', $term)));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Enriched detail - every line item, the full payment history (not
     * just the latest one - a failed attempt followed by a successful
     * retry should both be visible here), and whichever
     * booking/appointment detail applies to this specific order_type,
     * same shape OrderResource already exposes to the app.
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'franchise',
            'address',
            'items.product',
            'payments',
            'deliveryAssignments.deliveryAgent',
            'labTestBooking.labTest',
            'labTestBooking.labCenter',
            'appointmentBooking.doctor',
            'appointmentBooking.hospital',
        ]);

        return view('admin.orders.show', compact('order'));
    }
}
