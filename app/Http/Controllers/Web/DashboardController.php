<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Single /dashboard entry point that routes each role to its own portal. */
    public function index(): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if ($user->hasAnyRole(['Super Admin', 'Accountant'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('franchise.dashboard');
    }
}
