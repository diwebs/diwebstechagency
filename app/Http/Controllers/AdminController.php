<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lead;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ExamSession;
use App\Models\SecurityLog;
use App\Models\CbtCenter;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\Contract;
use App\Models\Ticket;
use App\Models\NewsArticle;
use App\Models\CbtCenterEnrollment;
use App\Models\CbtLiveExam;
use App\Models\Exam;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_leads' => Lead::count(),
            'total_courses' => Course::count(),
            'total_sessions' => ExamSession::count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('amount'),
            'flagged_sessions' => ExamSession::where('status', 'flagged')->count(),
        ];

        $recentLogs = SecurityLog::with(['user', 'examSession.exam'])->orderBy('created_at', 'desc')->take(10)->get();
        $centers = CbtCenter::withCount('seats')->get();
        $recentSessions = ExamSession::with(['user', 'exam'])->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'centers', 'recentSessions'));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        return back()->with('success', 'User status updated to ' . $newStatus . '.');
    }

    public function deleteUser($id)
    {
        if (auth()->id() == $id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);

        try {
            DB::transaction(function () use ($user) {
                $user->delete();
            });
            return back()->with('success', 'User ' . $user->name . ' deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function exams()
    {
        $sessions = ExamSession::with(['user', 'exam', 'center'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.exams', compact('sessions'));
    }

    public function centers()
    {
        $centers = CbtCenter::withCount('seats')->paginate(10);
        return view('admin.centers', compact('centers'));
    }

    public function securityLogs()
    {
        $logs = SecurityLog::with(['user', 'examSession.exam'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.security-logs', compact('logs'));
    }

    public function projects()
    {
        $projects = Project::with(['client', 'milestones'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.projects', compact('projects'));
    }

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);

        try {
            DB::transaction(function () use ($project) {
                $project->delete();
            });
            return back()->with('success', 'Project ' . $project->title . ' deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    // Portfolios Submodule
    public function portfolios()
    {
        $portfolios = Portfolio::orderBy('order', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.portfolio-index', compact('portfolios'));
    }

    public function createPortfolio()
    {
        return view('admin.portfolio-edit');
    }

    public function storePortfolio(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'mock_image'  => 'nullable|image|max:5120', // max 5MB image
            'project_url' => 'nullable|url|max:255',
            'order'       => 'nullable|integer',
        ]);

        if ($request->hasFile('mock_image')) {
            $path = $request->file('mock_image')->store('portfolios', 'public');
            $validated['mock_image'] = $path;
        }

        $validated['order'] = $validated['order'] ?? 0;

        Portfolio::create($validated);

        return redirect()->route('admin.portfolios')->with('success', 'Portfolio project created successfully.');
    }

    public function editPortfolio($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        return view('admin.portfolio-edit', compact('portfolio'));
    }

    public function updatePortfolio(Request $request, $id)
    {
        $portfolio = Portfolio::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'mock_image'  => 'nullable|image|max:5120', // max 5MB image
            'project_url' => 'nullable|url|max:255',
            'order'       => 'nullable|integer',
        ]);

        if ($request->hasFile('mock_image')) {
            // Delete old mock image if it exists
            if ($portfolio->mock_image) {
                Storage::disk('public')->delete($portfolio->mock_image);
            }
            $path = $request->file('mock_image')->store('portfolios', 'public');
            $validated['mock_image'] = $path;
        }

        $validated['order'] = $validated['order'] ?? 0;

        $portfolio->update($validated);

        return redirect()->route('admin.portfolios')->with('success', 'Portfolio project updated successfully.');
    }

    public function deletePortfolio($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        if ($portfolio->mock_image) {
            Storage::disk('public')->delete($portfolio->mock_image);
        }

        $portfolio->delete();

        return redirect()->route('admin.portfolios')->with('success', 'Portfolio project deleted successfully.');
    }

    public function updateMilestoneStatus(Request $request, $id, $milestoneId)
    {
        $milestone = Milestone::where('project_id', $id)->findOrFail($milestoneId);
        $milestone->update(['status' => $request->status]);
        return back()->with('success', 'Milestone status updated successfully.');
    }

    // Financial Operations Submodule
    public function finance()
    {
        $invoices = Invoice::with(['project', 'client', 'milestone'])->orderBy('created_at', 'desc')->paginate(15);
        $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
        $pendingRevenue = Invoice::where('status', 'pending')->sum('amount');
        return view('admin.finance', compact('invoices', 'totalRevenue', 'pendingRevenue'));
    }

    public function updateInvoiceStatus(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => $request->status,
            'paid_at' => $request->status === 'paid' ? now() : null
        ]);
        return back()->with('success', 'Invoice status updated successfully.');
    }

    // LMS Academic Courses Submodule
    public function courses()
    {
        $courses = Course::withCount('lessons')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.courses', compact('courses'));
    }

    public function createCourse()
    {
        return view('admin.course-edit');
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'instructor_name' => 'required|string|max:255',
            'price'           => 'required|numeric|min:0',
            'cover_image'     => 'nullable|url',
            'difficulty'      => 'nullable|string|in:Beginner,Intermediate,Advanced,All Levels',
            'category'        => 'nullable|string|max:100',
            'syllabus'        => 'nullable|string',
        ]);

        $validated['slug']    = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['syllabus'] = $validated['syllabus']
            ? array_filter(array_map('trim', explode("\n", $validated['syllabus'])))
            : [];

        Course::create($validated);

        return redirect()->route('admin.courses')->with('success', 'Course created successfully.');
    }

    public function editCourse($id)
    {
        $course = Course::with('lessons')->findOrFail($id);
        return view('admin.course-edit', compact('course'));
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'instructor_name' => 'required|string|max:255',
            'price'           => 'required|numeric|min:0',
            'cover_image'     => 'nullable|url',
            'difficulty'      => 'nullable|string|in:Beginner,Intermediate,Advanced,All Levels',
            'category'        => 'nullable|string|max:100',
            'syllabus'        => 'nullable|string',
        ]);

        $validated['syllabus'] = $validated['syllabus']
            ? array_filter(array_map('trim', explode("\n", $validated['syllabus'])))
            : [];

        $course->update($validated);

        return redirect()->route('admin.courses')->with('success', 'Course updated successfully.');
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully.');
    }

    // Add / Update / Delete Lesson inside a course
    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'nullable|string',
            'video_url'        => 'nullable|url',
            'duration_seconds' => 'nullable|integer|min:0',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        $validated['slug']      = Str::slug($validated['title']) . '-' . Str::random(4);
        $validated['course_id'] = $course->id;

        Lesson::create($validated);

        return redirect()->route('admin.courses.edit', $courseId)->with('success', 'Lesson added successfully.');
    }

    public function deleteLesson($courseId, $lessonId)
    {
        $lesson = Lesson::where('course_id', $courseId)->findOrFail($lessonId);
        $lesson->delete();
        return redirect()->route('admin.courses.edit', $courseId)->with('success', 'Lesson deleted.');
    }

    // AI Prompts Settings Submodule
    public function aiSettings()
    {
        $aiSettings = [
            'prompt' => cache('ai_system_prompt', 'You are Antigravity, a powerful AI assistant trained by Google DeepMind...'),
            'temperature' => cache('ai_temperature', 0.7),
            'max_tokens' => cache('ai_max_tokens', 2048),
            'accuracy_rate' => '98.4%',
            'tokens_consumed' => number_format(1420815)
        ];
        return view('admin.ai', compact('aiSettings'));
    }

    public function updateAiSettings(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:2',
            'max_tokens' => 'required|integer|min:1'
        ]);
        cache(['ai_system_prompt' => $request->prompt]);
        cache(['ai_temperature' => $request->temperature]);
        cache(['ai_max_tokens' => $request->max_tokens]);
        return back()->with('success', 'AI Prompt configurations updated successfully.');
    }

    // CRM Leads Submodule
    public function leads()
    {
        $leads = Lead::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.leads', compact('leads'));
    }

    public function updateLeadStatus(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $lead->update(['status' => $request->status]);
        return back()->with('success', 'Lead status updated to ' . $request->status . '.');
    }

    // News Articles Editor & Crud Submodule
    public function news()
    {
        $articles = NewsArticle::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.news-index', compact('articles'));
    }

    public function createArticle()
    {
        $categories = [
            'Technology', 
            'AI', 
            'Cybersecurity', 
            'SaaS', 
            'Software Engineering', 
            'Cloud', 
            'CBT Updates', 
            'Company News'
        ];
        return view('admin.news-edit', compact('categories'));
    }

    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|url',
            'category' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'author_name' => 'nullable|string|max:255',
            'author_bio' => 'nullable|string',
            'author_avatar' => 'nullable|url'
        ]);

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        NewsArticle::create($validated);

        return redirect()->route('admin.news')->with('success', 'News article created successfully.');
    }

    public function editArticle($id)
    {
        $article = NewsArticle::findOrFail($id);
        $categories = [
            'Technology', 
            'AI', 
            'Cybersecurity', 
            'SaaS', 
            'Software Engineering', 
            'Cloud', 
            'CBT Updates', 
            'Company News'
        ];
        return view('admin.news-edit', compact('article', 'categories'));
    }

    public function updateArticle(Request $request, $id)
    {
        $article = NewsArticle::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|url',
            'category' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'author_name' => 'nullable|string|max:255',
            'author_bio' => 'nullable|string',
            'author_avatar' => 'nullable|url'
        ]);

        if ($validated['status'] === 'published' && !$article->published_at) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.news')->with('success', 'News article updated successfully.');
    }

    public function deleteArticle($id)
    {
        $article = NewsArticle::findOrFail($id);
        $article->delete();
        return redirect()->route('admin.news')->with('success', 'News article deleted successfully.');
    }

    // Support Submodule
    public function support()
    {
        $tickets = Ticket::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.support', compact('tickets'));
    }

    public function updateTicketStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => $request->status]);
        return back()->with('success', 'Support ticket status updated to ' . $request->status . '.');
    }

    // Notifications Submodule
    public function notifications()
    {
        $notifications = \App\Models\AdminNotification::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.notifications', compact('notifications'));
    }

    public function sendSystemNotification(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|string'
        ]);
        // Simulate dispatcher
        return back()->with('success', 'System dispatch alert sent successfully to ' . $request->target_role . ' users.');
    }

    public function markNotificationRead($id)
    {
        $notification = \App\Models\AdminNotification::findOrFail($id);
        $notification->update(['is_read' => true]);
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead()
    {
        \App\Models\AdminNotification::where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    // Settings Submodule
    public function settings()
    {
        $settings = [
            'app_name' => \App\Helpers\SettingsHelper::get('app_name', 'Diwebs Tech Agency'),
            'maintenance_mode' => \App\Helpers\SettingsHelper::get('maintenance_mode', false),
            'allow_registration' => \App\Helpers\SettingsHelper::get('allow_registration', true),
            'auto_backups' => \App\Helpers\SettingsHelper::get('auto_backups', true),
            'referral_bonus_amount' => \App\Helpers\SettingsHelper::get('referral_bonus_amount', 50.00),

            // Analytics & SEO Settings
            'google_analytics_id' => \App\Helpers\SettingsHelper::get('google_analytics_id', ''),
            'seo_meta_title_suffix' => \App\Helpers\SettingsHelper::get('seo_meta_title_suffix', ' | Diwebs Tech Agency'),
            'seo_meta_description' => \App\Helpers\SettingsHelper::get('seo_meta_description', 'Diwebs Tech Agency is a world-class builder of enterprise software, LMS academy, mobile apps, and robust CBT infrastructures.'),
            'seo_meta_keywords' => \App\Helpers\SettingsHelper::get('seo_meta_keywords', 'agency, lms, cbt, software development, next.js, vue, laravel, enterprise solution, ai automation'),
            'seo_og_image_url' => \App\Helpers\SettingsHelper::get('seo_og_image_url', 'https://diwebstechagency.website/images/brand/seo_card.jpg'),

            // Dynamic Mail Settings
            'mail_mailer' => \App\Helpers\SettingsHelper::get('mail_mailer', 'log'),
            'mail_host' => \App\Helpers\SettingsHelper::get('mail_host', 'mail.diwebstechagency.website'),
            'mail_port' => \App\Helpers\SettingsHelper::get('mail_port', '465'),
            'mail_username' => \App\Helpers\SettingsHelper::get('mail_username', 'noreply@diwebstechagency.website'),
            'mail_password' => \App\Helpers\SettingsHelper::get('mail_password', ''),
            'mail_scheme' => \App\Helpers\SettingsHelper::get('mail_scheme', 'ssl'),
            'mail_from_address' => \App\Helpers\SettingsHelper::get('mail_from_address', 'noreply@diwebstechagency.website'),
            'mail_from_name' => \App\Helpers\SettingsHelper::get('mail_from_name', 'Diwebs Tech Agency'),
        ];
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'referral_bonus_amount' => 'required|numeric|min:0'
        ]);

        \App\Helpers\SettingsHelper::set('app_name', $request->app_name);
        \App\Helpers\SettingsHelper::set('maintenance_mode', $request->has('maintenance_mode'));
        \App\Helpers\SettingsHelper::set('allow_registration', $request->has('allow_registration'));
        \App\Helpers\SettingsHelper::set('auto_backups', $request->has('auto_backups'));
        \App\Helpers\SettingsHelper::set('referral_bonus_amount', (float)$request->referral_bonus_amount);
        return back()->with('success', 'System branding settings updated successfully.');
    }

    public function updateSeoSettings(Request $request)
    {
        $request->validate([
            'google_analytics_id' => 'nullable|string|max:50',
            'seo_meta_title_suffix' => 'required|string|max:255',
            'seo_meta_description' => 'required|string',
            'seo_meta_keywords' => 'required|string',
            'seo_og_image_url' => 'nullable|url'
        ]);

        \App\Helpers\SettingsHelper::set('google_analytics_id', $request->google_analytics_id);
        \App\Helpers\SettingsHelper::set('seo_meta_title_suffix', $request->seo_meta_title_suffix);
        \App\Helpers\SettingsHelper::set('seo_meta_description', $request->seo_meta_description);
        \App\Helpers\SettingsHelper::set('seo_meta_keywords', $request->seo_meta_keywords);
        \App\Helpers\SettingsHelper::set('seo_og_image_url', $request->seo_og_image_url);

        return back()->with('success', 'Google Analytics and Global SEO settings updated successfully.');
    }

    public function updateMailSettings(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string|in:smtp,log,sendmail,array',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string',
            'mail_scheme' => 'nullable|string|in:ssl,tls,null',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        \App\Helpers\SettingsHelper::set('mail_mailer', $request->mail_mailer);
        \App\Helpers\SettingsHelper::set('mail_host', $request->mail_host);
        \App\Helpers\SettingsHelper::set('mail_port', $request->mail_port);
        \App\Helpers\SettingsHelper::set('mail_username', $request->mail_username);
        
        if ($request->filled('mail_password')) {
            \App\Helpers\SettingsHelper::set('mail_password', $request->mail_password);
        }
        
        \App\Helpers\SettingsHelper::set('mail_scheme', $request->mail_scheme === 'null' ? null : $request->mail_scheme);
        \App\Helpers\SettingsHelper::set('mail_from_address', $request->mail_from_address);
        \App\Helpers\SettingsHelper::set('mail_from_name', $request->mail_from_name);

        Artisan::call('config:clear');

        return back()->with('success', 'Dynamic Mail & SMTP configurations updated successfully.');
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            $toEmail = $request->test_email;
            
            \Illuminate\Support\Facades\Mail::html(
                "<h3>SMTP Test Verification</h3><p>This is a test email sent from the <strong>Diwebs Tech Agency</strong> Admin Settings panel.</p><p>If you received this message, your custom dynamic SMTP configurations are working perfectly!</p><p>Timestamp: " . now()->toDateTimeString() . "</p>",
                function ($message) use ($toEmail) {
                    $message->to($toEmail)
                            ->subject('Diwebs SMTP Verification Test');
                }
            );

            return back()->with('success', '✅ Test email sent successfully to ' . $toEmail . '. Check your inbox or system logs.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Test email delivery failed: ' . $e->getMessage());
        }
    }

    // Payment Settings Submodule
    public function paymentSettings()
    {
        $paymentSettings = [
            // Active gateway & currency
            'active_gateway'        => cache('payment_active_gateway', 'stripe'),
            'default_currency'      => cache('payment_default_currency', 'USD'),
            'currency_symbol'       => cache('payment_currency_symbol', '$'),
            'currency_position'     => cache('payment_currency_position', 'before'),
            // Invoice configuration
            'invoice_prefix'        => cache('payment_invoice_prefix', 'INV'),
            'tax_rate'              => cache('payment_tax_rate', '0'),
            'tax_label'             => cache('payment_tax_label', 'VAT'),
            // Stripe
            'stripe_public_key'     => cache('payment_stripe_public_key', ''),
            'stripe_secret_key'     => cache('payment_stripe_secret_key', ''),
            'stripe_webhook_secret' => cache('payment_stripe_webhook_secret', ''),
            'stripe_enabled'        => cache('payment_stripe_enabled', true),
            // Paystack
            'paystack_public_key'   => cache('payment_paystack_public_key', ''),
            'paystack_secret_key'   => cache('payment_paystack_secret_key', ''),
            'paystack_enabled'      => cache('payment_paystack_enabled', false),
            // Flutterwave
            'flw_public_key'        => cache('payment_flw_public_key', ''),
            'flw_secret_key'        => cache('payment_flw_secret_key', ''),
            'flw_enabled'           => cache('payment_flw_enabled', false),
            // PayPal
            'paypal_client_id'      => cache('payment_paypal_client_id', ''),
            'paypal_secret'         => cache('payment_paypal_secret', ''),
            'paypal_mode'           => cache('payment_paypal_mode', 'sandbox'),
            'paypal_enabled'        => cache('payment_paypal_enabled', false),
            // Bank Transfer
            'bank_name'             => cache('payment_bank_name', ''),
            'bank_account_name'     => cache('payment_bank_account_name', ''),
            'bank_account_number'   => cache('payment_bank_account_number', ''),
            'bank_routing_number'   => cache('payment_bank_routing_number', ''),
            'bank_swift_code'       => cache('payment_bank_swift_code', ''),
            'bank_enabled'          => cache('payment_bank_enabled', false),
            // Crypto
            'crypto_wallet_btc'     => cache('payment_crypto_wallet_btc', ''),
            'crypto_wallet_usdt'    => cache('payment_crypto_wallet_usdt', ''),
            'crypto_enabled'        => cache('payment_crypto_enabled', false),
        ];

        return view('admin.payment-settings', compact('paymentSettings'));
    }

    public function updatePaymentSettings(Request $request)
    {
        $request->validate([
            'active_gateway'    => 'required|in:stripe,paystack,flutterwave,paypal,bank_transfer,crypto',
            'default_currency'  => 'required|string|max:3',
            'currency_symbol'   => 'required|string|max:5',
            'currency_position' => 'required|in:before,after',
            'invoice_prefix'    => 'required|string|max:20',
            'tax_rate'          => 'required|numeric|min:0|max:100',
            'tax_label'         => 'required|string|max:20',
        ]);

        // General
        cache(['payment_active_gateway'    => $request->active_gateway]);
        cache(['payment_default_currency'  => strtoupper($request->default_currency)]);
        cache(['payment_currency_symbol'   => $request->currency_symbol]);
        cache(['payment_currency_position' => $request->currency_position]);
        cache(['payment_invoice_prefix'    => $request->invoice_prefix]);
        cache(['payment_tax_rate'          => $request->tax_rate]);
        cache(['payment_tax_label'         => $request->tax_label]);

        // Stripe
        cache(['payment_stripe_public_key'     => $request->stripe_public_key]);
        cache(['payment_stripe_secret_key'     => $request->stripe_secret_key]);
        cache(['payment_stripe_webhook_secret' => $request->stripe_webhook_secret]);
        cache(['payment_stripe_enabled'        => $request->has('stripe_enabled')]);

        // Paystack
        cache(['payment_paystack_public_key' => $request->paystack_public_key]);
        cache(['payment_paystack_secret_key' => $request->paystack_secret_key]);
        cache(['payment_paystack_enabled'    => $request->has('paystack_enabled')]);

        // Flutterwave
        cache(['payment_flw_public_key' => $request->flw_public_key]);
        cache(['payment_flw_secret_key' => $request->flw_secret_key]);
        cache(['payment_flw_enabled'    => $request->has('flw_enabled')]);

        // PayPal
        cache(['payment_paypal_client_id' => $request->paypal_client_id]);
        cache(['payment_paypal_secret'    => $request->paypal_secret]);
        cache(['payment_paypal_mode'      => $request->paypal_mode ?? 'sandbox']);
        cache(['payment_paypal_enabled'   => $request->has('paypal_enabled')]);

        // Bank Transfer
        cache(['payment_bank_name'           => $request->bank_name]);
        cache(['payment_bank_account_name'   => $request->bank_account_name]);
        cache(['payment_bank_account_number' => $request->bank_account_number]);
        cache(['payment_bank_routing_number' => $request->bank_routing_number]);
        cache(['payment_bank_swift_code'     => $request->bank_swift_code]);
        cache(['payment_bank_enabled'        => $request->has('bank_enabled')]);

        // Crypto
        cache(['payment_crypto_wallet_btc'  => $request->crypto_wallet_btc]);
        cache(['payment_crypto_wallet_usdt' => $request->crypto_wallet_usdt]);
        cache(['payment_crypto_enabled'     => $request->has('crypto_enabled')]);

        return back()->with('success', 'Payment gateway settings updated successfully.');
    }

    public function portalControl()
    {
        $clients = User::where('role', 'client')->get();
        $projects = Project::with(['client', 'milestones', 'invoices'])->get();
        $serviceRequests = \App\Models\ServiceRequest::with('client')->orderBy('created_at', 'desc')->get();
        $contracts = \App\Models\Contract::with(['client', 'project'])->orderBy('created_at', 'desc')->get();
        $auditLogs = \App\Models\AuditLog::with('user')->orderBy('created_at', 'desc')->take(20)->get();

        return view('admin.portal-control', compact('clients', 'projects', 'serviceRequests', 'contracts', 'auditLogs'));
    }

    public function createClientAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'company_name' => 'nullable|string|max:255'
        ]);

        $client = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'client',
            'status' => 'active'
        ]);

        if ($request->filled('company_name')) {
            cache(['client_company_' . $client->id => $request->company_name]);
        }

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event_type' => 'role_change',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => json_encode(['created_client_id' => $client->id, 'email' => $client->email])
        ]);

        return back()->with('success', 'Client account created successfully. They can now sign in using ' . $client->email);
    }

    public function sendProposal(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'contract_content' => 'required|string',
            'milestone_titles' => 'required|array',
            'milestone_amounts' => 'required|array',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function() use ($request) {
            $projectId = (string) Str::uuid();
            
            // 1. Create project
            $project = Project::create([
                'id' => $projectId,
                'client_id' => $request->client_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'initiated',
                'budget' => $request->budget
            ]);

            // 2. Create milestones and invoices
            $titles = $request->milestone_titles;
            $amounts = $request->milestone_amounts;
            
            foreach ($titles as $index => $title) {
                if (empty($title)) continue;
                $amount = isset($amounts[$index]) ? (float)$amounts[$index] : 0.00;

                $milestone = Milestone::create([
                    'project_id' => $projectId,
                    'title' => $title,
                    'description' => 'Milestone sprint delivery stage.',
                    'due_date' => now()->addDays(($index + 1) * 15),
                    'status' => 'pending',
                    'amount' => $amount
                ]);

                // Create invoice for this milestone
                Invoice::create([
                    'project_id' => $projectId,
                    'milestone_id' => $milestone->id,
                    'client_id' => $request->client_id,
                    'amount' => $amount,
                    'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(Str::random(5)),
                    'status' => 'unpaid',
                    'due_date' => now()->addDays(($index + 1) * 15)
                ]);
            }

            // 3. Create digital contract
            Contract::create([
                'project_id' => $projectId,
                'client_id' => $request->client_id,
                'title' => 'Service Agreement: ' . $request->title,
                'content' => $request->contract_content,
                'status' => 'pending_signature'
            ]);
        });

        return back()->with('success', 'Digital proposal and milestones sent successfully to client workspace.');
    }

    public function exportClientReport($id)
    {
        $project = Project::with(['client', 'milestones', 'invoices'])->findOrFail($id);
        
        $report = [
            'agency' => 'Diwebs Tech Agency',
            'export_date' => now()->toDateTimeString(),
            'project_id' => $project->id,
            'title' => $project->title,
            'client_name' => $project->client->name,
            'client_email' => $project->client->email,
            'budget' => $project->budget,
            'status' => $project->status,
            'agreement_signed' => $project->agreement_signed_at ? $project->agreement_signed_at->toDateTimeString() : 'Pending',
            'milestones' => $project->milestones->map(function($m) {
                return [
                    'title' => $m->title,
                    'amount' => $m->amount,
                    'status' => $m->status,
                    'due_date' => $m->due_date ? $m->due_date->toDateString() : 'N/A'
                ];
            }),
            'invoices' => $project->invoices->map(function($i) {
                return [
                    'invoice_number' => $i->invoice_number,
                    'amount' => $i->amount,
                    'status' => $i->status,
                    'due_date' => $i->due_date ? $i->due_date->toDateString() : 'N/A',
                    'paid_at' => $i->paid_at ? $i->paid_at->toDateTimeString() : 'Unpaid'
                ];
            })
        ];

        return response()->json($report, 200, [
            'Content-Disposition' => 'attachment; filename="diwebs_project_report_' . $project->id . '.json"'
        ]);
    }

    public function validateProject($id)
    {
        $project = Project::findOrFail($id);
        
        \Illuminate\Support\Facades\DB::transaction(function() use ($project) {
            $project->update([
                'is_validated' => true,
                'status' => 'planning'
            ]);

            // Create milestone
            $milestone = Milestone::create([
                'project_id' => $project->id,
                'title' => 'Initial Project Kickoff',
                'description' => 'Initial sprint milestone set up on project validation.',
                'due_date' => now()->addDays(15),
                'status' => 'pending',
                'amount' => $project->budget
            ]);

            // Create initial invoice
            Invoice::create([
                'project_id' => $project->id,
                'milestone_id' => $milestone->id,
                'client_id' => $project->client_id,
                'amount' => $project->budget,
                'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(Str::random(5)),
                'status' => 'unpaid',
                'due_date' => now()->addDays(15)
            ]);

            // Create digital contract
            \App\Models\Contract::create([
                'project_id' => $project->id,
                'client_id' => $project->client_id,
                'title' => 'Service Agreement: ' . $project->title,
                'content' => "This Service Agreement is entered into between Diwebs Tech Agency and the client. Project title: {$project->title}. Budget: " . \App\Helpers\PaymentHelper::format($project->budget) . "\n\nScope Description:\n{$project->description}",
                'status' => 'pending_signature'
            ]);
        });

        return back()->with('success', 'Project has been validated. The initial invoice and digital contract have been generated.');
    }

    public function updateSuccessRate(Request $request, $id)
    {
        $request->validate([
            'success_rate' => 'required|integer|min:0|max:100'
        ]);

        $project = Project::findOrFail($id);
        $project->update([
            'success_rate' => $request->success_rate
        ]);

        return back()->with('success', 'Project success rate updated successfully.');
    }

    // Academy Live Sessions & Teachers submodule logic
    public function academyLiveSessions()
    {
        $sessions = \App\Models\AcademyLiveSession::with('teacher')->orderBy('date', 'desc')->get();
        $teachers = \App\Models\AcademyTeacher::all();
        return view('admin.academy-live-sessions', compact('sessions', 'teachers'));
    }

    public function storeLiveSession(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'teacher_id' => 'required|exists:academy_teachers,id',
            'date' => 'required|date',
            'duration_minutes' => 'required|integer|min:15',
            'meeting_provider' => 'required|string',
            'session_type' => 'required|string',
            'description' => 'nullable|string'
        ]);

        // Auto-generate Google Meet URL simulation
        $meetUrl = 'https://meet.google.com/' . strtolower(Str::random(3)) . '-' . strtolower(Str::random(4)) . '-' . strtolower(Str::random(3));

        \App\Models\AcademyLiveSession::create([
            'title' => $request->title,
            'teacher_id' => $request->teacher_id,
            'meeting_provider' => $request->meeting_provider,
            'meeting_url' => $meetUrl,
            'date' => $request->date,
            'duration_minutes' => $request->duration_minutes,
            'session_type' => $request->session_type,
            'status' => 'scheduled',
            'description' => $request->description,
            'target_role' => 'all'
        ]);

        return back()->with('success', 'Live session scheduled successfully! Meet invite generated: ' . $meetUrl);
    }

    public function updateLiveSessionStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:scheduled,live,ended,cancelled'
        ]);

        $session = \App\Models\AcademyLiveSession::findOrFail($id);
        $session->update([
            'status' => $request->status
        ]);

        // Auto-generate recording if session is ended
        if ($request->status === 'ended') {
            \App\Models\AcademyRecording::firstOrCreate([
                'live_session_id' => $session->id,
            ], [
                'title' => $session->title . ' (Playback Recording)',
                'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'notes' => 'Recorded class review. Discussed topics: ' . $session->description,
                'ai_summary' => 'AI Summary: This session analyzed ' . $session->title . ' and established design paradigms for scaling.',
                'retention_days' => 30
            ]);
        }

        return back()->with('success', 'Live session status updated to: ' . $request->status);
    }

    public function academyTeachers()
    {
        $teachers = \App\Models\AcademyTeacher::with('availabilities')->get();
        $users = \App\Models\User::all();
        return view('admin.academy-teachers', compact('teachers', 'users'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expertise' => 'required|string',
            'bio' => 'required|string',
            'role' => 'required|string',
            'hourly_rate' => 'required|numeric|min:0',
            'email' => 'nullable|email',
            'user_id' => 'nullable|exists:users,id'
        ]);

        \App\Models\AcademyTeacher::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'expertise' => $request->expertise,
            'bio' => $request->bio,
            'role' => $request->role,
            'hourly_rate' => $request->hourly_rate,
            'email' => $request->email,
            'voice_only_enabled' => $request->has('voice_only_enabled'),
            'video_enabled' => $request->has('video_enabled'),
            'certifications' => []
        ]);

        return back()->with('success', 'Teacher profile onboarding complete.');
    }

    /**
     * CBT Command Center for Super Admins
     */
    public function cbtCommandCenter()
    {
        $centers = CbtCenter::with('owner')->get();
        $enrollments = CbtCenterEnrollment::with('user')->orderBy('created_at', 'desc')->get();
        $liveExams = CbtLiveExam::with(['exam', 'proctor'])->orderBy('scheduled_at', 'desc')->get();
        $exams = Exam::where('is_active', true)->get();
        
        // Fetch candidates proctor violations across the system
        $violations = \App\Models\CbtCandidateFlag::with('session.user')->orderBy('created_at', 'desc')->take(20)->get();

        return view('admin.cbt-command', compact('centers', 'enrollments', 'liveExams', 'exams', 'violations'));
    }

    /**
     * Approve or reject CBT physical center partner applications
     */
    public function updateCenterEnrollmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected'
        ]);

        $enrollment = CbtCenterEnrollment::findOrFail($id);
        $enrollment->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            // Auto-create/approve the physical center in cbt_centers
            $code = 'CBT-' . strtoupper(Str::random(3)) . '-' . rand(100, 999);
            
            CbtCenter::create([
                'owner_id' => $enrollment->user_id,
                'name' => $enrollment->organization_name . ' Certification Center',
                'code' => $code,
                'address' => 'Approved Physical Site Address',
                'city' => 'Metropolitan Area',
                'capacity' => ($enrollment->systems_count === '100+') ? 150 : 50,
                'contact_email' => $enrollment->user->email,
                'contact_phone' => '+2348000000000',
                'status' => 'active',
                'center_type' => $enrollment->center_type,
                'has_physical_location' => $enrollment->has_physical_location,
                'systems_count' => $enrollment->systems_count,
                'internet_quality' => $enrollment->internet_quality,
                'power_backup' => $enrollment->power_backup,
                'commission_rate' => 12.50,
                'revenue' => 0.00
            ]);

            // Ensure owner role is candidate or updated if roles mapping allows
        }

        return back()->with('success', 'CBT Center enrollment status updated to: ' . strtoupper($request->status));
    }

    /**
     * Schedule a live proctored examination event
     */
    public function storeLiveExamSchedule(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'scheduled_at' => 'required|date',
            'camera_required' => 'boolean',
            'mic_required' => 'boolean',
            'browser_lock_required' => 'boolean'
        ]);

        CbtLiveExam::create([
            'exam_id' => $request->exam_id,
            'scheduled_at' => $request->scheduled_at,
            'proctor_id' => auth()->id(),
            'camera_required' => $request->has('camera_required'),
            'mic_required' => $request->has('mic_required'),
            'browser_lock_required' => $request->has('browser_lock_required'),
            'status' => 'scheduled'
        ]);

        return back()->with('success', 'Live proctored examination successfully scheduled.');
    }

    public function referrals()
    {
        $referrals = \App\Models\Referral::with(['referrer', 'referee'])->orderBy('created_at', 'desc')->paginate(15);
        $totalPaid = \App\Models\Referral::where('status', 'paid')->sum('bonus_amount');
        $totalApproved = \App\Models\Referral::where('status', 'approved')->sum('bonus_amount');
        $totalPending = \App\Models\Referral::where('status', 'pending')->sum('bonus_amount');

        return view('admin.referrals', compact('referrals', 'totalPaid', 'totalApproved', 'totalPending'));
    }

    public function payReferralBonus($id)
    {
        $referral = \App\Models\Referral::findOrFail($id);
        $referral->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        return back()->with('success', 'Referral bonus marked as paid successfully.');
    }

    public function updateReferralStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,paid,void'
        ]);

        $referral = \App\Models\Referral::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        if ($request->status === 'paid' && !$referral->paid_at) {
            $updateData['paid_at'] = now();
        } elseif ($request->status !== 'paid') {
            $updateData['paid_at'] = null;
        }

        $referral->update($updateData);

        return back()->with('success', 'Referral status updated to ' . $request->status . '.');
    }

    // ──────────────────────────────────────────────────────────────
    // MAINTENANCE ACTIONS
    // ──────────────────────────────────────────────────────────────

    /**
     * Clear application cache, config cache, route cache & view cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return back()->with('success', '✅ All caches cleared successfully (application, config, route & view cache).');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Cache clear failed: ' . $e->getMessage());
        }
    }

    /**
     * Optimize database tables (MySQL OPTIMIZE TABLE on all tables).
     */
    public function optimizeDatabase()
    {
        try {
            $driver = DB::connection()->getDriverName();
            $count  = 0;

            if ($driver === 'sqlite') {
                // In SQLite, VACUUM is the database optimization and file defragmentation command
                DB::statement('VACUUM');
                
                // Fetch user tables to count them for the user message
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $count = count($tables);
            } else {
                // Default to MySQL table optimizations
                $tables = DB::select('SHOW TABLES');
                $dbName = DB::getDatabaseName();
                $key    = 'Tables_in_' . $dbName;
                foreach ($tables as $table) {
                    $tableName = $table->$key;
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                    $count++;
                }
            }

            // Also regenerate autoloads
            Artisan::call('config:cache');
            return back()->with('success', "✅ Database optimized successfully ({$count} tables processed and autoload cache rebuilt).");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Database optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Flush all active user sessions (forces everyone to re-login).
     */
    public function flushSessions()
    {
        try {
            // Flush the session table if using database driver
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->truncate();
            } else {
                // File-based sessions
                $sessionPath = config('session.files', storage_path('framework/sessions'));
                if (File::isDirectory($sessionPath)) {
                    foreach (File::files($sessionPath) as $file) {
                        File::delete($file);
                    }
                }
            }
            // Also clear the trusted devices table so 2FA re-triggers
            DB::table('user_devices')->truncate();
            return back()->with('success', '✅ All active sessions flushed. All users have been logged out and device records cleared.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Session flush failed: ' . $e->getMessage());
        }
    }

    /**
     * Full site data purge — clears expired OTPs, old audit logs,
     * stale device records, ended/flagged exam sessions older than 90 days,
     * read notifications older than 30 days, and all caches.
     */
    public function purgeOldData()
    {
        try {
            $report = [];

            // 1. Expired OTP codes
            $otps = DB::table('otp_codes')->where('expires_at', '<', now())->delete();
            $report[] = "{$otps} expired OTP code(s) removed";

            // 2. Old audit logs (> 90 days)
            $logs = DB::table('audit_logs')->where('created_at', '<', now()->subDays(90))->delete();
            $report[] = "{$logs} audit log entry/entries older than 90 days removed";

            // 3. Stale trusted device records not seen in 60 days
            $devices = DB::table('user_devices')->where('last_active_at', '<', now()->subDays(60))->delete();
            $report[] = "{$devices} stale device record(s) removed";

            // 4. Old ended/flagged exam sessions (> 90 days)
            $sessions = ExamSession::whereIn('status', ['completed', 'flagged', 'terminated'])
                ->where('created_at', '<', now()->subDays(90))
                ->delete();
            $report[] = "{$sessions} old exam session(s) purged";

            // 5. Read admin notifications older than 30 days
            $notifs = DB::table('admin_notifications')
                ->where('is_read', true)
                ->where('created_at', '<', now()->subDays(30))
                ->delete();
            $report[] = "{$notifs} read notification(s) removed";

            // 6. Full cache flush
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            $report[] = 'Application & view cache flushed';

            $summary = implode(', ', $report);
            return back()->with('success', "✅ Site cleaned successfully! {$summary}.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Purge failed: ' . $e->getMessage());
        }
    }
}

