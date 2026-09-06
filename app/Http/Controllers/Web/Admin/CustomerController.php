<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('mobile', 'like', $term));
            })
            ->withCount('orders')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Everything on this page is queried directly by user_id rather than
     * relying purely on the relationships just added to User - this page
     * touches order history, prescriptions, vitals, health records,
     * appointment bookings, lab test bookings, cart contents, and wallet,
     * so eager-loading each explicitly keeps every query visible and
     * bounded (limit()'d) rather than one implicit N+1-prone chain.
     */
    public function show(User $customer): View
    {
        abort_unless($customer->hasRole('Customer'), 404);

        $customer->load([
            'addresses',
            'wallet',
            'orders' => fn ($q) => $q->latest()->limit(20),
            'prescriptions' => fn ($q) => $q->latest()->limit(20),
            'vitals' => fn ($q) => $q->latest()->limit(20),
            'healthRecords' => fn ($q) => $q->latest()->limit(20),
            'appointmentBookings' => fn ($q) => $q->with('doctor', 'hospital')->latest('scheduled_date')->limit(20),
            'labTestBookings' => fn ($q) => $q->with('labTest', 'labCenter')->latest('scheduled_date')->limit(20),
            'cart.items.product',
            'cart.coupon',
        ]);

        return view('admin.customers.show', ['customer' => $customer]);
    }
}
