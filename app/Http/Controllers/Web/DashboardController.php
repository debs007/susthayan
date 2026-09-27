<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Single /dashboard entry point that routes each role to its own
     * portal. Previously this was a binary check (Super Admin/Accountant
     * vs "everyone else assumed franchise-worthy") with no real
     * verification of the franchise roles - Delivery Agent (which has no
     * web portal at all) fell through that gap and got sent to
     * franchise.dashboard regardless, which then correctly rejected it
     * with a 403. Now every branch is explicit, and a role this app
     * genuinely has no web destination for gets logged out with a clear
     * reason instead of being sent somewhere that will just reject it.
     */
    public function index(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if ($user->hasAnyRole(['Super Admin', 'Accountant'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasAnyRole(['Franchise Owner', 'Franchise Staff', 'Pharmacist'])) {
            return redirect()->route('franchise.dashboard');
        }

        // Delivery Agent (or any other role) has no web portal - nothing
        // to usefully redirect to, so end the session cleanly rather
        // than bounce them toward a destination that would just 403.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors(['login' => 'Your account doesn\'t have a web portal - please use the Susthayan Staff mobile app instead.']);
    }
}
