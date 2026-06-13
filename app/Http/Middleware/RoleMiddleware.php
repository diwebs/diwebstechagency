<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return $request->expectsJson() 
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        if ($request->user()->status !== 'active') {
            auth()->logout();
            return $request->expectsJson()
                ? response()->json(['message' => 'Your account is suspended.'], 403)
                : redirect()->route('login')->with('error', 'Your account is suspended.');
        }

        $userRole = $request->user()->role;

        if (in_array($userRole, $roles) || $userRole === 'super_admin') {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthorized.'], 403)
            : abort(403, 'Unauthorized action.');
    }
}
