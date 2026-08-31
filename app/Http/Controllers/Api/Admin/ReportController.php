<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Concerns\ExportsCsv;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportDateRangeRequest;
use App\Services\Reporting\ReportingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    use ExportsCsv;

    public function __construct(private readonly ReportingService $reports) {}

    public function salesRegister(ReportDateRangeRequest $request): JsonResponse|StreamedResponse
    {
        $rows = $this->reports->salesRegister($request->from(), $request->to(), $request->validated('franchise_id'));

        if ($request->validated('format') === 'csv') {
            return $this->csvResponse(
                'sales-register.csv',
                ['Date', 'Order ID', 'Invoice Number', 'Franchise', 'Channel', 'Subtotal', 'Tax', 'Total'],
                $rows->map(fn ($row) => array_values($row)),
            );
        }

        return response()->json([
            'period' => ['from' => $request->from(), 'to' => $request->to()],
            'rows' => $rows,
            'total' => (string) $rows->sum(fn ($row) => (float) $row['total']),
        ]);
    }

    public function purchaseRegister(ReportDateRangeRequest $request): JsonResponse|StreamedResponse
    {
        $rows = $this->reports->purchaseRegister($request->from(), $request->to(), $request->validated('franchise_id'));

        if ($request->validated('format') === 'csv') {
            return $this->csvResponse(
                'purchase-register.csv',
                ['Date', 'Invoice Number', 'Supplier', 'Amount', 'GST', 'Total'],
                $rows->map(fn ($row) => array_values($row)),
            );
        }

        return response()->json([
            'period' => ['from' => $request->from(), 'to' => $request->to()],
            'rows' => $rows,
            'total' => (string) $rows->sum(fn ($row) => (float) $row['total']),
        ]);
    }

    public function gstSummary(ReportDateRangeRequest $request): JsonResponse
    {
        return response()->json(
            $this->reports->gstSummary($request->from(), $request->to(), $request->validated('franchise_id'))
        );
    }

    /** No date range - these are current-state data issues, not a historical report. */
    public function paymentMismatches(): JsonResponse
    {
        return response()->json($this->reports->paymentMismatches());
    }
}
