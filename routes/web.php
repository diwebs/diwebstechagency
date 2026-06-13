<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public Static Corporate Pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');
Route::get('/case-studies', [PageController::class, 'caseStudies'])->name('case-studies');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact/submit', [PageController::class, 'submitLead'])->name('lead.submit');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/2fa/verify', [AuthController::class, 'verifyLogin2FA'])->name('login.2fa.verify');

// Hidden Custom Admin Login Gate
Route::get('/secure-gate-admin', [AuthController::class, 'showAdminLogin'])->name('admin.login-gate');

// Password Reset Routes
Route::get('/password/reset', [AuthController::class, 'showResetRequest'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Registration & OTP flow
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/otp/send', [AuthController::class, 'sendRegistrationOtp'])->name('register.otp.send');
Route::post('/register/otp/verify', [AuthController::class, 'verifyRegistrationOtp'])->name('register.otp.verify');

// Passkeys authentication challenge
Route::post('/auth/passkeys/login-challenge', [AuthController::class, 'passkeyLoginChallenge']);
Route::post('/auth/passkeys/verify', [AuthController::class, 'passkeyVerify']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/dev/{role}', [AuthController::class, 'devLogin'])->name('auth.dev-login');

// Authenticated Routes Group
Route::middleware(['auth'])->group(function () {

    // Unified Profile Security Settings
    Route::get('/profile/security', [\App\Http\Controllers\SecurityController::class, 'showSecuritySettings'])->name('profile.security');
    Route::post('/profile/2fa/enable', [\App\Http\Controllers\SecurityController::class, 'enable2fa'])->name('profile.2fa.enable');
    Route::post('/profile/2fa/disable', [\App\Http\Controllers\SecurityController::class, 'disable2fa'])->name('profile.2fa.disable');
    Route::post('/profile/passkeys/challenge', [\App\Http\Controllers\SecurityController::class, 'passkeyChallenge']);
    Route::post('/profile/passkeys/store', [\App\Http\Controllers\SecurityController::class, 'passkeyStore']);
    Route::post('/profile/passkeys/{id}/delete', [\App\Http\Controllers\SecurityController::class, 'passkeyDelete']);
    Route::post('/profile/devices/{id}/revoke', [\App\Http\Controllers\SecurityController::class, 'revokeSession']);
    Route::post('/profile/devices/revoke-all', [\App\Http\Controllers\SecurityController::class, 'revokeAllOtherSessions']);
    Route::post('/profile/password/update', [\App\Http\Controllers\SecurityController::class, 'updatePassword'])->name('profile.password.update');

    // CBT Candidate Portal Routing
    Route::prefix('cbt')->name('cbt.')->group(function () {
        Route::get('/', function () {
            // Mock CBT dashboard
            $exams = \App\Models\Exam::where('is_active', true)->get();
            $sessions = \App\Models\ExamSession::with('exam')->where('user_id', auth()->id())->get();
            return view('cbt.dashboard', compact('exams', 'sessions'));
        })->name('dashboard');

        Route::post('/exam/{examId}/start', [CbtController::class, 'startExam'])->name('exam.start');
        Route::get('/session/{sessionId}', [CbtController::class, 'showSession'])->name('exam.session');
        Route::post('/session/{sessionId}/log', [CbtController::class, 'logSecurityEvent'])->name('exam.log-event');
        Route::post('/session/{sessionId}/submit', [CbtController::class, 'submitExam'])->name('exam.submit');
        Route::get('/results/{sessionId}', [CbtController::class, 'showResults'])->name('results');
    });

    // Academy LMS Portal Routing
    Route::prefix('academy')->name('academy.')->group(function () {
        Route::get('/', [AcademyController::class, 'dashboard'])->name('dashboard');
        Route::get('/course/{slug}', [AcademyController::class, 'courseDetail'])->name('course');
        Route::post('/course/{courseId}/enroll', [AcademyController::class, 'enroll'])->name('enroll');
        Route::get('/course/{courseSlug}/lesson/{lessonSlug}', [AcademyController::class, 'lessonDetail'])->name('lesson');
        Route::post('/ask-ai', [AcademyController::class, 'askAiTutor'])->name('ask-ai');
    });

    // Client Portal Routing
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/project/{id}', [PortalController::class, 'projectDetail'])->name('project');
        Route::post('/project/{id}/upload', [PortalController::class, 'uploadFile'])->name('project.upload');
        Route::post('/project/{id}/sign', [PortalController::class, 'signAgreement'])->name('project.sign');
        Route::post('/invoice/{id}/pay', [PortalController::class, 'payInvoice'])->name('invoice.pay');
    });

    // Super Admin Dashboard Routing (RBAC protected)
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
        Route::get('/centers', [AdminController::class, 'centers'])->name('centers');
        Route::get('/security-logs', [AdminController::class, 'securityLogs'])->name('security-logs');
    });

});

