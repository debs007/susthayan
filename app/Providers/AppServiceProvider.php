<?php

namespace App\Providers;

use App\Services\Payment\LogPaymentGateway;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\RazorpayGateway;
use App\Services\Payout\LogPayoutGateway;
use App\Services\Payout\PayoutGatewayInterface;
use App\Services\Payout\RazorpayXPayoutGateway;
use App\Services\Sms\LogSmsProvider;
use App\Services\Sms\Msg91SmsProvider;
use App\Services\Sms\SmsProviderInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsProviderInterface::class, function () {
            return match (config('services.sms.provider', 'log')) {
                'msg91' => new Msg91SmsProvider(
                    config('services.msg91.authkey'),
                    config('services.msg91.sender_id'),
                ),
                default => new LogSmsProvider(),
            };
        });

        $this->app->bind(PaymentGatewayInterface::class, function () {
            return match (config('services.payment.gateway', 'log')) {
                'razorpay' => new RazorpayGateway(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret'),
                    config('services.razorpay.webhook_secret'),
                ),
                default => new LogPaymentGateway(),
            };
        });

        $this->app->bind(PayoutGatewayInterface::class, function () {
            return match (config('services.payout.gateway', 'log')) {
                'razorpayx' => new RazorpayXPayoutGateway(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret'),
                    config('services.razorpay.x_account_number'),
                ),
                default => new LogPayoutGateway(),
            };
        });
    }

    public function boot(): void
    {
        // Shared by /auth/customer/otp/request and /auth/staff/login (both can
        // trigger an OTP send) - keyed by whichever identifier the request
        // actually carries, falling back to IP so it still applies either way.
        RateLimiter::for('otp', function (Request $request) {
            $key = $request->input('mobile') ?? $request->input('login') ?? $request->ip();

            return Limit::perMinute(3)->by($key);
        });
    }
}
