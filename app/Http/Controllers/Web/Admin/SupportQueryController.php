<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportQueryController extends Controller
{
    public function index(Request $request): View
    {
        $queries = SupportQuery::with('user:id,name,mobile')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.support-queries.index', compact('queries'));
    }

    public function resolve(SupportQuery $supportQuery): RedirectResponse
    {
        $supportQuery->update(['status' => 'resolved', 'resolved_at' => now()]);

        return back()->with('success', 'Marked as resolved.');
    }
}
