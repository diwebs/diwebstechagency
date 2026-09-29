<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\StaffAdminController;
use App\Http\Controllers\StaffPortalController;
use Illuminate\Support\Facades\Route;

// Public Static Corporate Pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');
Route::get('/docs/{slug?}', [PageController::class, 'docs'])->name('docs');
Route::get('/case-studies', [PageController::class, 'caseStudies'])->name('case-studies');
Route::get('/blog', function() {
    return redirect()->route('news.index');
})->name('blog');
Route::get('/diwebs-news', [NewsController::class, 'index'])->name('news.index');
Route::get('/diwebs-news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact/submit', [PageController::class, 'submitLead'])->middleware(['spam.protect', 'throttle:6,1'])->name('lead.submit');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->middleware(['spam.protect', 'throttle:6,1'])->name('newsletter.subscribe');

// Careers, Services Detail, and Legal Pages routes
Route::get('/careers', [PageController::class, 'careers'])->name('careers');
Route::get('/services/{slug}', [PageController::class, 'serviceDetail'])->name('services.detail');
Route::get('/legal/{slug}', [PageController::class, 'legal'])->name('legal.show');

// Web Onboarding/Installation Wizard Routes
Route::get('/install', [App\Http\Controllers\InstallController::class, 'showInstallForm'])->name('install.index');
Route::post('/install/database', [App\Http\Controllers\InstallController::class, 'setupDatabase'])->name('install.database');
Route::post('/install/admin', [App\Http\Controllers\InstallController::class, 'setupAdmin'])->name('install.admin');

// Temporary Secure Database Migration Route (can be visited at: /run-migrations-securely?token=diwebs-secure-mig-2026)
Route::get('/run-migrations-securely', function(\Illuminate\Http\Request $request) {
    if ($request->get('token') !== 'diwebs-secure-mig-2026') {
        abort(403, 'Unauthorized');
    }
    try {
        $output = "";
        
        if ($request->get('migrate_sqlite') == '1') {
            \Illuminate\Support\Facades\Artisan::call('db:migrate-sqlite-to-mysql', [
                '--fresh' => $request->get('fresh') == '1',
                '--force' => true
            ]);
            $output .= "SQLite to MySQL Data Migration Output:<br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre><br>";
        } else {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output .= "Standard Migrations Output:<br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre><br>";
        }
        
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $output .= "View Cache Cleared.<br>";
        
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        $output .= "Application Cache Cleared.<br>";
        
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        $output .= "Config Cache Cleared.<br>";

        \Illuminate\Support\Facades\Artisan::call('route:clear');
        $output .= "Route Cache Cleared.<br>";
        
        return "System Maintenance completed successfully!<br><br>" . $output;
    } catch (\Exception $e) {
        return "Error running maintenance: " . $e->getMessage();
    }
});

// Temporary Secure Admin Login Recreation Route (can be visited at: /recreate-admin-securely?token=diwebs-secure-mig-2026)
Route::get('/recreate-admin-securely', function(\Illuminate\Http\Request $request) {
    if ($request->get('token') !== 'diwebs-secure-mig-2026') {
        abort(403, 'Unauthorized');
    }
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'AdminRecreateSeeder',
            '--force' => true
        ]);
        return "Admin account successfully recreated / reset!<br><strong>Email:</strong> admin@diwebstechagency.website<br><strong>Password:</strong> password";
    } catch (\Exception $e) {
        return "Error recreating admin: " . $e->getMessage();
    }
});

// Secure Log and Diagnostics Debugger (can be visited at: /debug-prod-errors?token=diwebs-secure-mig-2026)
Route::get('/debug-prod-errors', function(\Illuminate\Http\Request $request) {
    if ($request->get('token') !== 'diwebs-secure-mig-2026') {
        abort(403);
    }
    
    $logPath = storage_path('logs/laravel.log');
    $logContent = 'Log file does not exist';
    if (file_exists($logPath)) {
        $data = file($logPath);
        $line_count = count($data);
        $output = array_slice($data, max(0, $line_count - 100));
        $logContent = implode("", $output);
    }
    
    return response()->json([
        'portfolio_class_exists' => class_exists('App\Models\Portfolio'),
        'portfolios_table_exists' => \Illuminate\Support\Facades\Schema::hasTable('portfolios'),
        'db_connection' => config('database.default'),
        'db_database' => config('database.connections.' . config('database.default') . '.database'),
        'log_tail' => $logContent,
    ]);
});


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
Route::post('/register', [AuthController::class, 'register'])->middleware(['spam.protect', 'throttle:5,1']);
Route::post('/register/otp/send', [AuthController::class, 'sendRegistrationOtp'])->name('register.otp.send');
Route::post('/register/otp/verify', [AuthController::class, 'verifyRegistrationOtp'])->name('register.otp.verify');

