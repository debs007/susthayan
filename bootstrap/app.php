<?php

use App\Http\Middleware\EnsureFranchiseAccess;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Laravel 11+ removed app/Http/Kernel.php, and with it the
        // mechanism packages used to auto-register into $routeMiddleware.
        // Every alias - ours AND third-party ones - now has to be added
        // here explicitly by the application. Spatie's package does NOT
        // do this on its own despite what an earlier version of this
        // comment claimed; that was wrong from the start, not something
        // that broke later.
        $middleware->alias([
            'franchise.scope' => EnsureFranchiseAccess::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            // Overrides Laravel's own framework-default 'guest' middleware,
            // which redirects every already-authenticated visitor to one
            // hardcoded destination regardless of role - wrong for a system
            // with several genuinely different, role-gated dashboards. See
            // the class itself for the full explanation.
            'guest' => RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
