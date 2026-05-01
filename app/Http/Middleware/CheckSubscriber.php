<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscriber
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $isAdmin = (bool) ($user->is_system_admin ?? false)
            || in_array((string) ($user->role ?? ''), ['system_admin', 'super_admin', 'saas_admin', 'admin'], true);

        if ($isAdmin) {
            abort(403, 'Subscriber access only.');
        }

        return $next($request);
    }
}
