<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSystemAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'Please sign in as system admin.');
        }

        if (!$user->isSystemAdmin()) {
            return redirect()->route('dashboard.index')->with('error', 'System admin access only.');
        }

        return $next($request);
    }
}
