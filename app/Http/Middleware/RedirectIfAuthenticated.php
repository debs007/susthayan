<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replaces Laravel's own default 'guest' middleware, which redirects an
 * already-authenticated visitor to one hardcoded destination regardless
 * of role. That's wrong here: this app has several genuinely different,
 * role-gated portals. Sending everyone to the same fixed destination
 * meant a Franchise Owner with a still-valid session, visiting
 * /sa/admin, could land somewhere their own role doesn't have access
 * to - a 403 from the destination, not the login page itself.
 *
 * Deliberately does NOT decide role -> destination itself - that
 * mapping already exists and is correct: Web\DashboardController::index(),
 * behind the bare 'dashboard' route name. Redirecting there instead of
 * duplicating that same role check here means there's exactly one place
 * that mapping lives, not two that could quietly drift apart later.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ?string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
