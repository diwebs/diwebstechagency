<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrmAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if super admin is authenticated via default guard
        if (auth()->check() && auth()->user()->role === 'super_admin') {
            if (auth()->user()->status !== 'active') {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your account is suspended.');
            }
            return $next($request);
        }

        // 2. Check if staff is authenticated via staff guard
        if (auth()->guard('staff')->check()) {
            $staff = auth()->guard('staff')->user();
            if ($staff->status !== 'Active') {
                auth()->guard('staff')->logout();
                return redirect()->route('staff.login')->with('error', 'Your staff account is suspended.');
            }
            return $next($request);
        }

        // 3. Fallback: Unauthenticated
        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthenticated.'], 401)
            : redirect()->route('login')->with('error', 'Please authenticate to access the CRM system.');
    }
}
