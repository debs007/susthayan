<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $activity = Activity::query()
            ->with('causer:id,name,mobile')
            ->when($request->filled('subject_type'), fn ($query) => $query->where('subject_type', 'App\\Models\\'.$request->string('subject_type')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        // Short names for the filter dropdown - matches the API's own
        // ?subject_type=Order style rather than making someone type the
        // full App\Models\Order string into a form field.
        $subjectTypes = ['Order', 'PurchaseOrder', 'CustomerPayment', 'Refund', 'Prescription', 'FranchiseSettlement', 'Franchise', 'User'];

        return view('admin.audit-log.index', compact('activity', 'subjectTypes'));
    }
}
