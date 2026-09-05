<?php

use App\Http\Controllers\Web\Admin\AuditLogController;
use App\Http\Controllers\Web\Admin\BrandController;
use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\CouponController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\DepartmentController;
use App\Http\Controllers\Web\Admin\DoctorController;
use App\Http\Controllers\Web\Admin\FranchiseController as AdminFranchiseController;
use App\Http\Controllers\Web\Admin\HealthArticleController;
use App\Http\Controllers\Web\Admin\HomeBannerController;
use App\Http\Controllers\Web\Admin\HospitalController;
use App\Http\Controllers\Web\Admin\LabCenterController;
use App\Http\Controllers\Web\Admin\LabTestBlockedDateController;
use App\Http\Controllers\Web\Admin\LabTestCategoryController;
use App\Http\Controllers\Web\Admin\LabTestController;
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
use App\Http\Controllers\Web\Franchise\ProductImageController;
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

            // Owner-only, same separation as PO approval below - product
            // catalog changes (even just a photo) have consistently been an
            // ownership-level action in this app, not shared with Staff/
            // Pharmacist. Products are a single shared catalog (no
            // franchise_id on the products table) - an image uploaded here
            // is visible to every franchise's customers, not just this one.
            Route::middleware('role:Franchise Owner')->group(function () {
                Route::get('/products/{product}/image', [ProductImageController::class, 'edit'])->name('products.image.edit');
                Route::post('/products/{product}/image', [ProductImageController::class, 'upload'])->name('products.image.upload');
                Route::delete('/products/{product}/image', [ProductImageController::class, 'remove'])->name('products.image.remove');
            });

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
            Route::post('/products/{product}/image', [AdminProductController::class, 'uploadImage'])->name('products.image.upload');
            Route::delete('/products/{product}/image', [AdminProductController::class, 'removeImage'])->name('products.image.remove');

            Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
            Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
            Route::patch('/brands/{brand}/toggle-active', [BrandController::class, 'toggleActive'])->name('brands.toggle-active');

            Route::get('/lab-test-categories', [LabTestCategoryController::class, 'index'])->name('lab-test-categories.index');
            Route::post('/lab-test-categories', [LabTestCategoryController::class, 'store'])->name('lab-test-categories.store');

            Route::get('/lab-tests', [LabTestController::class, 'index'])->name('lab-tests.index');
            Route::get('/lab-tests/create', [LabTestController::class, 'create'])->name('lab-tests.create');
            Route::post('/lab-tests', [LabTestController::class, 'store'])->name('lab-tests.store');
            Route::get('/lab-tests/{labTest}/edit', [LabTestController::class, 'edit'])->name('lab-tests.edit');
            Route::patch('/lab-tests/{labTest}', [LabTestController::class, 'update'])->name('lab-tests.update');
            Route::patch('/lab-tests/{labTest}/toggle-active', [LabTestController::class, 'toggleActive'])->name('lab-tests.toggle-active');

            Route::get('/lab-centers', [LabCenterController::class, 'index'])->name('lab-centers.index');
            Route::get('/lab-centers/create', [LabCenterController::class, 'create'])->name('lab-centers.create');
            Route::post('/lab-centers', [LabCenterController::class, 'store'])->name('lab-centers.store');
            Route::get('/lab-centers/{labCenter}/edit', [LabCenterController::class, 'edit'])->name('lab-centers.edit');
            Route::patch('/lab-centers/{labCenter}', [LabCenterController::class, 'update'])->name('lab-centers.update');
            Route::patch('/lab-centers/{labCenter}/toggle-active', [LabCenterController::class, 'toggleActive'])->name('lab-centers.toggle-active');

            Route::get('/lab-test-blocked-dates', [LabTestBlockedDateController::class, 'index'])->name('lab-test-blocked-dates.index');
            Route::post('/lab-test-blocked-dates', [LabTestBlockedDateController::class, 'store'])->name('lab-test-blocked-dates.store');
            Route::delete('/lab-test-blocked-dates/{labTestBlockedDate}', [LabTestBlockedDateController::class, 'destroy'])->name('lab-test-blocked-dates.destroy');

            Route::get('/home-banners', [HomeBannerController::class, 'index'])->name('home-banners.index');
            Route::post('/home-banners', [HomeBannerController::class, 'store'])->name('home-banners.store');
            Route::patch('/home-banners/{homeBanner}/toggle-active', [HomeBannerController::class, 'toggleActive'])->name('home-banners.toggle-active');
            Route::delete('/home-banners/{homeBanner}', [HomeBannerController::class, 'destroy'])->name('home-banners.destroy');

            Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
            Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupons.create');
            Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
            Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
            Route::patch('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
            Route::patch('/coupons/{coupon}/toggle-active', [CouponController::class, 'toggleActive'])->name('coupons.toggle-active');

            Route::get('/health-articles', [HealthArticleController::class, 'index'])->name('health-articles.index');
            Route::post('/health-articles', [HealthArticleController::class, 'store'])->name('health-articles.store');
            Route::patch('/health-articles/{healthArticle}/toggle-active', [HealthArticleController::class, 'toggleActive'])->name('health-articles.toggle-active');
            Route::delete('/health-articles/{healthArticle}', [HealthArticleController::class, 'destroy'])->name('health-articles.destroy');

            Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
            Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');

            Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals.index');
            Route::get('/hospitals/create', [HospitalController::class, 'create'])->name('hospitals.create');
            Route::post('/hospitals', [HospitalController::class, 'store'])->name('hospitals.store');
            Route::patch('/hospitals/{hospital}/toggle-active', [HospitalController::class, 'toggleActive'])->name('hospitals.toggle-active');

            Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
            Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
            Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
            Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
            Route::patch('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
            Route::patch('/doctors/{doctor}/toggle-active', [DoctorController::class, 'toggleActive'])->name('doctors.toggle-active');

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
