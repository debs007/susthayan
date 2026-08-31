<?php

use App\Http\Controllers\Web\Admin\AuditLogController;
use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\FranchiseController as AdminFranchiseController;
use App\Http\Controllers\Web\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Web\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Web\Admin\SettlementController as AdminSettlementController;
use App\Http\Controllers\Web\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\Admin\VendorLedgerController as AdminVendorLedgerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Franchise\DashboardController as FranchiseDashboardController;
use App\Http\Controllers\Web\Franchise\InventoryController;
use App\Http\Controllers\Web\Franchise\OrderController;
use App\Http\Controllers\Web\Franchise\PosController;
use App\Http\Controllers\Web\Franchise\PrescriptionController;
use App\Http\Controllers\Web\Franchise\PurchaseOrderController;
use App\Http\Controllers\Web\Franchise\SettlementController as FranchiseSettlementController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Server-rendered pages for the Franchise & Admin portals (session auth,
| 'web' guard). The Flutter apps talk to routes/api.php instead - see that
| file's header comment for why the two coexist on one Laravel app.
|
| Every controller here is a *separate* Web\* class from its Api\* sibling
| by design - same underlying services and, wherever the shape matches,
| the exact same Form Request classes for validation - but a redirect+
| Blade response and a JSON response are different enough concerns that
| forcing them into one controller wasn't worth it. See Web\Admin\
| FranchiseController's docblock for the fuller reasoning.
|
*/

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest:web')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/two-factor', [LoginController::class, 'showTwoFactor'])->name('two-factor.show');
    Route::post('/two-factor', [LoginController::class, 'verifyTwoFactor'])->name('two-factor.verify');

    Route::get('/forgot-password', [LoginController::class, 'showForgotPassword'])->name('forgot-password.show');
    Route::post('/forgot-password', [LoginController::class, 'sendResetOtp'])
        ->middleware('throttle:otp')
        ->name('forgot-password.send');
    Route::get('/reset-password', [LoginController::class, 'showResetPassword'])->name('password-reset.show');
    Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password-reset.update');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Franchise Portal - Owner, Staff, Pharmacist share it; franchise.scope
    // stops any of them reaching another store's data via a route-bound id.
    Route::prefix('franchise')
        ->name('franchise.')
        ->middleware(['role:Franchise Owner|Franchise Staff|Pharmacist', 'franchise.scope'])
        ->group(function () {
            Route::get('/dashboard', [FranchiseDashboardController::class, 'index'])->name('dashboard');

            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
            Route::post('/orders/{order}/assign-delivery', [OrderController::class, 'assignDelivery'])->name('orders.assign-delivery');

            Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
            Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

            Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

            // role:Pharmacist further narrows verification specifically -
            // Franchise Staff can see orders/POS/inventory but shouldn't
            // be the one clearing a prescription, same separation the API
            // already enforces on this same action.
            Route::middleware('role:Pharmacist')->group(function () {
                Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
                Route::patch('/prescriptions/{prescription}/verify', [PrescriptionController::class, 'verify'])->name('prescriptions.verify');
            });
            Route::get('/prescriptions/{prescription}/file', [PrescriptionController::class, 'show'])->name('prescriptions.file');

            Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
            Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
            Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
            Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
            // Owner-only approval, same separation of duties as the API.
            Route::middleware('role:Franchise Owner')->group(function () {
                Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
                Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');
            });
            Route::post('/purchase-orders/{purchaseOrder}/receive-goods', [PurchaseOrderController::class, 'receiveGoods'])->name('purchase-orders.receive-goods');
            Route::post('/purchase-orders/{purchaseOrder}/capture-invoice', [PurchaseOrderController::class, 'captureInvoice'])->name('purchase-orders.capture-invoice');
            Route::post('/purchase-orders/{purchaseOrder}/invoices/{invoice}/payments', [PurchaseOrderController::class, 'recordPayment'])->name('purchase-orders.record-payment');

            Route::get('/settlements', [FranchiseSettlementController::class, 'index'])->name('settlements.index');
        });

    // Admin / Finance Portal
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:Super Admin|Accountant')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::resource('franchises', AdminFranchiseController::class)
                ->only(['index', 'create', 'store', 'edit', 'update']);
            Route::patch('/franchises/{franchise}/bank-details', [AdminFranchiseController::class, 'updateBankDetails'])
                ->name('franchises.bank-details');

            Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
            Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');

            Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
            Route::post('/products/{product}/prices', [AdminProductController::class, 'storePrice'])->name('products.prices.store');

            Route::get('/vendors', [AdminSupplierController::class, 'index'])->name('vendors.index');
            Route::get('/vendors/create', [AdminSupplierController::class, 'create'])->name('vendors.create');
            Route::post('/vendors', [AdminSupplierController::class, 'store'])->name('vendors.store');
            Route::get('/vendors/{supplier}/edit', [AdminSupplierController::class, 'edit'])->name('vendors.edit');
            Route::patch('/vendors/{supplier}', [AdminSupplierController::class, 'update'])->name('vendors.update');
            Route::get('/vendors-outstanding', [AdminVendorLedgerController::class, 'outstanding'])->name('vendors.outstanding');
            Route::get('/vendors/{supplier}/ledger', [AdminVendorLedgerController::class, 'ledger'])->name('vendors.ledger');

            Route::get('/settlements', [AdminSettlementController::class, 'index'])->name('settlements.index');
            Route::post('/settlements/generate', [AdminSettlementController::class, 'generate'])->name('settlements.generate');
            Route::post('/settlements/{settlement}/release', [AdminSettlementController::class, 'release'])->name('settlements.release');

            Route::get('/reports/sales-register', [AdminReportController::class, 'salesRegister'])->name('reports.sales-register');
            Route::get('/reports/purchase-register', [AdminReportController::class, 'purchaseRegister'])->name('reports.purchase-register');
            Route::get('/reports/gst-summary', [AdminReportController::class, 'gstSummary'])->name('reports.gst-summary');
            Route::get('/reports/payment-mismatches', [AdminReportController::class, 'paymentMismatches'])->name('reports.payment-mismatches');

            Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/role', [AdminUserController::class, 'changeRole'])->name('users.role');
            Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        });
});
