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
        $isAuthenticated = false;
        try {
            $isAuthenticated = Auth::check();
        } catch (\Throwable $e) {
            // Silence query exceptions if database is unmigrated or tables (e.g., users) do not exist yet
        }

        if ($isAuthenticated) {
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
            $idleTimeoutMinutes = \App\Helpers\SettingsHelper::get('session_idle_timeout', 15);
            $idleLimit = $idleTimeoutMinutes * 60;
            $lastActivity = session('last_activity_time');

            if ($lastActivity && now()->timestamp - $lastActivity > $idleLimit) {
                $intendedUrl = $request->fullUrl();

                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                // Save in the new session so it is preserved across invalidation
                session(['session_expired_redirect_url' => $intendedUrl]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Session expired due to inactivity.',
                        'redirect' => route('login')
                    ], 401);
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
