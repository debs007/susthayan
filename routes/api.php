<?php

use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\FranchiseController as AdminFranchiseController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\PurchaseOrderController as AdminPurchaseOrderController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\SettlementController as AdminSettlementController;
use App\Http\Controllers\Api\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\VendorLedgerController as AdminVendorLedgerController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\CustomerAuthController;
use App\Http\Controllers\Api\Auth\StaffAuthController;
use App\Http\Controllers\Api\Customer\AddressController;
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\PaymentController;
use App\Http\Controllers\Api\Customer\PrescriptionController;
use App\Http\Controllers\Api\Customer\ProductController;
use App\Http\Controllers\Api\Customer\RefundController;
use App\Http\Controllers\Api\Delivery\AssignmentController as DeliveryAssignmentAgentController;
use App\Http\Controllers\Api\Franchise\DeliveryAssignmentController;
use App\Http\Controllers\Api\Franchise\GoodsReceiptController;
use App\Http\Controllers\Api\Franchise\OrderFulfillmentController;
use App\Http\Controllers\Api\Franchise\PosController;
use App\Http\Controllers\Api\Franchise\PrescriptionVerificationController;
use App\Http\Controllers\Api\Franchise\PurchaseOrderController;
use App\Http\Controllers\Api\Franchise\SettlementController;
use App\Http\Controllers\Api\Franchise\SupplierController;
use App\Http\Controllers\Api\Franchise\SupplierInvoiceController;
use App\Http\Controllers\Api\Franchise\SupplierPaymentController;
use App\Http\Controllers\Api\Franchise\VendorLedgerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| One `users` table, one `sanctum` guard - the 4 login "portals" are
| enforced here via route-group prefixes + Spatie role middleware, not by
| separate guards. Each Flutter/web client only ever calls its own prefix.
|
| Every flow from the System Flow doc has real implementation now. What's
| left is depth, not breadth - see the README's "Not included yet" list
| for what's deliberately simplified or deferred within each module.
|
*/

// Registration/login differ per portal (OTP for customers, password
// (+2FA for Super Admin) for staff/admin) but all end in a Sanctum token.
Route::prefix('auth')->group(function () {
    Route::post('/customer/otp/request', [CustomerAuthController::class, 'requestOtp'])
        ->middleware('throttle:otp');
    Route::post('/customer/otp/verify', [CustomerAuthController::class, 'verifyOtp']);

    Route::post('/staff/login', [StaffAuthController::class, 'login'])
        ->middleware('throttle:otp');
    Route::post('/staff/two-factor/verify', [StaffAuthController::class, 'verifyTwoFactor']);
});

// Shared across all 4 portals.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 1. Customer app / website
Route::prefix('customer')->middleware(['auth:sanctum', 'role:Customer'])->group(function () {
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/select-franchise', [CartController::class, 'selectFranchise']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem']);

    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/prescriptions', [PrescriptionController::class, 'index']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/payments/initiate', [PaymentController::class, 'initiate']);
    Route::post('/orders/{order}/refunds', [RefundController::class, 'store']);
    Route::get('/orders/{order}/refunds', [RefundController::class, 'index']);

    Route::post('/payments/verify', [PaymentController::class, 'verify']);
});

// 2. Franchise Portal - Franchise Owner, Franchise Staff, Pharmacist share this
// prefix; what each can actually do is narrowed further by role checks on
// individual routes below, not by splitting the route list itself.
// franchise.scope blocks any of them from reaching another franchise's data
// through a route-model-bound id (see app/Http/Middleware/EnsureFranchiseAccess).
Route::prefix('franchise')
    ->middleware(['auth:sanctum', 'role:Franchise Owner|Franchise Staff|Pharmacist', 'franchise.scope'])
    ->group(function () {
        Route::get('/orders', [OrderFulfillmentController::class, 'index']);
        Route::post('/orders/{order}/status', [OrderFulfillmentController::class, 'updateStatus']);
        Route::get('/delivery-assignments', [DeliveryAssignmentController::class, 'index']);
        Route::post('/orders/{order}/assign-delivery', [DeliveryAssignmentController::class, 'store']);

        Route::middleware('role:Pharmacist')->group(function () {
            Route::get('/prescriptions', [PrescriptionVerificationController::class, 'index']);
            Route::post('/prescriptions/{prescription}/verify', [PrescriptionVerificationController::class, 'verify']);
        });

        // POS - "real-time final sale" per the SRS, no reservation phase.
        // Any Owner/Staff/Pharmacist can ring up a sale; PosSaleService
        // itself further gates prescription-item sales to role:Pharmacist
        // specifically, since that check depends on what's actually in the
        // cart (unknowable at the route-middleware level).
        Route::get('/pos/products', [PosController::class, 'products']);
        Route::post('/pos/sales', [PosController::class, 'store']);
        Route::get('/pos/sales', [PosController::class, 'index']);
        Route::get('/pos/summary', [PosController::class, 'summary']);

        // Purchase & Vendor - any Owner/Staff/Pharmacist can create a PO or
        // receive goods; approving a PO/invoice and recording a payment are
        // narrowed to role:Franchise Owner specifically (separation of
        // duties - the person who orders shouldn't be the only one who can
        // also approve their own order and pay for it).
        Route::get('/suppliers', [SupplierController::class, 'index']);

        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
        Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show']);
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store']);
        Route::post('/purchase-orders/{purchaseOrder}/grn', [GoodsReceiptController::class, 'store']);

        Route::get('/supplier-invoices', [SupplierInvoiceController::class, 'index']);
        Route::post('/supplier-invoices', [SupplierInvoiceController::class, 'store']);

        Route::get('/vendors/outstanding', [VendorLedgerController::class, 'outstanding']);
        Route::get('/vendors/{supplier}/ledger', [VendorLedgerController::class, 'ledger']);

        Route::get('/settlements', [SettlementController::class, 'index']);

        Route::middleware('role:Franchise Owner')->group(function () {
            Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve']);
            Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject']);
            Route::post('/supplier-invoices/{supplierInvoice}/approve', [SupplierInvoiceController::class, 'approve']);
            Route::post('/supplier-invoices/{supplierInvoice}/payments', [SupplierPaymentController::class, 'store']);
        });
    });

