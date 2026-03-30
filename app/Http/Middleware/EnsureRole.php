<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  string  ...$roles  Comma-separated roles in single parameter from route: role:admin,dispatcher
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $allowed = [];
        foreach ($roles as $r) {
            $allowed = array_merge($allowed, array_map('trim', explode(',', $r)));
        }

        if (! in_array($request->user()->role, $allowed, true)) {
            abort(403, 'You are not authorized to access this resource.');
        }

        return $next($request);
    }
}
