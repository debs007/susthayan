<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The multi-tenancy boundary: a Franchise Owner/Staff/Pharmacist/Delivery
 * Agent must never be able to read or act on another franchise's data just
 * by changing an id in the URL. Super Admin and Accountant are exempt -
 * they're meant to see across every franchise.
 *
 * Works generically off route-model binding: any bound model in the route
 * that has a franchise_id column gets checked against the logged-in user's
 * own franchise_id automatically, so new routes get this for free just by
 * type-hinting the model - no per-controller code needed.
 *
 * Register as an alias (see bootstrap/app.php) and apply to franchise-scoped
 * route groups, e.g. ->middleware(['auth:sanctum', 'role:...', 'franchise.scope']).
 */
class EnsureFranchiseAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasAnyRole(['Super Admin', 'Accountant'])) {
            return $next($request);
        }

        foreach ($request->route()?->parameters() ?? [] as $parameter) {
            if (! $parameter instanceof Model) {
                continue;
            }

            if (! array_key_exists('franchise_id', $parameter->getAttributes())) {
                continue;
            }

            if ((int) $parameter->getAttribute('franchise_id') !== (int) $user->franchise_id) {
                abort(403, "You don't have access to this franchise's data.");
            }
        }

        return $next($request);
    }
}
