<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Concerns\ExportsCsv;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportDateRangeRequest;
use App\Services\Reporting\ReportingService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    use ExportsCsv;

    public function __construct(private readonly ReportingService $reports) {}

    public function salesRegister(ReportDateRangeRequest $request): View|StreamedResponse
    {
        $rows = $this->reports->salesRegister($request->from(), $request->to(), $request->validated('franchise_id'));

        if ($request->query('format') === 'csv') {
            return $this->csvResponse(
                'sales-register.csv',
                ['Date', 'Order ID', 'Invoice Number', 'Franchise', 'Channel', 'Subtotal', 'Tax', 'Total'],
                $rows->map(fn ($row) => array_values($row)),
            );
        }

        return view('admin.reports.sales-register', [
            'rows' => $rows,
            'total' => $rows->sum(fn ($row) => (float) $row['total']),
            'from' => $request->from(),
            'to' => $request->to(),
        ]);
    }

    public function purchaseRegister(ReportDateRangeRequest $request): View|StreamedResponse
    {
        $rows = $this->reports->purchaseRegister($request->from(), $request->to(), $request->validated('franchise_id'));

        if ($request->query('format') === 'csv') {
            return $this->csvResponse(
                'purchase-register.csv',
                ['Date', 'Invoice Number', 'Supplier', 'Amount', 'GST', 'Total'],
                $rows->map(fn ($row) => array_values($row)),
            );
        }

        return view('admin.reports.purchase-register', [
            'rows' => $rows,
            'total' => $rows->sum(fn ($row) => (float) $row['total']),
            'from' => $request->from(),
            'to' => $request->to(),
        ]);
    }

    public function gstSummary(ReportDateRangeRequest $request): View
    {
        $summary = $this->reports->gstSummary($request->from(), $request->to(), $request->validated('franchise_id'));

        return view('admin.reports.gst-summary', compact('summary'));
    }

    public function paymentMismatches(): View
    {
        $mismatches = $this->reports->paymentMismatches();

        return view('admin.reports.payment-mismatches', compact('mismatches'));
    }
}
