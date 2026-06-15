<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecurityTimeoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // 1. Session Rotation (rotate ID periodically e.g., every 5 minutes or on each authenticated view check)
            if (app()->environment() !== 'testing') {
                if (!session()->has('last_rotated_at') || now()->diffInMinutes(session('last_rotated_at')) >= 5) {
                    session()->regenerate();
                    session(['last_rotated_at' => now()]);
                }
            } else {
                session(['last_rotated_at' => now()]);
            }

            // 2. Idle Timeout check (Inactivity) - Configurable (Default 15 minutes = 900s)
            $idleLimit = config('session.idle_timeout', 900); 
            $lastActivity = session('last_activity_time');

            if ($lastActivity && now()->timestamp - $lastActivity > $idleLimit) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired due to inactivity.'], 401);
                }

                return redirect()->route('login')->with('error', 'Your session has expired due to inactivity.');
            }

            // Update activity time
            session(['last_activity_time' => now()->timestamp]);

            // 3. Absolute Session Expiration check (Force re-auth after 24h = 86400s)
            $absoluteLimit = config('session.absolute_timeout', 86400); 
            $sessionCreated = session('session_created_at');

            if (!$sessionCreated) {
                session(['session_created_at' => now()->timestamp]);
            } elseif (now()->timestamp - $sessionCreated > $absoluteLimit) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Absolute session lifetime exceeded. Please re-authenticate.'], 401);
                }

                return redirect()->route('login')->with('error', 'Security limit reached. Please log in again.');
            }
        }

        return $next($request);
    }
}
