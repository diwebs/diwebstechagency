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
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/dev/{role}', [AuthController::class, 'devLogin'])->name('auth.dev-login');

// Authenticated Routes Group
Route::middleware(['auth'])->group(function () {

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

