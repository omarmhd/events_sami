<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectSystemAdminToSystemPanel
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->isSystemAdmin() && !$request->session()->has('impersonator_id')) {
            return redirect()->route('system.dashboard')
                ->with('error', 'System admins must use the /admin panel. Use impersonation to access organizer panel.');
        }

        return $next($request);
    }
}

