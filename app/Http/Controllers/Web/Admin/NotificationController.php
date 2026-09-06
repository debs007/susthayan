<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Search-then-compose, same shape as the Customers list search - no
     * autocomplete/AJAX picker exists anywhere else in this admin
     * portal, so this matches the established pattern rather than
     * introduce a new one for just this page. ?user_id preselects a
     * specific customer, e.g. arriving here from their profile page.
     */
    public function create(Request $request): View
    {
        $customers = collect();
        if ($request->filled('search')) {
            $term = '%'.$request->string('search').'%';
            $customers = User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'))
                ->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('mobile', 'like', $term))
                ->limit(20)
                ->get(['id', 'name', 'mobile']);
        }

        $preselected = $request->filled('user_id') ? User::find($request->integer('user_id')) : null;

        return view('admin.notifications.create', compact('customers', 'preselected'));
    }

    public function store(SendNotificationRequest $request): RedirectResponse
    {
        $recipient = User::findOrFail($request->validated('user_id'));

        Notification::create([
            'user_id' => $recipient->id,
            'type' => 'admin',
            'title' => $request->validated('title'),
            'message' => $request->validated('message'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.notifications.create')->with('success', "Notification sent to {$recipient->name}.");
    }
}