// Passkeys authentication - Login (public, no auth required)
Route::post('/auth/passkeys/login-challenge', [AuthController::class, 'passkeyLoginChallenge']);
Route::post('/auth/passkeys/verify', [AuthController::class, 'passkeyVerify']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/dev/{role}', [AuthController::class, 'devLogin'])->name('auth.dev-login');

// OAuth / Social Login Redirect Routes (built dynamically from admin-configured credentials)
Route::get('/auth/google', function () {
    $clientId    = \App\Helpers\SettingsHelper::get('oauth_google_client_id', '');
    $redirectUri = \App\Helpers\SettingsHelper::get('oauth_google_redirect_uri', url('/auth/google/callback'));
    $enabled     = \App\Helpers\SettingsHelper::get('oauth_google_enabled', false);
    if (!$enabled || empty($clientId)) {
        return redirect()->route('login')->with('error', 'Google login is not configured yet. Please contact the administrator.');
    }
    $params = http_build_query([
        'client_id'     => $clientId,
        'redirect_uri'  => $redirectUri,
        'response_type' => 'code',
        'scope'         => 'openid email profile',
        'access_type'   => 'online',
        'state'         => csrf_token(),
        'prompt'        => 'select_account',
    ]);
    return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
})->name('auth.google');

Route::get('/auth/apple', function () {
    $clientId    = \App\Helpers\SettingsHelper::get('oauth_apple_client_id', '');
    $redirectUri = \App\Helpers\SettingsHelper::get('oauth_apple_redirect_uri', url('/auth/apple/callback'));
    $enabled     = \App\Helpers\SettingsHelper::get('oauth_apple_enabled', false);
    if (!$enabled || empty($clientId)) {
        return redirect()->route('login')->with('error', 'Apple login is not configured yet. Please contact the administrator.');
    }
    $params = http_build_query([
        'client_id'     => $clientId,
        'redirect_uri'  => $redirectUri,
        'response_type' => 'code',
        'response_mode' => 'form_post',
        'scope'         => 'name email',
        'state'         => csrf_token(),
    ]);
    return redirect('https://appleid.apple.com/auth/authorize?' . $params);
})->name('auth.apple');

Route::get('/auth/microsoft', function () {
    $clientId  = \App\Helpers\SettingsHelper::get('oauth_microsoft_client_id', '');
    $tenantId  = \App\Helpers\SettingsHelper::get('oauth_microsoft_tenant_id', 'common');
    $redirectUri = \App\Helpers\SettingsHelper::get('oauth_microsoft_redirect_uri', url('/auth/microsoft/callback'));
    $enabled   = \App\Helpers\SettingsHelper::get('oauth_microsoft_enabled', false);
    if (!$enabled || empty($clientId)) {
        return redirect()->route('login')->with('error', 'Microsoft Azure login is not configured yet. Please contact the administrator.');
    }
    $params = http_build_query([
        'client_id'     => $clientId,
        'redirect_uri'  => $redirectUri,
        'response_type' => 'code',
        'scope'         => 'openid email profile User.Read',
        'response_mode' => 'query',
        'state'         => csrf_token(),
    ]);
    return redirect("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?" . $params);
})->name('auth.microsoft');

// OAuth Callback placeholders (handle token exchange here when Socialite/custom handler is added)
Route::get('/auth/google/callback', function () {
    return redirect()->route('login')->with('error', 'Google OAuth callback received. Token exchange handler not yet implemented — add your Socialite logic here.');
})->name('auth.google.callback');
Route::get('/auth/apple/callback', function () {
    return redirect()->route('login')->with('error', 'Apple OAuth callback received. Token exchange handler not yet implemented.');
})->name('auth.apple.callback');
Route::get('/auth/microsoft/callback', function () {
    return redirect()->route('login')->with('error', 'Microsoft OAuth callback received. Token exchange handler not yet implemented.');
})->name('auth.microsoft.callback');

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
        // Dashboard
        Route::get('/', [CbtController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [CbtController::class, 'dashboard']);

        // Candidate Modules
        Route::get('/practice-tests', [CbtController::class, 'practiceTests'])->name('practice-tests');
        Route::get('/live-exams', [CbtController::class, 'liveExams'])->name('live-exams');
        Route::get('/exams', [CbtController::class, 'scheduledExams'])->name('exams');
        Route::get('/results', [CbtController::class, 'resultsHistory'])->name('results.history');
        Route::get('/certificates', [CbtController::class, 'certificatesList'])->name('certificates');
        Route::get('/sessions', [CbtController::class, 'sessionsList'])->name('sessions');
        Route::get('/notifications', [CbtController::class, 'notificationsFeed'])->name('notifications');
        Route::get('/profile', [CbtController::class, 'profileInfo'])->name('profile');

        Route::get('/certificates/{id}/download', [CbtController::class, 'downloadCertificate'])->name('certificate.download');
        Route::get('/live-exams/{id}/lobby', [CbtController::class, 'liveLobby'])->name('live-exams.lobby');
        Route::post('/live-exams/{id}/start', [CbtController::class, 'startLiveExam'])->name('live-exams.start');
        Route::post('/practice-tests/{id}/start', [CbtController::class, 'startPracticeExam'])->name('practice-tests.start');

        // Existing CBT flow
        Route::post('/exam/{examId}/start', [CbtController::class, 'startExam'])->name('exam.start');
        Route::get('/session/{sessionId}', [CbtController::class, 'showSession'])->name('exam.session');
        Route::post('/session/{sessionId}/log', [CbtController::class, 'logSecurityEvent'])->name('exam.log-event');
        Route::post('/session/{sessionId}/submit', [CbtController::class, 'submitExam'])->name('exam.submit');
        Route::get('/results/{sessionId}', [CbtController::class, 'showResults'])->name('results');

        // Partner / Institution Modules
        Route::get('/center-enrollment', [CbtController::class, 'centerEnrollment'])->name('center-enrollment');
        Route::post('/center-enrollment', [CbtController::class, 'storeCenterEnrollment'])->name('center-enrollment.store');
        Route::get('/cbt-centers', [CbtController::class, 'cbtCenters'])->name('cbt-centers');
        Route::get('/institution-management', [CbtController::class, 'institutionManagement'])->name('institution-management');
        Route::post('/institution-management/exam', [CbtController::class, 'storeInstitutionExam'])->name('institution.exam.store');
        Route::post('/institution-management/exam/{examId}/question', [CbtController::class, 'storeInstitutionQuestion'])->name('institution.question.store');

        // Extended Partner Screens
        Route::get('/partner/dashboard', [CbtController::class, 'partnerDashboard'])->name('partner.dashboard');
        Route::get('/partner/centers', [CbtController::class, 'partnerCenters'])->name('partner.centers');
        Route::get('/partner/centers/{centerId}/seats', [CbtController::class, 'partnerCenterSeats'])->name('partner.centers.seats');
        Route::get('/partner/candidates', [CbtController::class, 'partnerCandidates'])->name('partner.candidates');
        Route::post('/partner/candidates/{sessionId}/warn', [CbtController::class, 'partnerWarnCandidate'])->name('partner.candidates.warn');
        Route::post('/partner/candidates/{sessionId}/terminate', [CbtController::class, 'partnerTerminateCandidate'])->name('partner.candidates.terminate');
        Route::get('/partner/revenue', [CbtController::class, 'partnerRevenue'])->name('partner.revenue');
        Route::get('/partner/reports', [CbtController::class, 'partnerReports'])->name('partner.reports');
        Route::get('/partner/settings', [CbtController::class, 'partnerSettings'])->name('partner.settings');
    });

    // Academy LMS Portal Routing
    Route::prefix('academy')->name('academy.')->group(function () {
        Route::get('/', function() {
            return redirect()->route('academy.dashboard');
        });
        Route::get('/dashboard', [AcademyController::class, 'dashboard'])->name('dashboard');
        
        // Extended Modules
        Route::get('/courses', [AcademyController::class, 'courses'])->name('courses');
        Route::get('/audio-learning', [AcademyController::class, 'audioLearning'])->name('audio-learning');
        Route::get('/live-classes', [AcademyController::class, 'liveClasses'])->name('live-classes');
        Route::get('/mentorship', [AcademyController::class, 'mentorship'])->name('mentorship');
        Route::get('/sessions', [AcademyController::class, 'sessions'])->name('sessions');
        
        // Sidebar Submodules
        Route::get('/assignments', [AcademyController::class, 'assignments'])->name('assignments');
        Route::get('/certificates', [AcademyController::class, 'certificates'])->name('certificates');
        Route::get('/messages', [AcademyController::class, 'messages'])->name('messages');
        Route::get('/notifications', [AcademyController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [AcademyController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AcademyController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
        Route::get('/notifications/unread-count', [AcademyController::class, 'unreadNotifCount'])->name('notifications.count');
        Route::get('/settings', [AcademyController::class, 'settings'])->name('settings');

        // Extended API endpoints
        Route::post('/bookings/create', [AcademyController::class, 'bookSession'])->name('bookings.store');
        Route::post('/messages/send', [AcademyController::class, 'sendMessage'])->name('messages.send');
        Route::post('/ai/query', [AcademyController::class, 'askAcademyAi'])->name('ai.query');

        // Existing LMS flow
        Route::get('/course/{slug}', [AcademyController::class, 'courseDetail'])->name('course');
        Route::post('/course/{courseId}/enroll', [AcademyController::class, 'enroll'])->name('enroll');
        Route::get('/course/{courseSlug}/lesson/{lessonSlug}', [AcademyController::class, 'lessonDetail'])->name('lesson');
        Route::post('/ask-ai', [AcademyController::class, 'askAiTutor'])->name('ask-ai');
    });

    // Client Portal Routing
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/project/{id}', [PortalController::class, 'projectDetail'])->name('project');
        Route::post('/project/create', [PortalController::class, 'storeProject'])->name('project.store');
        Route::post('/project/{id}/upload', [PortalController::class, 'uploadFile'])->name('project.upload');
        Route::post('/project/{id}/sign', [PortalController::class, 'signAgreement'])->name('project.sign');
        Route::post('/invoice/{id}/pay', [PortalController::class, 'payInvoice'])->name('invoice.pay');
        
        // Extended Portal Actions
        Route::post('/service-request', [PortalController::class, 'storeServiceRequest'])->name('service-request.store');
        Route::get('/checkout/{id}', [PortalController::class, 'showCheckout'])->name('checkout');
        Route::post('/checkout/{id}/pay', [PortalController::class, 'processCheckoutPay'])->name('checkout.pay');
        Route::post('/milestone/{id}/action', [PortalController::class, 'milestoneAction'])->name('milestone.action');
        Route::post('/ticket/create', [PortalController::class, 'createTicket'])->name('ticket.create');
        Route::post('/chat/send', [PortalController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages', [PortalController::class, 'getMessages'])->name('chat.messages');
        Route::post('/team/invite', [PortalController::class, 'inviteTeamMember'])->name('team.invite');
        Route::post('/team/member/{id}/delete', [PortalController::class, 'removeTeamMember'])->name('team.remove');
        Route::post('/settings/update', [PortalController::class, 'updateSettings'])->name('settings.update');
        Route::post('/ai/chat', [PortalController::class, 'askAiAssistant'])->name('ai.chat');
        Route::get('/file/{id}/download', [PortalController::class, 'downloadFile'])->name('file.download');
        Route::post('/review/store', [PortalController::class, 'storeReview'])->name('review.store');
        Route::post('/partnership/apply', [PortalController::class, 'submitPartnershipRequest'])->name('partnership.apply');
    });

    // Super Admin Dashboard Routing (RBAC protected)
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/telemetry', [AdminController::class, 'getTelemetryData'])->name('telemetry');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::post('/users/{id}/change-password', [AdminController::class, 'changeUserPassword'])->name('users.change-password');
        Route::post('/users/{id}/update-region', [AdminController::class, 'updateUserRegion'])->name('users.update-region');
        Route::get('/referrals', [AdminController::class, 'referrals'])->name('referrals');
        Route::post('/referrals/{id}/pay', [AdminController::class, 'payReferralBonus'])->name('referrals.pay');
        Route::post('/referrals/{id}/status', [AdminController::class, 'updateReferralStatus'])->name('referrals.status');
        Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
        Route::get('/centers', [AdminController::class, 'centers'])->name('centers');
        Route::post('/centers/store', [AdminController::class, 'storeCenter'])->name('centers.store');
        Route::get('/security-logs', [AdminController::class, 'securityLogs'])->name('security-logs');
        
        // Projects Submodule
        Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
        Route::post('/projects/{id}/delete', [AdminController::class, 'deleteProject'])->name('projects.delete');
        Route::post('/projects/{id}/milestone/{milestoneId}/status', [AdminController::class, 'updateMilestoneStatus'])->name('projects.milestone.status');
        Route::post('/projects/{id}/validate', [AdminController::class, 'validateProject'])->name('projects.validate');
        Route::post('/projects/{id}/success-rate', [AdminController::class, 'updateSuccessRate'])->name('projects.success-rate');
        Route::post('/projects/{id}/update-note', [AdminController::class, 'updateProjectNote'])->name('projects.update-note');
        Route::post('/projects/{id}/assign', [AdminController::class, 'assignStaff'])->name('projects.assign');
        Route::post('/projects/{id}/assign/{assignmentId}/remove', [AdminController::class, 'removeStaffAssignment'])->name('projects.remove-assignment');
        
        // Portfolios Showcase Submodule
        Route::get('/portfolios', [AdminController::class, 'portfolios'])->name('portfolios');
        Route::get('/portfolios/create', [AdminController::class, 'createPortfolio'])->name('portfolios.create');
        Route::post('/portfolios/store', [AdminController::class, 'storePortfolio'])->name('portfolios.store');
        Route::post('/portfolios/capture-screenshot', [AdminController::class, 'capturePortfolioScreenshot'])->name('portfolios.capture-screenshot');
        Route::get('/portfolios/{id}/edit', [AdminController::class, 'editPortfolio'])->name('portfolios.edit');
        Route::post('/portfolios/{id}/update', [AdminController::class, 'updatePortfolio'])->name('portfolios.update');
        Route::post('/portfolios/{id}/delete', [AdminController::class, 'deletePortfolio'])->name('portfolios.delete');
        
        // Financial Operations Submodule
        Route::get('/finance', [AdminController::class, 'finance'])->name('finance');
        Route::post('/finance/invoice/store', [AdminController::class, 'storeInvoice'])->name('finance.invoice.store');
        Route::post('/finance/invoice/{id}/status', [AdminController::class, 'updateInvoiceStatus'])->name('finance.invoice.status');
        
        // LMS Academic Courses Submodule
        Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
        Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
        Route::post('/courses/store', [AdminController::class, 'storeCourse'])->name('courses.store');
        Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
        Route::post('/courses/{id}/update', [AdminController::class, 'updateCourse'])->name('courses.update');
        Route::post('/courses/{id}/delete', [AdminController::class, 'deleteCourse'])->name('courses.delete');
        Route::post('/courses/{courseId}/lessons/store', [AdminController::class, 'storeLesson'])->name('courses.lessons.store');
        Route::post('/courses/{courseId}/lessons/{lessonId}/delete', [AdminController::class, 'deleteLesson'])->name('courses.lessons.delete');

        // Academy Live Sessions & Teachers
        Route::get('/academy-live-sessions', [AdminController::class, 'academyLiveSessions'])->name('academy.live-sessions');
        Route::post('/academy-live-sessions/store', [AdminController::class, 'storeLiveSession'])->name('academy.live-sessions.store');
        Route::post('/academy-live-sessions/{id}/status', [AdminController::class, 'updateLiveSessionStatus'])->name('academy.live-sessions.status');
        
        Route::get('/academy-teachers', [AdminController::class, 'academyTeachers'])->name('academy.teachers');
        Route::post('/academy-teachers/store', [AdminController::class, 'storeTeacher'])->name('academy.teachers.store');
        
        // AI Prompts Settings Submodule
        Route::get('/ai', [AdminController::class, 'aiSettings'])->name('ai');
        Route::post('/ai/update', [AdminController::class, 'updateAiSettings'])->name('ai.update');
        
        // CRM Leads Submodule
        Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
        Route::post('/leads/{id}/status', [AdminController::class, 'updateLeadStatus'])->name('leads.status');
        
        // News Articles Editor & Crud Submodule
        Route::get('/news', [AdminController::class, 'news'])->name('news');
        Route::get('/news/create', [AdminController::class, 'createArticle'])->name('news.create');
        Route::post('/news/store', [AdminController::class, 'storeArticle'])->name('news.store');
        Route::get('/news/{id}/edit', [AdminController::class, 'editArticle'])->name('news.edit');
        Route::post('/news/{id}/update', [AdminController::class, 'updateArticle'])->name('news.update');
        Route::post('/news/{id}/delete', [AdminController::class, 'deleteArticle'])->name('news.delete');
        
        // Support Submodule
        Route::get('/support', [AdminController::class, 'support'])->name('support');
        Route::post('/support/ticket/{id}/status', [AdminController::class, 'updateTicketStatus'])->name('support.ticket.status');
        
        // Passkeys - Registration (authenticated user must be logged in)
    Route::post('/auth/passkeys/register-challenge', [AuthController::class, 'passkeyRegisterChallenge'])->name('passkeys.register.challenge');
    Route::post('/auth/passkeys/register-verify', [AuthController::class, 'passkeyRegisterVerify'])->name('passkeys.register.verify');
    Route::delete('/auth/passkeys/{id}', [AuthController::class, 'passkeyDelete'])->name('passkeys.delete');

    // Notifications Submodule
        Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/send', [AdminController::class, 'sendSystemNotification'])->name('notifications.send');
        Route::post('/notifications/send-email', [AdminController::class, 'sendDirectEmail'])->name('notifications.send-email');
        Route::post('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AdminController::class, 'markAllNotificationsRead'])->name('notifications.read-all');

        // Academy Plans Submodule
        Route::get('/academy-plans', [AdminController::class, 'academyPlans'])->name('academy-plans');
        Route::post('/academy-plans/store', [AdminController::class, 'storeAcademyPlan'])->name('academy-plans.store');
        Route::post('/academy-plans/{id}/cancel', [AdminController::class, 'cancelAcademyPlan'])->name('academy-plans.cancel');
        Route::delete('/academy-plans/{id}/delete', [AdminController::class, 'deleteAcademyPlan'])->name('academy-plans.delete');
        
        // Settings Submodule
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/seo', [AdminController::class, 'updateSeoSettings'])->name('settings.seo.update');
        Route::post('/settings/mail', [AdminController::class, 'updateMailSettings'])->name('settings.mail.update');
        Route::post('/settings/mail/test', [AdminController::class, 'sendTestEmail'])->name('settings.mail.test');
        Route::post('/settings/oauth', [AdminController::class, 'updateOAuthSettings'])->name('settings.oauth.update');
        Route::post('/settings/clear-cache', [AdminController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('/settings/optimize-db', [AdminController::class, 'optimizeDatabase'])->name('settings.optimize-db');
        Route::post('/settings/run-migrations', [AdminController::class, 'runMigrations'])->name('settings.run-migrations');
        Route::post('/settings/flush-sessions', [AdminController::class, 'flushSessions'])->name('settings.flush-sessions');
        Route::post('/settings/purge-old-data', [AdminController::class, 'purgeOldData'])->name('settings.purge-old-data');

        // Payment Settings Submodule
        Route::get('/payment-settings', [AdminController::class, 'paymentSettings'])->name('payment-settings');
        Route::post('/payment-settings/update', [AdminController::class, 'updatePaymentSettings'])->name('payment-settings.update');
        Route::post('/service-request/{id}/approve-payment', [AdminController::class, 'approveServiceRequestPayment'])->name('service-request.approve-payment');
        Route::post('/service-request/{id}/reject-payment', [AdminController::class, 'rejectServiceRequestPayment'])->name('service-request.reject-payment');

        // Customer Reviews Submodule
        Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/{id}/approve', [AdminController::class, 'approveReview'])->name('reviews.approve');
        Route::post('/reviews/{id}/delete', [AdminController::class, 'deleteReview'])->name('reviews.delete');


        // Extended Admin Portal Controls
        Route::get('/portal-control', [AdminController::class, 'portalControl'])->name('portal-control');
        Route::post('/portal-control/create-client', [AdminController::class, 'createClientAccount'])->name('portal-control.create-client');
        Route::post('/portal-control/send-proposal', [AdminController::class, 'sendProposal'])->name('portal-control.send-proposal');
        Route::get('/portal-control/export/{id}', [AdminController::class, 'exportClientReport'])->name('portal-control.export');
        Route::post('/portal-control/partnership/{id}/approve', [AdminController::class, 'approvePartnership'])->name('portal-control.partnership.approve');
        Route::post('/portal-control/partnership/{id}/decline', [AdminController::class, 'declinePartnership'])->name('portal-control.partnership.decline');

        // CBT Command Center Admin Controls
        Route::get('/cbt-command', [AdminController::class, 'cbtCommandCenter'])->name('cbt.command');
        Route::post('/cbt-command/enrollment/{id}/status', [AdminController::class, 'updateCenterEnrollmentStatus'])->name('cbt.enrollment.status');
        Route::post('/cbt-command/live-exam', [AdminController::class, 'storeLiveExamSchedule'])->name('cbt.live-exam.store');

        // Staff & HRMS Modules
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/', [StaffAdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/dashboard', [StaffAdminController::class, 'dashboard']);
            Route::get('/index', [StaffAdminController::class, 'index'])->name('index');
            Route::get('/create', [StaffAdminController::class, 'create'])->name('create');
            Route::post('/store', [StaffAdminController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StaffAdminController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [StaffAdminController::class, 'update'])->name('update');
            Route::post('/{id}/delete', [StaffAdminController::class, 'destroy'])->name('delete');
            Route::get('/{id}/profile', [StaffAdminController::class, 'profile'])->name('profile');

            // Departments & Roles
            Route::get('/departments', [StaffAdminController::class, 'departments'])->name('departments');
            Route::post('/departments/store', [StaffAdminController::class, 'storeDepartment'])->name('departments.store');
            Route::post('/departments/{id}/update', [StaffAdminController::class, 'updateDepartment'])->name('departments.update');
            Route::post('/departments/{id}/delete', [StaffAdminController::class, 'deleteDepartment'])->name('departments.delete');
            Route::post('/roles/store', [StaffAdminController::class, 'storeRole'])->name('roles.store');
            Route::post('/roles/{id}/delete', [StaffAdminController::class, 'deleteRole'])->name('roles.delete');
            Route::get('/ajax-roles/{departmentId}', [StaffAdminController::class, 'getRolesByDepartment'])->name('ajax-roles');

            // Roles & Permissions
            Route::get('/rbac', [StaffAdminController::class, 'rbac'])->name('rbac');
            Route::match(['get', 'post'], '/rbac/update', [StaffAdminController::class, 'updateRbac'])->name('rbac.update');


            // Attendance Logs
            Route::get('/attendance', [StaffAdminController::class, 'attendance'])->name('attendance');

            // Payroll & Payslips
            Route::get('/payroll', [StaffAdminController::class, 'payroll'])->name('payroll');
            Route::post('/payroll/run', [StaffAdminController::class, 'runPayroll'])->name('payroll.run');
            Route::post('/payroll/update/{id}', [StaffAdminController::class, 'updatePayrollItem'])->name('payroll.update');

            // Performance Reviews
            Route::get('/performance', [StaffAdminController::class, 'performance'])->name('performance');
            Route::post('/performance/store', [StaffAdminController::class, 'storePerformance'])->name('performance.store');

            // Leave Management
            Route::get('/leaves', [StaffAdminController::class, 'leaves'])->name('leaves');
            Route::post('/leaves/{id}/approve', [StaffAdminController::class, 'approveLeave'])->name('leaves.approve');
            Route::post('/leaves/reject/{id}', [StaffAdminController::class, 'rejectLeave'])->name('leaves.reject');

            // Staff Activity Logs
            Route::get('/activity-logs', [StaffAdminController::class, 'activityLogs'])->name('activity-logs');
        });
    });

});

