<?php

use App\Http\Controllers\Web\Storefront\CategoryController;
use App\Http\Controllers\Web\Storefront\HealthArticleController;
use App\Http\Controllers\Web\Storefront\OfferController;
use App\Http\Controllers\Web\Storefront\OrderConfirmationController;
use App\Http\Controllers\Web\Storefront\OrderHistoryController;
use App\Http\Controllers\Web\Storefront\ProductController;
use App\Livewire\Storefront\AccountPage;
use App\Livewire\Storefront\AppointmentBookingPage;
use App\Livewire\Storefront\CartPage;
use App\Livewire\Storefront\CheckoutPage;
use App\Livewire\Storefront\HealthRecordsPage;
use App\Livewire\Storefront\LabTestBookingPage;
use App\Livewire\Storefront\Login;
use App\Livewire\Storefront\ProductBrowser;
use Illuminate\Support\Facades\Route;

/*
| The '/' route deliberately does NOT live here - see web.php, where it
| lives instead (registered before this file is require'd in at the end
| of it, so it has to be the one that actually owns that URI).
|
| Login lives at /account/login, not /login - the bare /login path is
| already owned by the existing staff LoginController (a completely
| different flow from this OTP-based customer one), and that's a real,
| currently-used staff URL this shouldn't disturb.
*/
Route::name('storefront.')->group(function () {
    Route::get('/account/login', Login::class)->name('login');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/products', ProductBrowser::class)->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/cart', CartPage::class)->name('cart');

    Route::get('/lab-tests', LabTestBookingPage::class)->name('lab-tests');
    Route::get('/appointments', AppointmentBookingPage::class)->name('appointments');
    Route::get('/offers', [OfferController::class, 'index'])->name('offers');
    Route::get('/offers/{coupon}', [OfferController::class, 'products'])->name('offers.products');
    Route::get('/health-articles', [HealthArticleController::class, 'index'])->name('health-articles');

    Route::middleware('auth:web')->group(function () {
        Route::get('/checkout', CheckoutPage::class)->name('checkout');
        Route::get('/orders/{order}/confirmation', [OrderConfirmationController::class, 'show'])->name('orders.confirmation');
        Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders');
        Route::get('/orders/{order}/invoice', [OrderHistoryController::class, 'downloadInvoice'])->name('orders.invoice');
        Route::get('/account', AccountPage::class)->name('account');
        Route::get('/health-records', HealthRecordsPage::class)->name('health-records');
    });
});
