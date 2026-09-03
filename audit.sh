#!/bin/bash
# Full health check for the pharmacy-platform Laravel install.
# Run from the project root: bash audit.sh
#
# Read top to bottom. Fix the FIRST "FAIL" you hit and re-run the whole
# script rather than chasing every line at once - later checks can fail
# purely as a side effect of an earlier one (e.g. if Laravel can't boot,
# everything below that will also show as failed without being a separate
# problem).

echo "======================================================"
echo "1. Laravel boots at all"
echo "======================================================"
php artisan --version

echo ""
echo "======================================================"
echo "2. Package versions - the 3 that needed Laravel 13 bumps"
echo "======================================================"
echo "-- laravel/tinker (expect v3.x) --"
composer show laravel/tinker 2>/dev/null | grep versions
echo "-- spatie/laravel-permission (expect v8.x) --"
composer show spatie/laravel-permission 2>/dev/null | grep versions
echo "-- spatie/laravel-activitylog (expect v5.x) --"
composer show spatie/laravel-activitylog 2>/dev/null | grep versions

echo ""
echo "======================================================"
echo "3. Default Laravel scaffolding files (previously found missing)"
echo "======================================================"
for f in app/Http/Controllers/Controller.php app/Providers/AppServiceProvider.php bootstrap/app.php bootstrap/providers.php artisan; do
  if [ -f "$f" ]; then echo "OK    $f exists"; else echo "FAIL  $f MISSING"; fi
done

echo ""
echo "======================================================"
echo "4. bootstrap/providers.php actually lists AppServiceProvider"
echo "======================================================"
grep -q "AppServiceProvider::class" bootstrap/providers.php 2>/dev/null \
  && echo "OK    AppServiceProvider is registered" \
  || echo "FAIL  AppServiceProvider missing from bootstrap/providers.php"

echo ""
echo "======================================================"
echo "5. Every middleware alias this app actually uses is registered"
echo "======================================================"
for alias in "'franchise.scope'" "'role'" "'permission'" "'role_or_permission'"; do
  grep -q "$alias =>" bootstrap/app.php 2>/dev/null \
    && echo "OK    $alias registered" \
    || echo "FAIL  $alias NOT in bootstrap/app.php"
done

echo ""
echo "======================================================"
echo "6. Custom service interface bindings actually resolve"
echo "======================================================"
php artisan tinker --execute="echo 'SMS: ' . get_class(app(App\Services\Sms\SmsProviderInterface::class));" 2>&1
echo ""
php artisan tinker --execute="echo 'Payment: ' . get_class(app(App\Services\Payment\PaymentGatewayInterface::class));" 2>&1
echo ""
php artisan tinker --execute="echo 'Payout: ' . get_class(app(App\Services\Payout\PayoutGatewayInterface::class));" 2>&1

echo ""
echo "======================================================"
echo "7. Database connection + migration status"
echo "======================================================"
php artisan migrate:status 2>&1 | tail -6

echo ""
echo "======================================================"
echo "8. Redis"
echo "======================================================"
php artisan tinker --execute="Cache::put('audit-test','ok',5); echo Cache::get('audit-test');" 2>&1

echo ""
echo "======================================================"
echo "9. Roles seeded correctly"
echo "======================================================"
php artisan tinker --execute="echo Spatie\Permission\Models\Role::count() . ' roles found: ' . Spatie\Permission\Models\Role::pluck('name')->implode(', ');" 2>&1

echo ""
echo "======================================================"
echo "10. THE test that actually matters: web-session login + role check"
echo "    together, the exact combination /login and /two-factor use"
echo "======================================================"
php artisan tinker --execute='
$user = App\Models\User::where("mobile", "9999999999")->first();
if (!$user) { echo "FAIL: seeded Super Admin (9999999999) not found - run db:seed"; exit; }
Auth::guard("web")->login($user);
$resolved = Auth::guard("web")->user();
echo ($resolved && $resolved->hasRole("Super Admin"))
    ? "PASS: web-guard login + hasRole both work correctly"
    : "FAIL: hasRole did not resolve for a web-guard-authenticated user";
' 2>&1

echo ""
echo "======================================================"
echo "Audit complete."
echo "======================================================"