// 3. Delivery Agent app - franchise.scope is harmless here but not what's
// actually protecting these routes: DeliveryAssignment has no franchise_id
// column, since an agent's real boundary is "my own assignments," not "my
// franchise's assignments" (see Api\Delivery\AssignmentController).
Route::prefix('delivery')
    ->middleware(['auth:sanctum', 'role:Delivery Agent', 'franchise.scope'])
    ->group(function () {
        Route::get('/assignments', [DeliveryAssignmentAgentController::class, 'index']);
        Route::get('/assignments/{assignment}', [DeliveryAssignmentAgentController::class, 'show']);
        Route::post('/assignments/{assignment}/picked-up', [DeliveryAssignmentAgentController::class, 'pickedUp']);
        Route::post('/assignments/{assignment}/delivered', [DeliveryAssignmentAgentController::class, 'delivered']);
        Route::post('/assignments/{assignment}/failed', [DeliveryAssignmentAgentController::class, 'failed']);
    });

// 4. Admin / Finance Portal
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:Super Admin|Accountant'])
    ->group(function () {
        Route::get('/suppliers', [AdminSupplierController::class, 'index']);
        Route::post('/suppliers', [AdminSupplierController::class, 'store']);
        Route::get('/suppliers/{supplier}', [AdminSupplierController::class, 'show']);
        Route::patch('/suppliers/{supplier}', [AdminSupplierController::class, 'update']);

        Route::get('/purchase-orders', [AdminPurchaseOrderController::class, 'index']);

        Route::get('/vendors/outstanding', [AdminVendorLedgerController::class, 'outstanding']);
        Route::get('/vendors/{supplier}/ledger', [AdminVendorLedgerController::class, 'ledger']);

        // Franchise Settlement - deliberately Admin-only to generate/release,
        // not something a franchise can do to itself (see Api\Franchise\
        // SettlementController for their read-only view of the result).
        Route::get('/franchises', [AdminFranchiseController::class, 'index']);
        Route::post('/franchises', [AdminFranchiseController::class, 'store']);
        Route::get('/franchises/{franchise}', [AdminFranchiseController::class, 'show']);
        Route::patch('/franchises/{franchise}', [AdminFranchiseController::class, 'update']);
        Route::patch('/franchises/{franchise}/bank-details', [AdminFranchiseController::class, 'updateBankDetails']);

        Route::get('/settlements', [AdminSettlementController::class, 'index']);
        Route::post('/franchises/{franchise}/settlements/generate', [AdminSettlementController::class, 'generate']);
        Route::post('/settlements/generate-all', [AdminSettlementController::class, 'generateAll']);
        Route::post('/settlements/{settlement}/release', [AdminSettlementController::class, 'release']);

        // Catalog - no franchise.scope in this group at all (Admin sees/
        // manages everything), but products/categories aren't franchise-
        // scoped to begin with; pricing is the only part with a per-
        // franchise dimension, handled by an optional franchise_id here
        // rather than a route prefix.
        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::post('/categories', [AdminCategoryController::class, 'store']);

        Route::get('/products', [AdminProductController::class, 'index']);
        Route::post('/products', [AdminProductController::class, 'store']);
        Route::get('/products/{product}', [AdminProductController::class, 'show']);
        Route::patch('/products/{product}', [AdminProductController::class, 'update']);
        Route::post('/products/{product}/prices', [AdminProductController::class, 'setPrice']);

        // Staff onboarding - the only path any non-Customer account is
        // created through. Creating a Super Admin/Accountant account is
        // further narrowed to an existing Super Admin inside
        // CreateStaffUserRequest, not at the route level (needs the
        // submitted role to decide, which route middleware can't see).
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::patch('/users/{user}', [AdminUserController::class, 'update']);
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword']);

        Route::get('/audit-log', [AuditLogController::class, 'index']);
        Route::get('/audit-log/{activity}', [AuditLogController::class, 'show']);

        // Reporting & Accounting - Vendor Outstanding and Franchise
        // Settlement reports already exist above; these are the rest of
        // the flow (?format=csv on the first two for Tally/ERP import).
        Route::get('/reports/sales-register', [ReportController::class, 'salesRegister']);
        Route::get('/reports/purchase-register', [ReportController::class, 'purchaseRegister']);
        Route::get('/reports/gst-summary', [ReportController::class, 'gstSummary']);
        Route::get('/reports/payment-mismatches', [ReportController::class, 'paymentMismatches']);
    });

// Webhooks stay outside every role-guarded group above - Razorpay signs each
// request instead, and that signature is what the controller must verify.
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);
