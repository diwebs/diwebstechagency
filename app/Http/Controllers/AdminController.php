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
use App\Models\PartnershipRequest;
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

        $recentNotifications = \App\Models\AdminNotification::orderBy('created_at', 'desc')->take(10)->get();
        $unreadNotificationCount = \App\Models\AdminNotification::where('is_read', false)->count();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'centers', 'recentSessions', 'recentNotifications', 'unreadNotificationCount'));
    }

    public function getTelemetryData()
    {
        // 1. Active Users (sessions in last 5 minutes)
        $activeUsers = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
            ->count();

        // 2. Page views (based on logs)
        $pageViews = \App\Models\AuditLog::where('created_at', '>=', now()->subDay())->count();

        // 3. Growth rate
        $prevPageViews = \App\Models\AuditLog::where('created_at', '>=', now()->subDays(2))
            ->where('created_at', '<', now()->subDay())
            ->count();
        $growth = $prevPageViews > 0 ? round((($pageViews - $prevPageViews) / $prevPageViews) * 100, 1) : 0;

        // 4. Avg Session Duration (based on IP grouping first/last log time diff)
        $sessionDurations = DB::table('audit_logs')
            ->select(DB::raw('MIN(created_at) as start, MAX(created_at) as end'))
            ->groupBy('ip_address')
            ->get();
        
        $totalDuration = 0;
        $count = 0;
        foreach ($sessionDurations as $s) {
            $diff = strtotime($s->end) - strtotime($s->start);
            if ($diff > 0 && $diff < 86400) { // filter out anomalous single logs spanning days
                $totalDuration += $diff;
                $count++;
            }
        }
        $avgDuration = $count > 0 ? round($totalDuration / $count) : 0;
        $durationStr = $avgDuration > 60 
            ? floor($avgDuration / 60) . 'm ' . ($avgDuration % 60) . 's'
            : $avgDuration . 's';

        // 5. Bounce Rate (percentage of IPs with only 1 log)
        $sessionsCount = DB::table('audit_logs')->distinct('ip_address')->count('ip_address');
        $singleLogSessions = DB::table('audit_logs')
            ->select('ip_address', DB::raw('count(*) as total'))
            ->groupBy('ip_address')
            ->having('total', '=', 1)
            ->get()
            ->count();
        
        $bounceVal = $sessionsCount > 0 ? round(($singleLogSessions / $sessionsCount) * 100, 1) : 0;
        $bounceStr = $bounceVal . '%';

        // 6. Hourly traffic chart (last 12 hours)
        $chartData = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $hourStart = now()->subHours($i)->startOfHour();
            $hourEnd = now()->subHours($i)->endOfHour();
            $logCount = \App\Models\AuditLog::where('created_at', '>=', $hourStart)
                ->where('created_at', '<=', $hourEnd)
                ->count();
            $chartData[] = $logCount;
            $labels[] = now()->subHours($i)->format('H:i');
        }

        // 7. Acquisition channels based on real User Agents
        $logs = \App\Models\AuditLog::select('user_agent')->get();
        $chrome = 0;
        $safari = 0;
        $firefox = 0;
        $other = 0;
        $total = $logs->count();
        foreach ($logs as $log) {
            $ua = strtolower($log->user_agent);
            if (str_contains($ua, 'chrome') || str_contains($ua, 'crios')) {
                $chrome++;
            } elseif (str_contains($ua, 'safari') && !str_contains($ua, 'chrome') && !str_contains($ua, 'android')) {
                $safari++;
            } elseif (str_contains($ua, 'firefox') || str_contains($ua, 'fxios')) {
                $firefox++;
            } else {
                $other++;
            }
        }
        if ($total > 0) {
            $chromePct = round(($chrome / $total) * 100);
            $safariPct = round(($safari / $total) * 100);
            $firefoxPct = round(($firefox / $total) * 100);
            $otherPct = 100 - ($chromePct + $safariPct + $firefoxPct);
        } else {
            $chromePct = 0;
            $safariPct = 0;
            $firefoxPct = 0;
            $otherPct = 0;
        }

        return response()->json([
            'active_users' => $activeUsers,
            'page_views' => number_format($pageViews),
            'page_views_growth' => ($growth >= 0 ? '↑ ' : '↓ ') . abs($growth) . '% since yesterday',
            'session_duration' => $durationStr,
            'bounce_rate' => $bounceStr,
            'hourly_data' => $chartData,
            'hourly_labels' => $labels,
            'acquisition' => [
                ['name' => 'Chrome / Chromium', 'share' => $chromePct . '%', 'width' => $chromePct . '%', 'val' => $chromePct],
                ['name' => 'Safari Browser', 'share' => $safariPct . '%', 'width' => $safariPct . '%', 'val' => $safariPct],
                ['name' => 'Mozilla Firefox', 'share' => $firefoxPct . '%', 'width' => $firefoxPct . '%', 'val' => $firefoxPct],
                ['name' => 'Edge / Mobile / Other', 'share' => $otherPct . '%', 'width' => $otherPct . '%', 'val' => $otherPct],
            ]
        ]);
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

    public function changeUserPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Password for user ' . $user->name . ' updated successfully.');
    }

    public function updateUserRegion(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'country' => 'required|string|max:100'
        ]);

        $user->update([
            'country' => $validated['country']
        ]);

        // Sync CRM client record if present
        \App\Models\CrmClient::where('email', $user->email)->update([
            'country' => $validated['country']
        ]);

        return back()->with('success', "Region/Country updated to '{$validated['country']}' for {$user->name}. Localized billing plan is now active.");
    }

    public function storeCenter(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:cbt_centers,code|max:50',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'contact_email' => 'required|email|max:150',
            'contact_phone' => 'required|string|max:50',
            'center_type' => 'nullable|string|max:50',
            'power_backup' => 'nullable|string|max:50',
        ]);

        \App\Models\CbtCenter::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'city' => $request->city,
            'capacity' => $request->capacity,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'center_type' => $request->center_type,
            'power_backup' => $request->power_backup,
            'status' => 'active',
        ]);

        return back()->with('success', 'CBT Center "' . $request->name . '" registered successfully.');
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

    public function projects(Request $request)
    {
        $query = Project::with(['client', 'milestones', 'assignments.staff']);

        // Search by client name or project title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status tab
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $staffMembers = \App\Models\Staff::orderBy('name')->get();

        // Summary stats for the dashboard cards
        $stats = [
            'total'         => Project::count(),
            'pending'       => Project::where('is_validated', false)->count(),  // Client-submitted awaiting admin review
            'active'        => Project::where('status', 'active')->count(),
            'planning'      => Project::where('status', 'planning')->count(),
            'delivered'     => Project::where('status', 'delivered')->count(),
            'review'        => Project::where('status', 'review')->count(),
        ];

        // Recent client-submitted proposals (is_validated = false, status = initiated) 
        // that haven't been actioned yet — shown as the "Inbox"
        $pendingInbox = Project::with('client')
            ->where('is_validated', false)
            ->where('status', 'initiated')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.projects', compact('projects', 'staffMembers', 'stats', 'pendingInbox'));
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
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'mock_image'          => 'nullable|image|max:5120', // max 5MB image
            'captured_mock_image' => 'nullable|string|max:255',
            'project_url'         => 'nullable|url|max:255',
            'order'               => 'nullable|integer',
        ]);

        if ($request->hasFile('mock_image')) {
            $path = $request->file('mock_image')->store('portfolios', 'public');
            $validated['mock_image'] = $path;
        } elseif ($request->filled('captured_mock_image')) {
            $validated['mock_image'] = $request->input('captured_mock_image');
        }

        $validated['order'] = $validated['order'] ?? 0;

        Portfolio::create($validated);

        return redirect()->route('admin.portfolios')->with('success', 'Portfolio project created successfully.');
    }

    public function capturePortfolioScreenshot(Request $request)
    {
        $url = $request->input('url');
        if ($url && !preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
            $request->merge(['url' => $url]);
        }

        $request->validate([
            'url' => 'required|url|max:255',
        ]);

        $url = $request->input('url');

        try {
            // High-resolution API parameters for Microlink (retina, 1440x900 viewport, delay of 3000ms for JS rendering)
            // Enabling adblock and hiding common cookie consent/GDPR popups
            $hideSelectors = '#onetrust-consent-sdk,.cookie-banner,.cookie-consent,#cookie-banner,#cookie-consent,.cc-banner,.fc-consent-root,.cmp-consent-container,.sd-consent-dialog,#qc-cmp2-container,.cookie-modal';
            $screenshotUrl = "https://api.microlink.io/?url=" . urlencode($url) . 
                "&screenshot=true&embed=screenshot.url&viewport.width=1440&viewport.height=900&viewport.deviceScaleFactor=2&screenshot.type=png&delay=3000" .
                "&adblock=true&screenshot.hide=" . urlencode($hideSelectors);
            
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(30)->get($screenshotUrl);
            $contentType = $response->header('Content-Type');

            if ($response->successful() && $contentType && str_contains($contentType, 'image')) {
                $imageBytes = $response->body();
            } else {
                $errorMsg = 'Microlink API failed to capture image.';
                if ($contentType && str_contains($contentType, 'json')) {
                    $json = $response->json();
                    if (isset($json['message'])) {
                        $errorMsg = $json['message'];
                    } elseif (isset($json['data']['message'])) {
                        $errorMsg = $json['data']['message'];
                    }
                }
                logger()->warning("Microlink capture warning: " . $errorMsg . ". Trying fallback...");

                // Fallback to Thum.io if Microlink fails
                $fallbackUrl = "https://image.thum.io/get/width/1280/crop/800/" . $url;
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(30)->get($fallbackUrl);
                $contentType = $response->header('Content-Type');

                if ($response->successful() && $contentType && str_contains($contentType, 'image')) {
                    $imageBytes = $response->body();
                } else {
                    throw new \Exception('Failed to capture website screenshot from both primary and fallback services. Details: ' . $errorMsg);
                }
            }

            // Ensure portfolios storage folder exists
            if (!Storage::disk('public')->exists('portfolios')) {
                Storage::disk('public')->makeDirectory('portfolios');
            }

            $filename = 'captured_' . Str::random(10) . '_' . time() . '.png';
            $path = 'portfolios/' . $filename;

            Storage::disk('public')->put($path, $imageBytes);

            return response()->json([
                'status' => 'success',
                'path'   => $path,
                'url'    => '/storage/' . $path,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
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
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'mock_image'          => 'nullable|image|max:5120', // max 5MB image
            'captured_mock_image' => 'nullable|string|max:255',
            'project_url'         => 'nullable|url|max:255',
            'order'               => 'nullable|integer',
        ]);

        if ($request->hasFile('mock_image')) {
            // Delete old mock image if it exists
            if ($portfolio->mock_image) {
                Storage::disk('public')->delete($portfolio->mock_image);
            }
            $path = $request->file('mock_image')->store('portfolios', 'public');
            $validated['mock_image'] = $path;
        } elseif ($request->filled('captured_mock_image')) {
            // Delete old mock image if it exists and is different from the new captured image
            if ($portfolio->mock_image && $portfolio->mock_image !== $request->input('captured_mock_image')) {
                Storage::disk('public')->delete($portfolio->mock_image);
            }
            $validated['mock_image'] = $request->input('captured_mock_image');
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
        $clients = User::where('role', 'client')->orderBy('name')->get();
        $projects = Project::orderBy('title')->get();
        return view('admin.finance', compact('invoices', 'totalRevenue', 'pendingRevenue', 'clients', 'projects'));
    }

    public function storeInvoice(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        Invoice::create([
            'client_id' => $request->client_id,
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'invoice_number' => $request->invoice_number,
            'status' => 'unpaid',
            'due_date' => $request->due_date,
        ]);

        // Create User Notification
        \App\Models\UserNotification::create([
            'user_id' => $request->client_id,
            'title' => 'Milestone Invoice Dispatched',
            'message' => 'Invoice #' . $request->invoice_number . ' for ' . \App\Helpers\PaymentHelper::format($request->amount) . ' is outstanding. Due date: ' . date('M d, Y', strtotime($request->due_date)) . '.',
            'type' => 'invoice',
            'is_read' => false
        ]);

        return back()->with('success', 'Invoice generated and synchronized with client workspace successfully.');
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
        $allUsers = \App\Models\User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        return view('admin.notifications', compact('notifications', 'allUsers'));
    }

    public function sendSystemNotification(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|string'
        ]);

        $query = \App\Models\User::query();
        if ($request->target_role !== 'all') {
            $query->where('role', $request->target_role);
        }
        $users = $query->get();

        foreach ($users as $user) {
            \App\Models\UserNotification::create([
                'user_id' => $user->id,
                'title' => $request->subject,
                'message' => $request->message,
                'type' => 'broadcast',
                'is_read' => false
            ]);
        }

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

    /**
     * Send a direct email to a specific registered user.
     * Also creates an in-app notification for the recipient.
     */
    public function sendDirectEmail(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $recipient = \App\Models\User::findOrFail($request->user_id);
        $sender    = auth()->user();

        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($mail) use ($recipient, $sender, $request) {
                $mail->to($recipient->email, $recipient->name)
                     ->from(
                         config('mail.from.address', 'noreply@diwebstechagency.website'),
                         config('mail.from.name', 'Diwebs Tech Agency')
                     )
                     ->subject($request->subject)
                     ->text($request->message);
            });

            // Also store as in-app notification
            \App\Models\UserNotification::create([
                'user_id' => $recipient->id,
                'title'   => $request->subject,
                'message' => $request->message,
                'type'    => 'email',
                'is_read' => false,
            ]);

            return back()->with('email_success', 'Email sent successfully to ' . $recipient->name . ' (' . $recipient->email . ').');
        } catch (\Exception $e) {
            // Still save in-app notification even if mail fails
            \App\Models\UserNotification::create([
                'user_id' => $recipient->id,
                'title'   => $request->subject,
                'message' => $request->message . ' [Delivered as in-app notification only]',
                'type'    => 'email',
                'is_read' => false,
            ]);
            return back()->with('email_error', 'Mail delivery failed but the message was saved as an in-app notification for the user. Error: ' . $e->getMessage());
        }
    }

    // ─── Academy Plans CRUD ───────────────────────────────────────────────────

    public function academyPlans()
    {
        $plans   = \App\Models\AcademyPlan::with(['user', 'course'])->orderBy('created_at', 'desc')->paginate(20);
        $users   = \App\Models\User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        $courses = \App\Models\Course::orderBy('title')->get(['id', 'title']);
        return view('admin.academy-plans', compact('plans', 'users', 'courses'));
    }

    public function storeAcademyPlan(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'plan_name' => 'required|string|max:255',
            'expires_at'=> 'nullable|date|after:today',
            'notes'     => 'nullable|string|max:500',
        ]);

        $plan = \App\Models\AcademyPlan::create([
            'user_id'              => $request->user_id,
            'course_id'            => $request->course_id ?: null,
            'plan_name'            => $request->plan_name,
            'includes_live_class'  => $request->boolean('includes_live_class'),
            'includes_audio'       => $request->boolean('includes_audio'),
            'includes_mentorship'  => $request->boolean('includes_mentorship'),
            'status'               => 'active',
            'expires_at'           => $request->expires_at ?: null,
            'created_by'           => auth()->id(),
            'notes'                => $request->notes,
        ]);

        // Notify the student
        $features = [];
        if ($plan->includes_live_class)  $features[] = 'Live Classes';
        if ($plan->includes_audio)       $features[] = 'Audio Learning';
        if ($plan->includes_mentorship)  $features[] = 'Mentorship Bookings';

        \App\Models\UserNotification::create([
            'user_id' => $plan->user_id,
            'title'   => '🔑 New Academy Plan Assigned: ' . $plan->plan_name,
            'message' => 'An administrator has activated a new plan for you: "' . $plan->plan_name . '". '
                . (count($features) > 0 ? 'You now have access to: ' . implode(', ', $features) . '.' : '')
                . ($plan->expires_at ? ' This plan expires on ' . $plan->expires_at->format('M d, Y') . '.' : ' This plan has no expiry.'),
            'type'    => 'plan',
            'is_read' => false,
        ]);

        return redirect()->route('admin.academy-plans')->with('success', 'Plan "' . $plan->plan_name . '" assigned to student successfully.');
    }

    public function cancelAcademyPlan($id)
    {
        $plan = \App\Models\AcademyPlan::findOrFail($id);
        $plan->update(['status' => 'cancelled']);

        \App\Models\UserNotification::create([
            'user_id' => $plan->user_id,
            'title'   => '⚠️ Academy Plan Cancelled: ' . $plan->plan_name,
            'message' => 'Your plan "' . $plan->plan_name . '" has been cancelled by the administrator. Contact support if you have questions.',
            'type'    => 'plan',
            'is_read' => false,
        ]);

        return back()->with('success', 'Plan cancelled successfully.');
    }

    public function deleteAcademyPlan($id)
    {
        $plan = \App\Models\AcademyPlan::findOrFail($id);
        $plan->delete();
        return back()->with('success', 'Plan deleted permanently.');
    }

    // Settings Submodule
    public function settings()
    {
        $settings = [
            'app_name' => \App\Helpers\SettingsHelper::get('app_name', 'Diwebs Tech Agency'),
            'session_idle_timeout' => \App\Helpers\SettingsHelper::get('session_idle_timeout', 15),
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
            'google_reviews_url' => \App\Helpers\SettingsHelper::get('google_reviews_url', 'https://www.google.com/search?q=diwebs+tech+agency+review'),

            // Dynamic Mail Settings
            'mail_mailer' => \App\Helpers\SettingsHelper::get('mail_mailer', 'log'),
            'mail_host' => \App\Helpers\SettingsHelper::get('mail_host', 'mail.diwebstechagency.website'),
            'mail_port' => \App\Helpers\SettingsHelper::get('mail_port', '465'),
            'mail_username' => \App\Helpers\SettingsHelper::get('mail_username', 'noreply@diwebstechagency.website'),
            'mail_password' => \App\Helpers\SettingsHelper::get('mail_password', ''),
            'mail_scheme' => \App\Helpers\SettingsHelper::get('mail_scheme', 'ssl'),
            'mail_from_address' => \App\Helpers\SettingsHelper::get('mail_from_address', 'noreply@diwebstechagency.website'),
            'mail_from_name' => \App\Helpers\SettingsHelper::get('mail_from_name', 'Diwebs Tech Agency'),

            // OAuth / Social Login Settings
            'oauth_google_enabled'      => \App\Helpers\SettingsHelper::get('oauth_google_enabled', false),
            'oauth_google_client_id'    => \App\Helpers\SettingsHelper::get('oauth_google_client_id', ''),
            'oauth_google_client_secret'=> \App\Helpers\SettingsHelper::get('oauth_google_client_secret', ''),
            'oauth_google_redirect_uri' => \App\Helpers\SettingsHelper::get('oauth_google_redirect_uri', url('/auth/google/callback')),

            'oauth_apple_enabled'       => \App\Helpers\SettingsHelper::get('oauth_apple_enabled', false),
            'oauth_apple_client_id'     => \App\Helpers\SettingsHelper::get('oauth_apple_client_id', ''),
            'oauth_apple_team_id'       => \App\Helpers\SettingsHelper::get('oauth_apple_team_id', ''),
            'oauth_apple_key_id'        => \App\Helpers\SettingsHelper::get('oauth_apple_key_id', ''),
            'oauth_apple_redirect_uri'  => \App\Helpers\SettingsHelper::get('oauth_apple_redirect_uri', url('/auth/apple/callback')),

            'oauth_microsoft_enabled'       => \App\Helpers\SettingsHelper::get('oauth_microsoft_enabled', false),
            'oauth_microsoft_client_id'     => \App\Helpers\SettingsHelper::get('oauth_microsoft_client_id', ''),
            'oauth_microsoft_client_secret' => \App\Helpers\SettingsHelper::get('oauth_microsoft_client_secret', ''),
            'oauth_microsoft_tenant_id'     => \App\Helpers\SettingsHelper::get('oauth_microsoft_tenant_id', 'common'),
            'oauth_microsoft_redirect_uri'  => \App\Helpers\SettingsHelper::get('oauth_microsoft_redirect_uri', url('/auth/microsoft/callback')),
        ];
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'referral_bonus_amount' => 'required|numeric|min:0',
            'session_idle_timeout' => 'required|integer|min:1|max:1440',
        ]);

        \App\Helpers\SettingsHelper::set('app_name', $request->app_name);
        \App\Helpers\SettingsHelper::set('session_idle_timeout', (int)$request->session_idle_timeout);
        \App\Helpers\SettingsHelper::set('maintenance_mode', $request->has('maintenance_mode'));
        \App\Helpers\SettingsHelper::set('allow_registration', $request->has('allow_registration'));
        \App\Helpers\SettingsHelper::set('auto_backups', $request->has('auto_backups'));
        \App\Helpers\SettingsHelper::set('referral_bonus_amount', (float)$request->referral_bonus_amount);
        return back()->with('success', 'System branding and general settings updated successfully.');
    }


    public function updateSeoSettings(Request $request)
    {
        $request->validate([
            'google_analytics_id' => 'nullable|string|max:50',
            'seo_meta_title_suffix' => 'required|string|max:255',
            'seo_meta_description' => 'required|string',
            'seo_meta_keywords' => 'required|string',
            'seo_og_image_url' => 'nullable|url',
            'google_reviews_url' => 'nullable|url'
        ]);

        \App\Helpers\SettingsHelper::set('google_analytics_id', $request->google_analytics_id);
        \App\Helpers\SettingsHelper::set('seo_meta_title_suffix', $request->seo_meta_title_suffix);
        \App\Helpers\SettingsHelper::set('seo_meta_description', $request->seo_meta_description);
        \App\Helpers\SettingsHelper::set('seo_meta_keywords', $request->seo_meta_keywords);
        \App\Helpers\SettingsHelper::set('seo_og_image_url', $request->seo_og_image_url);
        \App\Helpers\SettingsHelper::set('google_reviews_url', $request->google_reviews_url);

        return back()->with('success', 'Google Analytics, SEO, and review settings updated successfully.');
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

    public function updateOAuthSettings(Request $request)
    {
        $request->validate([
            'oauth_google_client_id'     => 'nullable|string|max:500',
            'oauth_google_client_secret' => 'nullable|string|max:500',
            'oauth_google_redirect_uri'  => 'nullable|url|max:500',
            'oauth_apple_client_id'      => 'nullable|string|max:500',
            'oauth_apple_team_id'        => 'nullable|string|max:100',
            'oauth_apple_key_id'         => 'nullable|string|max:100',
            'oauth_apple_redirect_uri'   => 'nullable|url|max:500',
            'oauth_microsoft_client_id'     => 'nullable|string|max:500',
            'oauth_microsoft_client_secret' => 'nullable|string|max:500',
            'oauth_microsoft_tenant_id'     => 'nullable|string|max:200',
            'oauth_microsoft_redirect_uri'  => 'nullable|url|max:500',
        ]);

        // Google
        \App\Helpers\SettingsHelper::set('oauth_google_enabled', $request->has('oauth_google_enabled'));
        \App\Helpers\SettingsHelper::set('oauth_google_client_id', $request->oauth_google_client_id ?? '');
        if ($request->filled('oauth_google_client_secret')) {
            \App\Helpers\SettingsHelper::set('oauth_google_client_secret', $request->oauth_google_client_secret);
        }
        \App\Helpers\SettingsHelper::set('oauth_google_redirect_uri', $request->oauth_google_redirect_uri ?? url('/auth/google/callback'));

        // Apple
        \App\Helpers\SettingsHelper::set('oauth_apple_enabled', $request->has('oauth_apple_enabled'));
        \App\Helpers\SettingsHelper::set('oauth_apple_client_id', $request->oauth_apple_client_id ?? '');
        \App\Helpers\SettingsHelper::set('oauth_apple_team_id', $request->oauth_apple_team_id ?? '');
        \App\Helpers\SettingsHelper::set('oauth_apple_key_id', $request->oauth_apple_key_id ?? '');
        \App\Helpers\SettingsHelper::set('oauth_apple_redirect_uri', $request->oauth_apple_redirect_uri ?? url('/auth/apple/callback'));

        // Microsoft Azure
        \App\Helpers\SettingsHelper::set('oauth_microsoft_enabled', $request->has('oauth_microsoft_enabled'));
        \App\Helpers\SettingsHelper::set('oauth_microsoft_client_id', $request->oauth_microsoft_client_id ?? '');
        if ($request->filled('oauth_microsoft_client_secret')) {
            \App\Helpers\SettingsHelper::set('oauth_microsoft_client_secret', $request->oauth_microsoft_client_secret);
        }
        \App\Helpers\SettingsHelper::set('oauth_microsoft_tenant_id', $request->oauth_microsoft_tenant_id ?? 'common');
        \App\Helpers\SettingsHelper::set('oauth_microsoft_redirect_uri', $request->oauth_microsoft_redirect_uri ?? url('/auth/microsoft/callback'));

        return back()->with('success', 'Social Login (OAuth) settings updated successfully.');
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
            'active_gateway'        => \App\Helpers\SettingsHelper::get('payment_active_gateway', 'stripe'),
            'default_currency'      => \App\Helpers\SettingsHelper::get('payment_default_currency', 'USD'),
            'currency_symbol'       => \App\Helpers\SettingsHelper::get('payment_currency_symbol', '$'),
            'currency_position'     => \App\Helpers\SettingsHelper::get('payment_currency_position', 'before'),
            // Invoice configuration
            'invoice_prefix'        => \App\Helpers\SettingsHelper::get('payment_invoice_prefix', 'INV'),
            'tax_rate'              => \App\Helpers\SettingsHelper::get('payment_tax_rate', '0'),
            'tax_label'             => \App\Helpers\SettingsHelper::get('payment_tax_label', 'VAT'),
            // Stripe
            'stripe_public_key'     => \App\Helpers\SettingsHelper::get('payment_stripe_public_key', ''),
            'stripe_secret_key'     => \App\Helpers\SettingsHelper::get('payment_stripe_secret_key', ''),
            'stripe_webhook_secret' => \App\Helpers\SettingsHelper::get('payment_stripe_webhook_secret', ''),
            'stripe_enabled'        => \App\Helpers\SettingsHelper::get('payment_stripe_enabled', true),
            // Paystack
            'paystack_public_key'   => \App\Helpers\SettingsHelper::get('payment_paystack_public_key', ''),
            'paystack_secret_key'   => \App\Helpers\SettingsHelper::get('payment_paystack_secret_key', ''),
            'paystack_enabled'      => \App\Helpers\SettingsHelper::get('payment_paystack_enabled', false),
            // Flutterwave
            'flw_public_key'        => \App\Helpers\SettingsHelper::get('payment_flw_public_key', ''),
            'flw_secret_key'        => \App\Helpers\SettingsHelper::get('payment_flw_secret_key', ''),
            'flw_enabled'           => \App\Helpers\SettingsHelper::get('payment_flw_enabled', false),
            // PayPal
            'paypal_client_id'      => \App\Helpers\SettingsHelper::get('payment_paypal_client_id', ''),
            'paypal_secret'         => \App\Helpers\SettingsHelper::get('payment_paypal_secret', ''),
            'paypal_mode'           => \App\Helpers\SettingsHelper::get('payment_paypal_mode', 'sandbox'),
            'paypal_enabled'        => \App\Helpers\SettingsHelper::get('payment_paypal_enabled', false),
            // Razorpay
            'razorpay_key_id'       => \App\Helpers\SettingsHelper::get('payment_razorpay_key_id', ''),
            'razorpay_key_secret'   => \App\Helpers\SettingsHelper::get('payment_razorpay_key_secret', ''),
            'razorpay_enabled'      => \App\Helpers\SettingsHelper::get('payment_razorpay_enabled', false),
            // Coinbase Commerce
            'coinbase_api_key'      => \App\Helpers\SettingsHelper::get('payment_coinbase_api_key', ''),
            'coinbase_webhook_secret'=> \App\Helpers\SettingsHelper::get('payment_coinbase_webhook_secret', ''),
            'coinbase_enabled'      => \App\Helpers\SettingsHelper::get('payment_coinbase_enabled', false),
            // Bank Transfer
            'bank_name'             => \App\Helpers\SettingsHelper::get('payment_bank_name', ''),
            'bank_account_name'     => \App\Helpers\SettingsHelper::get('payment_bank_account_name', ''),
            'bank_account_number'   => \App\Helpers\SettingsHelper::get('payment_bank_account_number', ''),
            'bank_routing_number'   => \App\Helpers\SettingsHelper::get('payment_bank_routing_number', ''),
            'bank_swift_code'       => \App\Helpers\SettingsHelper::get('payment_bank_swift_code', ''),
            'bank_enabled'          => \App\Helpers\SettingsHelper::get('payment_bank_enabled', false),
            // Crypto
            'crypto_wallet_btc'     => \App\Helpers\SettingsHelper::get('payment_crypto_wallet_btc', ''),
            'crypto_wallet_usdt'    => \App\Helpers\SettingsHelper::get('payment_crypto_wallet_usdt', ''),
            'crypto_enabled'        => \App\Helpers\SettingsHelper::get('payment_crypto_enabled', false),
        ];

        return view('admin.payment-settings', compact('paymentSettings'));
    }

    public function updatePaymentSettings(Request $request)
    {
        $request->validate([
            'active_gateway'    => 'required|in:stripe,paystack,flutterwave,paypal,bank_transfer,crypto,razorpay,coinbase',
            'default_currency'  => 'required|string|max:3',
            'currency_symbol'   => 'required|string|max:5',
            'currency_position' => 'required|in:before,after',
            'invoice_prefix'    => 'required|string|max:20',
            'tax_rate'          => 'required|numeric|min:0|max:100',
            'tax_label'         => 'required|string|max:20',
        ]);

        // General
        \App\Helpers\SettingsHelper::set('payment_active_gateway', $request->active_gateway);
        \App\Helpers\SettingsHelper::set('payment_default_currency', strtoupper($request->default_currency));
        \App\Helpers\SettingsHelper::set('payment_currency_symbol', $request->currency_symbol);
        \App\Helpers\SettingsHelper::set('payment_currency_position', $request->currency_position);
        \App\Helpers\SettingsHelper::set('payment_invoice_prefix', $request->invoice_prefix);
        \App\Helpers\SettingsHelper::set('payment_tax_rate', $request->tax_rate);
        \App\Helpers\SettingsHelper::set('payment_tax_label', $request->tax_label);

        // Regional Exchange Rates
        if ($request->has('rate_zar')) \App\Helpers\SettingsHelper::set('currency_exchange_rate_zar', (float)$request->rate_zar);
        if ($request->has('rate_kes')) \App\Helpers\SettingsHelper::set('currency_exchange_rate_kes', (float)$request->rate_kes);
        if ($request->has('rate_ghs')) \App\Helpers\SettingsHelper::set('currency_exchange_rate_ghs', (float)$request->rate_ghs);
        if ($request->has('rate_egp')) \App\Helpers\SettingsHelper::set('currency_exchange_rate_egp', (float)$request->rate_egp);
        if ($request->has('rate_ngn')) \App\Helpers\SettingsHelper::set('currency_exchange_rate_ngn', (float)$request->rate_ngn);
        if ($request->has('rate_gbp')) \App\Helpers\SettingsHelper::set('currency_exchange_rate_gbp', (float)$request->rate_gbp);
        if ($request->has('rate_eur')) \App\Helpers\SettingsHelper::set('currency_exchange_rate_eur', (float)$request->rate_eur);

        // Stripe
        \App\Helpers\SettingsHelper::set('payment_stripe_public_key', $request->stripe_public_key);
        \App\Helpers\SettingsHelper::set('payment_stripe_secret_key', $request->stripe_secret_key);
        \App\Helpers\SettingsHelper::set('payment_stripe_webhook_secret', $request->stripe_webhook_secret);
        \App\Helpers\SettingsHelper::set('payment_stripe_enabled', $request->has('stripe_enabled'));

        // Paystack
        \App\Helpers\SettingsHelper::set('payment_paystack_public_key', $request->paystack_public_key);
        \App\Helpers\SettingsHelper::set('payment_paystack_secret_key', $request->paystack_secret_key);
        \App\Helpers\SettingsHelper::set('payment_paystack_enabled', $request->has('paystack_enabled'));

        // Flutterwave
        \App\Helpers\SettingsHelper::set('payment_flw_public_key', $request->flw_public_key);
        \App\Helpers\SettingsHelper::set('payment_flw_secret_key', $request->flw_secret_key);
        \App\Helpers\SettingsHelper::set('payment_flw_enabled', $request->has('flw_enabled'));

        // PayPal
        \App\Helpers\SettingsHelper::set('payment_paypal_client_id', $request->paypal_client_id);
        \App\Helpers\SettingsHelper::set('payment_paypal_secret', $request->paypal_secret);
        \App\Helpers\SettingsHelper::set('payment_paypal_mode', $request->paypal_mode ?? 'sandbox');
        \App\Helpers\SettingsHelper::set('payment_paypal_enabled', $request->has('paypal_enabled'));

        // Razorpay
        \App\Helpers\SettingsHelper::set('payment_razorpay_key_id', $request->razorpay_key_id);
        \App\Helpers\SettingsHelper::set('payment_razorpay_key_secret', $request->razorpay_key_secret);
        \App\Helpers\SettingsHelper::set('payment_razorpay_enabled', $request->has('razorpay_enabled'));

        // Coinbase Commerce
        \App\Helpers\SettingsHelper::set('payment_coinbase_api_key', $request->coinbase_api_key);
        \App\Helpers\SettingsHelper::set('payment_coinbase_webhook_secret', $request->coinbase_webhook_secret);
        \App\Helpers\SettingsHelper::set('payment_coinbase_enabled', $request->has('coinbase_enabled'));

        // Bank Transfer
        \App\Helpers\SettingsHelper::set('payment_bank_name', $request->bank_name);
        \App\Helpers\SettingsHelper::set('payment_bank_account_name', $request->bank_account_name);
        \App\Helpers\SettingsHelper::set('payment_bank_account_number', $request->bank_account_number);
        \App\Helpers\SettingsHelper::set('payment_bank_routing_number', $request->bank_routing_number);
        \App\Helpers\SettingsHelper::set('payment_bank_swift_code', $request->bank_swift_code);
        \App\Helpers\SettingsHelper::set('payment_bank_enabled', $request->has('bank_enabled'));

        // Crypto
        \App\Helpers\SettingsHelper::set('payment_crypto_wallet_btc', $request->crypto_wallet_btc);
        \App\Helpers\SettingsHelper::set('payment_crypto_wallet_usdt', $request->crypto_wallet_usdt);
        \App\Helpers\SettingsHelper::set('payment_crypto_enabled', $request->has('crypto_enabled'));

        return back()->with('success', 'Payment gateway settings updated successfully.');
    }

    public function approveServiceRequestPayment(Request $request, $id)
    {
        $serviceRequest = \App\Models\ServiceRequest::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function() use ($serviceRequest) {
            $serviceRequest->update([
                'payment_status' => 'paid',
                'status' => 'approved'
            ]);

            // Automatically provision project, contract, milestones, and invoice
            $project = \App\Models\Project::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'client_id' => $serviceRequest->client_id,
                'title' => $serviceRequest->title,
                'description' => $serviceRequest->description,
                'status' => 'planning',
                'budget' => $serviceRequest->payment_amount ?? 500.00,
                'is_validated' => true,
                'agreement_signed_at' => null
            ]);

            $milestone = \App\Models\Milestone::create([
                'project_id' => $project->id,
                'title' => 'Initial Project Kickoff',
                'description' => 'Initial sprint milestone set up on project checkout.',
                'due_date' => now()->addDays(15),
                'status' => 'pending',
                'amount' => $serviceRequest->payment_amount ?? 500.00
            ]);

            $invoice = \App\Models\Invoice::create([
                'project_id' => $project->id,
                'milestone_id' => $milestone->id,
                'client_id' => $serviceRequest->client_id,
                'amount' => $serviceRequest->payment_amount ?? 500.00,
                'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(5)),
                'status' => 'paid',
                'due_date' => now()->addDays(15),
                'paid_at' => now()
            ]);

            \App\Models\Contract::create([
                'project_id' => $project->id,
                'client_id' => $serviceRequest->client_id,
                'title' => 'Service Agreement: ' . $project->title,
                'content' => "This Service Agreement is entered into between Diwebs Tech Agency and the client. Project title: {$project->title}. Budget: " . \App\Helpers\PaymentHelper::format($project->budget) . "\n\nScope Description:\n{$project->description}",
                'status' => 'pending_signature'
            ]);

            \App\Models\UserNotification::create([
                'user_id' => $serviceRequest->client_id,
                'title' => 'Payment Approved & Project Activated',
                'message' => 'Your payment for "' . $serviceRequest->title . '" has been approved by the billing team! Project is now activated.',
                'type' => 'project',
                'is_read' => false
            ]);
        });

        return back()->with('success', 'Client service request payment has been approved. Project, contract, and milestones have been automatically provisioned.');
    }

    public function rejectServiceRequestPayment(Request $request, $id)
    {
        $serviceRequest = \App\Models\ServiceRequest::findOrFail($id);

        $serviceRequest->update([
            'payment_status' => 'unpaid',
            'status' => 'submitted',
            'payment_proof' => null,
            'payment_txid' => null
        ]);

        \App\Models\UserNotification::create([
            'user_id' => $serviceRequest->client_id,
            'title' => 'Payment Proof Rejected',
            'message' => 'Your payment proof for "' . $serviceRequest->title . '" was rejected by the billing team. Please check the checkout page and re-submit valid proof.',
            'type' => 'system',
            'is_read' => false
        ]);

        return back()->with('success', 'Client service request payment has been rejected and user has been notified.');
    }


    public function portalControl()
    {
        $clients = User::where('role', 'client')->get();
        $projects = Project::with(['client', 'milestones', 'invoices'])->get();
        $serviceRequests = \App\Models\ServiceRequest::with('client')->orderBy('created_at', 'desc')->get();
        $contracts = \App\Models\Contract::with(['client', 'project'])->orderBy('created_at', 'desc')->get();
        $auditLogs = \App\Models\AuditLog::with('user')->orderBy('created_at', 'desc')->take(20)->get();
        $partnershipRequests = PartnershipRequest::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.portal-control', compact('clients', 'projects', 'serviceRequests', 'contracts', 'auditLogs', 'partnershipRequests'));
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
            $invoice = Invoice::create([
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

            // Create notifications for client
            \App\Models\UserNotification::create([
                'user_id' => $project->client_id,
                'title' => 'Project Proposal Approved & Validated',
                'message' => 'Your project proposal "' . $project->title . '" has been validated. The initial kickoff stage is set.',
                'type' => 'project',
                'is_read' => false
            ]);

            \App\Models\UserNotification::create([
                'user_id' => $project->client_id,
                'title' => 'Initial Kickoff Invoice Dispatched',
                'message' => 'Invoice #' . $invoice->invoice_number . ' for ' . \App\Helpers\PaymentHelper::format($project->budget) . ' is outstanding.',
                'type' => 'invoice',
                'is_read' => false
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

    public function updateProjectNote(Request $request, $id)
    {
        $request->validate([
            'pipeline_note' => 'nullable|string|max:5000'
        ]);

        $project = Project::with('client')->findOrFail($id);
        $project->update([
            'pipeline_note' => $request->pipeline_note
        ]);

        // Create notification for client
        \App\Models\UserNotification::create([
            'user_id' => $project->client_id,
            'title' => 'Project Pipeline Note Added',
            'message' => 'Your project administrator has posted a new update note: "' . Str::limit($request->pipeline_note, 150) . '"',
            'type' => 'project',
            'is_read' => false
        ]);

        // Send email to client's mailbox
        try {
            $toEmail = $project->client->email;
            $projectTitle = $project->title;
            $noteContent = nl2br(e($request->pipeline_note));
            
            \Illuminate\Support\Facades\Mail::html(
                "<div style='font-family:sans-serif;max-width:600px;margin:auto;padding:25px;border:1px solid #1E2125;background-color:#1E2125;color:#ffffff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.3);'>" .
                "<div style='text-align:center;margin-bottom:20px;'><img src='https://diwebstechagency.website/images/brand/diwebs-logo.svg' alt='Diwebs Logo' style='height:45px;' /></div>" .
                "<h2 style='color:#06b6d4;border-bottom:1px solid #0d9488;padding-bottom:10px;text-align:center;margin-top:0;'>Project Status Update</h2>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Hello {$project->client->name},</p>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>A new status note has been added to your project: <strong>{$projectTitle}</strong></p>" .
                "<div style='background-color:#111827;border:1px solid #334155;border-left:4px solid #0d9488;padding:15px;border-radius:8px;margin:25px 0;color:#cbd5e1;font-size:13px;line-height:1.6;font-style:italic;'>" .
                "{$noteContent}" .
                "</div>" .
                "<div style='text-align:center;margin:30px 0;'><a href='https://diwebstechagency.website/login' style='background:linear-gradient(to right, #0d9488, #06b6d4);color:#111827;text-decoration:none;font-weight:bold;font-size:13px;padding:12px 30px;border-radius:8px;box-shadow:0 4px 15px rgba(6,182,212,0.2);'>View Client Workspace</a></div>" .
                "<p style='font-size:11px;color:#94a3b8;margin-top:40px;border-top:1px solid #334155;padding-top:15px;text-align:center;'>This is an automated notification from Diwebs Tech Project Pipeline Manager.</p>" .
                "</div>",
                function ($message) use ($toEmail, $projectTitle) {
                    $message->to($toEmail)->subject("Project Update: {$projectTitle} - Diwebs Tech Agency");
                }
            );
        } catch (\Exception $e) {
            logger()->error("Failed to send project pipeline note email: " . $e->getMessage());
        }

        return back()->with('success', 'Project pipeline note updated and client notified via email.');
    }

    public function assignStaff(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'staff_id' => 'required|exists:staff_members,id',
            'role'     => 'required|string|max:100',
        ]);

        // Create assignment
        \App\Models\ProjectAssignment::firstOrCreate([
            'project_id' => $project->id,
            'staff_id'   => $request->staff_id,
            'role'       => $request->role,
        ]);

        return back()->with('success', 'Staff member assigned to project successfully.');
    }

    public function removeStaffAssignment(Request $request, $id, $assignmentId)
    {
        $assignment = \App\Models\ProjectAssignment::where('project_id', $id)->findOrFail($assignmentId);
        $assignment->delete();

        return back()->with('success', 'Staff assignment removed successfully.');
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

            // Update user role to partner so they see the partner view
            $enrollment->user->update(['role' => 'partner']);

            \App\Models\UserNotification::create([
                'user_id' => $enrollment->user_id,
                'title' => 'Center Partner Application Approved',
                'message' => 'Congratulations! Your CBT center partner request has been approved. Center code: ' . $code . '.',
                'type' => 'broadcast',
                'is_read' => false
            ]);
        } elseif ($request->status === 'rejected') {
            \App\Models\UserNotification::create([
                'user_id' => $enrollment->user_id,
                'title' => 'Center Partner Application Declined',
                'message' => 'Your CBT center partner request for "' . $enrollment->organization_name . '" was declined.',
                'type' => 'broadcast',
                'is_read' => false
            ]);
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

    public function approvePartnership(Request $request, $id)
    {
        $partnership = PartnershipRequest::findOrFail($id);
        $partnership->update(['status' => 'approved']);

        // Send notification to the user
        \App\Models\UserNotification::create([
            'user_id' => $partnership->user_id,
            'title' => 'Partnership Application Approved',
            'message' => 'Congratulations! Your request to partner with Diwebs Tech Agency has been approved.',
            'type' => 'system',
            'is_read' => false
        ]);

        // Add to audit logs
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event_type' => 'partnership_approved',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => json_encode(['partnership_id' => $partnership->id, 'company_name' => $partnership->company_name])
        ]);

        return back()->with('success', 'Partnership request approved successfully.');
    }

    public function declinePartnership(Request $request, $id)
    {
        $partnership = PartnershipRequest::findOrFail($id);
        $partnership->update(['status' => 'declined']);

        // Send notification to the user
        \App\Models\UserNotification::create([
            'user_id' => $partnership->user_id,
            'title' => 'Partnership Application Declined',
            'message' => 'Your request to partner with Diwebs Tech Agency has been reviewed and declined.',
            'type' => 'system',
            'is_read' => false
        ]);

        // Add to audit logs
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event_type' => 'partnership_declined',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => json_encode(['partnership_id' => $partnership->id, 'company_name' => $partnership->company_name])
        ]);

        return back()->with('success', 'Partnership request declined.');
    }

    /**
     * Run outstanding database migrations securely.
     */
    public function runMigrations()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $migOutput = \Illuminate\Support\Facades\Artisan::output();

            // Clear all system caches to apply changes (e.g. view caches)
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');

            return back()->with('success', "✅ Database migrated and cache cleared successfully!\n" . $migOutput);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Migrations failed: ' . $e->getMessage());
        }
    }

    /**
     * Display all customer reviews for moderation.
     */
    public function reviews()
    {
        $reviews = \App\Models\Review::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.reviews', compact('reviews'));
    }

    /**
     * Approve a customer review.
     */
    public function approveReview($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->update(['status' => 'approved']);

        return back()->with('success', 'Customer review approved and published successfully.');
    }

    /**
     * Delete a customer review.
     */
    public function deleteReview($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Customer review deleted successfully.');
    }
}

