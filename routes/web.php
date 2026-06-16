<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;

// Public Static Corporate Pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');
Route::get('/case-studies', [PageController::class, 'caseStudies'])->name('case-studies');
Route::get('/blog', function() {
    return redirect()->route('news.index');
})->name('blog');
Route::get('/diwebs-news', [NewsController::class, 'index'])->name('news.index');
Route::get('/diwebs-news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact/submit', [PageController::class, 'submitLead'])->name('lead.submit');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Careers, Services Detail, and Legal Pages routes
Route::get('/careers', [PageController::class, 'careers'])->name('careers');
Route::get('/services/{slug}', [PageController::class, 'serviceDetail'])->name('services.detail');
Route::get('/legal/{slug}', [PageController::class, 'legal'])->name('legal.show');

// Web Onboarding/Installation Wizard Routes
Route::get('/install', [App\Http\Controllers\InstallController::class, 'showInstallForm'])->name('install.index');
Route::post('/install/database', [App\Http\Controllers\InstallController::class, 'setupDatabase'])->name('install.database');
Route::post('/install/admin', [App\Http\Controllers\InstallController::class, 'setupAdmin'])->name('install.admin');


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
    });

    // Super Admin Dashboard Routing (RBAC protected)
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::get('/referrals', [AdminController::class, 'referrals'])->name('referrals');
        Route::post('/referrals/{id}/pay', [AdminController::class, 'payReferralBonus'])->name('referrals.pay');
        Route::post('/referrals/{id}/status', [AdminController::class, 'updateReferralStatus'])->name('referrals.status');
        Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
        Route::get('/centers', [AdminController::class, 'centers'])->name('centers');
        Route::get('/security-logs', [AdminController::class, 'securityLogs'])->name('security-logs');
        
        // Projects Submodule
        Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
        Route::post('/projects/{id}/milestone/{milestoneId}/status', [AdminController::class, 'updateMilestoneStatus'])->name('projects.milestone.status');
        Route::post('/projects/{id}/validate', [AdminController::class, 'validateProject'])->name('projects.validate');
        Route::post('/projects/{id}/success-rate', [AdminController::class, 'updateSuccessRate'])->name('projects.success-rate');
        
        // Financial Operations Submodule
        Route::get('/finance', [AdminController::class, 'finance'])->name('finance');
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
        
        // Notifications Submodule
        Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/send', [AdminController::class, 'sendSystemNotification'])->name('notifications.send');
        Route::post('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AdminController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
        
        // Settings Submodule
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');

        // Payment Settings Submodule
        Route::get('/payment-settings', [AdminController::class, 'paymentSettings'])->name('payment-settings');
        Route::post('/payment-settings/update', [AdminController::class, 'updatePaymentSettings'])->name('payment-settings.update');

        // Extended Admin Portal Controls
        Route::get('/portal-control', [AdminController::class, 'portalControl'])->name('portal-control');
        Route::post('/portal-control/create-client', [AdminController::class, 'createClientAccount'])->name('portal-control.create-client');
        Route::post('/portal-control/send-proposal', [AdminController::class, 'sendProposal'])->name('portal-control.send-proposal');
        Route::get('/portal-control/export/{id}', [AdminController::class, 'exportClientReport'])->name('portal-control.export');

        // CBT Command Center Admin Controls
        Route::get('/cbt-command', [AdminController::class, 'cbtCommandCenter'])->name('cbt.command');
        Route::post('/cbt-command/enrollment/{id}/status', [AdminController::class, 'updateCenterEnrollmentStatus'])->name('cbt.enrollment.status');
        Route::post('/cbt-command/live-exam', [AdminController::class, 'storeLiveExamSchedule'])->name('cbt.live-exam.store');
    });

});