// CRM System Routes (shared between Super Admin & Staff)
Route::middleware([\App\Http\Middleware\CrmAuthMiddleware::class])->prefix('admin/crm')->name('admin.crm.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CrmController::class, 'dashboard'])->name('dashboard');
    Route::get('/leads', [\App\Http\Controllers\CrmController::class, 'leads'])->name('leads');
    Route::post('/leads', [\App\Http\Controllers\CrmController::class, 'storeLead'])->name('leads.store');
    Route::post('/leads/{id}/convert', [\App\Http\Controllers\CrmController::class, 'convertToClient'])->name('leads.convert');
    Route::post('/leads/{id}/delete', [\App\Http\Controllers\CrmController::class, 'deleteLead'])->name('leads.delete');
    
    Route::get('/prospects', [\App\Http\Controllers\CrmController::class, 'prospects'])->name('prospects');
    Route::post('/prospects/{id}/update', [\App\Http\Controllers\CrmController::class, 'updateProspect'])->name('prospects.update');
    
    Route::get('/clients', [\App\Http\Controllers\CrmController::class, 'clients'])->name('clients');
    Route::post('/clients', [\App\Http\Controllers\CrmController::class, 'storeClient'])->name('clients.store');
    Route::post('/clients/{id}/update', [\App\Http\Controllers\CrmController::class, 'updateClient'])->name('clients.update');
    
    Route::get('/companies', [\App\Http\Controllers\CrmController::class, 'companies'])->name('companies');
    Route::post('/companies', [\App\Http\Controllers\CrmController::class, 'storeCompany'])->name('companies.store');
    
    Route::get('/deals', [\App\Http\Controllers\CrmController::class, 'deals'])->name('deals');
    Route::post('/deals', [\App\Http\Controllers\CrmController::class, 'storeDeal'])->name('deals.store');
    Route::post('/deals/{id}/stage', [\App\Http\Controllers\CrmController::class, 'updateDealStage'])->name('deals.stage');
    
    Route::get('/tasks', [\App\Http\Controllers\CrmController::class, 'tasks'])->name('tasks');
    Route::post('/tasks', [\App\Http\Controllers\CrmController::class, 'storeTask'])->name('tasks.store');
    Route::post('/tasks/{id}/status', [\App\Http\Controllers\CrmController::class, 'updateTaskStatus'])->name('tasks.status');
    
    Route::get('/meetings', [\App\Http\Controllers\CrmController::class, 'meetings'])->name('meetings');
    Route::post('/meetings', [\App\Http\Controllers\CrmController::class, 'storeMeeting'])->name('meetings.store');
    
    Route::get('/communications', [\App\Http\Controllers\CrmController::class, 'communications'])->name('communications');
    Route::post('/communications', [\App\Http\Controllers\CrmController::class, 'storeCommunication'])->name('communications.store');
    
    Route::get('/contracts', [\App\Http\Controllers\CrmController::class, 'contracts'])->name('contracts');
    Route::post('/contracts', [\App\Http\Controllers\CrmController::class, 'storeContract'])->name('contracts.store');
    Route::post('/contracts/{id}/sign', [\App\Http\Controllers\CrmController::class, 'signContract'])->name('contracts.sign');
    
    Route::get('/invoices', [\App\Http\Controllers\CrmController::class, 'invoices'])->name('invoices');
    Route::post('/invoices', [\App\Http\Controllers\CrmController::class, 'storeInvoice'])->name('invoices.store');
    Route::post('/invoices/{id}/status', [\App\Http\Controllers\CrmController::class, 'updateInvoiceStatus'])->name('invoices.status');
    
    Route::get('/tickets', [\App\Http\Controllers\CrmController::class, 'tickets'])->name('tickets');
    Route::post('/tickets', [\App\Http\Controllers\CrmController::class, 'storeTicket'])->name('tickets.store');
    Route::post('/tickets/{id}/status', [\App\Http\Controllers\CrmController::class, 'updateTicketStatus'])->name('tickets.status');
    
    Route::get('/campaigns', [\App\Http\Controllers\CrmController::class, 'campaigns'])->name('campaigns');
    Route::post('/campaigns', [\App\Http\Controllers\CrmController::class, 'storeCampaign'])->name('campaigns.store');
    
    Route::get('/reports', [\App\Http\Controllers\CrmController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\CrmController::class, 'exportReport'])->name('reports.export');
    
    Route::get('/settings', [\App\Http\Controllers\CrmController::class, 'settings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\CrmController::class, 'updateSettings'])->name('settings.update');
});

// Isolated Staff Portal Routes (Guest & Authenticated)
Route::prefix('staff')->name('staff.')->group(function () {
    // Guest Routes
    Route::get('/login', [StaffPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [StaffPortalController::class, 'login'])->name('login.post');
    Route::get('/login/2fa', [StaffPortalController::class, 'show2fa'])->name('login.2fa');
    Route::post('/login/2fa', [StaffPortalController::class, 'verify2fa'])->name('login.2fa.post');

    // Authenticated Workspace Routes
    Route::middleware(['auth.staff'])->group(function () {
        Route::get('/dashboard', [StaffPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/password-change', [StaffPortalController::class, 'showPasswordChange'])->name('password.change');
        Route::post('/password-change', [StaffPortalController::class, 'changePassword'])->name('password.change.post');

        // Personal Profile Settings - 2FA Toggle
        Route::post('/2fa/enable', [StaffPortalController::class, 'enable2fa'])->name('2fa.enable');
        Route::post('/2fa/disable', [StaffPortalController::class, 'disable2fa'])->name('2fa.disable');

        // Attendance Clock Action
        Route::post('/attendance/clock', [StaffPortalController::class, 'clockInOut'])->name('attendance.clock');

        // Feature Submodules
        Route::get('/projects', [StaffPortalController::class, 'projects'])->name('projects');
        Route::get('/attendance', [StaffPortalController::class, 'attendance'])->name('attendance');
        Route::get('/payroll', [StaffPortalController::class, 'payroll'])->name('payroll');
        Route::get('/payroll/{id}/download', [StaffPortalController::class, 'downloadPayslip'])->name('payroll.download');
        Route::get('/leaves', [StaffPortalController::class, 'leaves'])->name('leaves');
        Route::post('/leaves/apply', [StaffPortalController::class, 'applyLeave'])->name('leaves.apply');
        Route::get('/performance', [StaffPortalController::class, 'performance'])->name('performance');
        Route::get('/academy', [StaffPortalController::class, 'academy'])->name('academy');
        Route::get('/messages', [StaffPortalController::class, 'messages'])->name('messages');

        // Log out
        Route::post('/logout', [StaffPortalController::class, 'logout'])->name('logout');
    });
});

