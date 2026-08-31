<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\CreatePosSaleRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\Pos\PosSaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class PosController extends Controller
{
    public function __construct(private readonly PosSaleService $service) {}

    /**
     * Barcode-scanner input lands in ?barcode= as a plain HID keyboard
     * event - a scanner is just a very fast typist, no special frontend
     * integration needed beyond a focused text input. ?q= covers manual
     * name search for items without a scannable barcode.
     */
    public function products(Request $request): AnonymousResourceCollection
    {
        $franchiseId = $request->user()->franchise_id;

        $products = Product::query()
            ->where('is_active', true)
            ->with('category')
            ->when($request->filled('barcode'), fn ($query) => $query->where('barcode', $request->string('barcode')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('barcode', $term));
            })
            ->limit(25)
            ->get();

        $productIds = $products->pluck('id');

        $prices = ProductPrice::whereIn('product_id', $productIds)
            ->where(fn ($q) => $q->where('franchise_id', $franchiseId)->orWhereNull('franchise_id'))
            ->get()
            ->groupBy('product_id');

        $stock = Inventory::whereIn('product_id', $productIds)
            ->where('franchise_id', $franchiseId)
            ->selectRaw('product_id, SUM(quantity - reserved_quantity) as available')
            ->groupBy('product_id')
            ->pluck('available', 'product_id');

        foreach ($products as $product) {
            $productPrices = $prices->get($product->id, collect());
            $product->resolved_price = $productPrices->firstWhere('franchise_id', $franchiseId)
                ?? $productPrices->firstWhere('franchise_id', null);
            $product->resolved_stock = (int) ($stock[$product->id] ?? 0);
        }

        return ProductResource::collection($products);
    }

    public function store(CreatePosSaleRequest $request): JsonResponse
    {
        // Client-facing "cash" maps to the DB enum's "pos_cash" - done here,
        // explicitly, rather than inside the FormRequest lifecycle (merging
        // input in a post-validation hook doesn't reliably flow through to
        // ->validated() due to how the underlying Validator snapshots data).
        $paymentMode = $request->validated('payment_mode') === 'cash' ? 'pos_cash' : $request->validated('payment_mode');

        try {
            $order = $this->service->sell(
                staff: $request->user(),
                items: $request->validated('items'),
                paymentMode: $paymentMode,
                customerId: $request->validated('customer_id'),
                walkInName: $request->validated('walk_in_name'),
                walkInPhone: $request->validated('walk_in_phone'),
                prescriptionNote: $request->validated('prescription_note'),
            );
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $sales = Order::where('franchise_id', $request->user()->franchise_id)
            ->where('fulfillment_type', 'pos')
            ->whereDate('created_at', $request->date('date') ?? today())
            ->with(['items.product', 'payments'])
            ->latest()
            ->paginate(30);

        return OrderResource::collection($sales);
    }

    /** The "Daily sales & cash report" from SRS 3.3. */
    public function summary(Request $request): JsonResponse
    {
        $date = $request->date('date') ?? today();

        $sales = Order::where('franchise_id', $request->user()->franchise_id)
            ->where('fulfillment_type', 'pos')
            ->whereDate('created_at', $date)
            ->with('payments')
            ->get();

        $byMode = $sales->flatMap->payments
            ->groupBy('payment_mode')
            ->map(fn ($payments) => (string) $payments->sum('amount'));

        return response()->json([
            'date' => $date->toDateString(),
            'total_sales' => $sales->count(),
            'total_amount' => (string) $sales->sum('total_amount'),
            'by_payment_mode' => $byMode,
        ]);
    }
}
