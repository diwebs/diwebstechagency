<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\AcademyController;

/*
|--------------------------------------------------------------------------
| Diwebs Tech Agency - REST API Routes
|--------------------------------------------------------------------------
| These routes are prefixed with /api and are consumed via JSON.
| Used by the frontend Alpine.js components for dynamic interactions.
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'operational',
        'app' => config('app.name'),
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Authenticated API routes
Route::middleware('auth')->group(function () {

    // CBT Security event logger (called by Alpine JS exam engine)
    Route::post('/cbt/session/{sessionId}/log', [CbtController::class, 'logSecurityEvent'])
        ->name('api.cbt.log-event');

    // AI Academy Tutor
    Route::post('/academy/ask-ai', [AcademyController::class, 'askAiTutor'])
        ->name('api.academy.ask-ai');

    // Authenticated user info
    Route::get('/me', function () {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ]);
    });

});
