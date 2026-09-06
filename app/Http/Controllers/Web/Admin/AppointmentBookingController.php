<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = AppointmentBooking::with(['user:id,name,mobile', 'doctor:id,name,degree', 'hospital:id,name', 'order:id,status'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';
                $query->whereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('mobile', 'like', $term))
                    ->orWhereHas('doctor', fn ($d) => $d->where('name', 'like', $term));
            })
            ->latest('scheduled_date')
            ->paginate(30)
            ->withQueryString();

        return view('admin.appointment-bookings.index', compact('bookings'));
    }
}
