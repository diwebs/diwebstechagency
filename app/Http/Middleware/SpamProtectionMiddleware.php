<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\SpamFilter;
use App\Models\SecurityLog;

class SpamProtectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only inspect POST / PUT / PATCH form requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            [$isSpam, $reason] = SpamFilter::check($request);

            if ($isSpam) {
                // Log security telemetry log
                try {
                    SecurityLog::create([
                        'user_id' => auth()->id(),
                        'event_type' => 'spam_attempt_blocked',
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string)$request->userAgent(), 0, 255),
                        'details' => json_encode([
                            'reason' => $reason,
                            'path' => $request->path(),
                            'inputs' => $request->except(['password', 'password_confirmation', '_token'])
                        ])
                    ]);
                } catch (\Exception $e) {
                    // Suppress log creation exception if table not present
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Security Filter Block: ' . $reason
                    ], 422);
                }

                return back()->withInput()->withErrors([
                    'spam' => 'Security Filter Block: ' . $reason
                ]);
            }
        }

        return $next($request);
    }
}
