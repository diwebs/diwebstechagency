<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('staff')->check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('staff.login');
        }

        $staff = auth()->guard('staff')->user();

        if ($staff->status !== 'Active') {
            auth()->guard('staff')->logout();
            return $request->expectsJson()
                ? response()->json(['message' => 'Your staff account is suspended.'], 403)
                : redirect()->route('staff.login')->with('error', 'Your account has been suspended or resigned.');
        }

        return $next($request);
    }
}
