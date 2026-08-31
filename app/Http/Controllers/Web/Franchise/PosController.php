<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\CreatePosSaleRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\Pos\PosSaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PosController extends Controller
{
    public function __construct(private readonly PosSaleService $service) {}

    public function index(Request $request): View
    {
        $franchiseId = $request->user()->franchise_id;

        $products = Product::query()->where('is_active', true)->get();
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

        // Flattened into plain arrays for the Alpine cart - the client
        // side needs price/stock/prescription-flag per product to build
        // the cart and validate quantities before ever hitting the server.
        $catalog = $products->map(function (Product $product) use ($prices, $stock, $franchiseId) {
            $productPrices = $prices->get($product->id, collect());
            $price = $productPrices->firstWhere('franchise_id', $franchiseId) ?? $productPrices->firstWhere('franchise_id', null);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'price' => $price?->selling_price,
                'prescription_required' => $product->prescription_required,
                'available' => (int) ($stock[$product->id] ?? 0),
            ];
        })->filter(fn ($p) => $p['price'] !== null)->values();

        $isPharmacist = $request->user()->hasRole('Pharmacist');

        return view('franchise.pos.index', compact('catalog', 'isPharmacist'));
    }

    public function store(CreatePosSaleRequest $request): RedirectResponse
    {
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
        } catch (InsufficientStockException|RuntimeException $e) {
            return back()->withErrors(['sale' => $e->getMessage()])->withInput();
        }

        return redirect()->route('franchise.pos.index')->with('success', "Sale #{$order->id} completed - ₹".number_format($order->total_amount, 2));
    }
}
