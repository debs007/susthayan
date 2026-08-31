# Manual steps required after extracting this backend

Three features were added on top of your restored codebase that need routes
registered and one model relationship added - these can't be applied
automatically since routes/api.php and app/Models/User.php are large,
existing files better edited by hand than risked with an automated patch.

## 1. routes/api.php - add inside the existing `customer` prefix group

```php
use App\Http\Controllers\Api\Customer\FranchiseController;
use App\Http\Controllers\Api\Customer\HealthController;
use App\Http\Controllers\Api\Customer\NotificationController;

// Franchise discovery
Route::get('/franchises', [FranchiseController::class, 'index']);

// Health records
Route::prefix('health')->group(function () {
    Route::get('/profile', [HealthController::class, 'showProfile']);
    Route::put('/profile', [HealthController::class, 'updateProfile']);
    Route::get('/vitals', [HealthController::class, 'indexVitals']);
    Route::post('/vitals', [HealthController::class, 'storeVitals']);
    Route::get('/records', [HealthController::class, 'indexRecords']);
    Route::post('/records', [HealthController::class, 'storeRecord']);
    Route::get('/records/{healthRecord}/file', [HealthController::class, 'showRecordFile']);
});

// Notifications
Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
```

## 2. app/Models/User.php - add these relationships

```php
use Illuminate\Database\Eloquent\Relations\HasOne; // add to imports if missing

public function healthProfile(): HasOne
{
    return $this->hasOne(HealthProfile::class);
}

public function vitals(): HasMany
{
    return $this->hasMany(Vital::class);
}

public function healthRecords(): HasMany
{
    return $this->hasMany(HealthRecord::class);
}

public function notifications(): HasMany
{
    return $this->hasMany(Notification::class);
}
```

## 3. Then run

```bash
composer dump-autoload
php artisan migrate
php artisan optimize:clear
```

## What was added, for reference

- **Role guard fix** - roles now seed under both `sanctum` and `web` guards
  (`AssignsRoleAcrossGuards` trait). Your restored copy still had the
  `RoleDoesNotExist for guard web` bug present.
- **PosController closure fix** - `$franchiseId` was referenced inside a
  closure but missing from its `use()` clause.
- **Susthayan rebrand** - `.env.example`, `composer.json`, both Blade
  layout files, and the OTP SMS message text.
- **Franchise Discovery module** - `GET /customer/franchises`, the
  endpoint that was missing this entire build, needed for checkout to
  resolve a `franchise_id`.
- **Health Records module** - profile, vitals, and records (lab reports/
  documents/vaccinations/checkups), with real Prescription data merged
  into the records list rather than duplicated.
- **Notifications module** - in-app notifications with read/unread state.
  Push notifications (Firebase Cloud Messaging) were NOT built - this is
  in-app only, and the Flutter app says so honestly rather than pretending
  push works.

## Still not built (known gaps, not oversights)

- Push notifications (FCM) - would need device token registration,
  Firebase project setup, and a dispatch mechanism, none of which exists.
- Appointments (shown in the original Health Records design) - would need
  a full booking/scheduling system, not just a display screen.
- Wallet, Coupons, Payment Methods (saved cards), Medicine Reminders,
  Reviews, Refer & Earn - no backend exists for any of these.
- Supplier product price lists were NOT in your restored zip and have NOT
  been re-applied in this delivery - flag if you need this specifically
  and I'll add it back.
