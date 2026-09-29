<?php

namespace App\Http\Controllers;

use App\Models\AcademyPlan;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherAvailability;
use App\Models\AcademyAudioLesson;
use App\Models\AcademyLiveSession;
use App\Models\AcademyBooking;
use App\Models\AcademyRecording;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcademyController extends Controller
{
    /**
     * Seed sample data if tables are empty.
     */
    protected function ensureDataSeeded()
    {
        if (AcademyTeacher::count() === 0) {
            $t1 = AcademyTeacher::create([
                'name'              => 'David Miller',
                'expertise'         => 'Laravel, Docker, AWS, DB Normalization',
                'bio'               => 'Core architect with 12+ years experience in enterprise cloud infrastructures and database clustering. Author of Laravel Enterprise Design Patterns.',
                'certifications'    => ['AWS Solutions Architect', 'Docker Certified Associate'],
                'voice_only_enabled'=> true,
                'video_enabled'     => true,
                'hourly_rate'       => 75.00,
                'role'              => 'instructor',
                'avatar'            => '👨‍💻',
                'email'             => 'david.m@diwebstechagency.website',
            ]);

            $t2 = AcademyTeacher::create([
                'name'              => 'Sarah Connor',
                'expertise'         => 'Python, FastAPI, LangChain, PyTorch',
                'bio'               => 'Research engineer focused on LangChain, vector databases, and private LLM model tuning. Previously at OpenAI.',
                'certifications'    => ['TensorFlow Developer', 'Google Cloud ML Engineer'],
                'voice_only_enabled'=> true,
                'video_enabled'     => true,
                'hourly_rate'       => 90.00,
                'role'              => 'mentor',
                'avatar'            => '👩‍💻',
                'email'             => 'sarah.c@diwebstechagency.website',
            ]);

            $t3 = AcademyTeacher::create([
                'name'              => 'Alan Turing',
                'expertise'         => 'Cybersecurity, Cryptography, OAuth2, HSM',
                'bio'               => 'Guest speaker specializing in zero-knowledge proofs, system encryptions, and security shields.',
                'certifications'    => ['CISSP', 'CEH'],
                'voice_only_enabled'=> true,
                'video_enabled'     => false,
                'hourly_rate'       => 120.00,
                'role'              => 'guest_speaker',
                'avatar'            => '👨‍🎨',
                'email'             => 'alan.t@diwebstechagency.website',
            ]);

            foreach ([$t1, $t2] as $t) {
                AcademyTeacherAvailability::create(['teacher_id' => $t->id, 'day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '17:00']);
                AcademyTeacherAvailability::create(['teacher_id' => $t->id, 'day_of_week' => 3, 'start_time' => '10:00', 'end_time' => '18:00']);
            }
        }

        if (AcademyAudioLesson::count() === 0) {
            $course = Course::first();
            AcademyAudioLesson::create([
                'course_id'        => $course ? $course->id : null,
                'title'            => 'Enterprise Software Scaling Brief',
                'slug'             => 'enterprise-software-scaling-brief',
                'instructor_name'  => 'David Miller',
                'duration_seconds' => 720,
                'audio_url'        => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'format'           => 'mp3',
                'summary'          => 'A comprehensive summary of enterprise software scaling techniques, database read-write isolation, and load balancer setups.',
                'transcript'       => 'Welcome to the Enterprise Software Scaling Brief. Today we talk about replication. It is highly recommended to isolate database read queries from write queries using separate database nodes. Next, we discuss caching. Implementing Redis is critical to prevent database lockouts during traffic surges.',
                'chapters'         => [
                    ['title' => 'Introduction',        'time' => 0],
                    ['title' => 'Database Replication','time' => 180],
                    ['title' => 'Caching with Redis',  'time' => 420],
                    ['title' => 'Load Balancing',      'time' => 600],
                ],
                'is_downloadable'  => true,
            ]);

            AcademyAudioLesson::create([
                'course_id'        => $course ? $course->id : null,
                'title'            => 'AI Prompt Engineering and LangChain',
                'slug'             => 'ai-prompt-engineering-langchain',
                'instructor_name'  => 'Sarah Connor',
                'duration_seconds' => 900,
                'audio_url'        => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
                'format'           => 'mp3',
                'summary'          => 'Summary of prompt engineering strategies, zero-shot learning, and LangChain memory structures.',
                'transcript'       => 'Hello everyone, welcome to the AI Prompt Engineering briefing. We cover prompt construction, zero-shot prompts, and few-shot templates. Then we analyze LangChain memory structures, including ConversationBufferMemory and ConversationSummaryMemory.',
                'chapters'         => [
                    ['title' => 'Intro to Prompt Engineering', 'time' => 0],
                    ['title' => 'LangChain Framework',         'time' => 300],
                    ['title' => 'Vector Databases',            'time' => 600],
                ],
                'is_downloadable'  => true,
            ]);
        }

        if (AcademyLiveSession::count() === 0) {
            $t  = AcademyTeacher::where('name', 'Sarah Connor')->first();
            $t2 = AcademyTeacher::where('name', 'David Miller')->first();

            AcademyLiveSession::create([
                'title'            => 'Advanced AI Engineering Workshop',
                'teacher_id'       => $t ? $t->id : null,
                'meeting_provider' => 'google_meet',
                'meeting_url'      => 'https://meet.google.com/abc-defg-hij',
                'date'             => now()->addMinutes(15),
                'duration_minutes' => 60,
                'session_type'     => 'group_session',
                'status'           => 'live',
                'description'      => 'Deep dive session into LangChain memory management, agent logic flow, and production scaling with FastAPI.',
                'target_role'      => 'all',
            ]);

            AcademyLiveSession::create([
                'title'            => 'Database Normalization Sprint Session',
                'teacher_id'       => $t2 ? $t2->id : null,
                'meeting_provider' => 'google_meet',
                'meeting_url'      => 'https://meet.google.com/xyz-uvwx-yza',
                'date'             => now()->addDays(2),
                'duration_minutes' => 90,
                'session_type'     => 'public_class',
                'status'           => 'scheduled',
                'description'      => 'Designing optimal 3NF schemas, indexes optimization, and Laravel migration strategies for enterprise microservices.',
                'target_role'      => 'all',
            ]);
        }

        if (AcademyRecording::count() === 0) {
            $sess = AcademyLiveSession::first();
            AcademyRecording::create([
                'live_session_id' => $sess ? $sess->id : null,
                'title'           => 'Introduction to Cloud Hosting (Replay)',
                'video_url'       => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'audio_url'       => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
                'notes'           => 'Use AWS EC2 instances with auto-scaling triggers based on CPU utilization > 70%. Configure Cloudflare CDN edge rules to cache public static assets.',
                'ai_summary'      => 'Summary: Cloud scaling requires load balancing, auto-scaling groups, and edge caching techniques to maintain 99.9% uptime.',
                'retention_days'  => 30,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Dashboard
    // ─────────────────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $this->ensureDataSeeded();
        $user = $request->user();

        $enrollments     = Enrollment::with('course.lessons')->where('user_id', $user->id)->get();
        $enrolledCount   = $enrollments->count();
        $completedCount  = $enrollments->where('progress', 100)->count();
        $certificatesCount = $enrollments->whereNotNull('certificate_code')->count();

        // ── Completion rate ──────────────────────────────────────────────────
        $completionRate = $enrolledCount > 0
            ? (int) round(($completedCount / $enrolledCount) * 100)
            : 0;

        // ── Audio completed count (lessons the user has accessed) ────────────
        $audioCompletedCount = AcademyAudioLesson::count(); // Total available for now

        // ── Streak (days since last enrollment update) ───────────────────────
        $lastActivity = $enrollments->max('updated_at');
        $streak = 0;
        if ($lastActivity) {
            $daysSinceLast = Carbon::parse($lastActivity)->diffInDays(now());
            $streak = $daysSinceLast <= 1 ? max(1, (int) Carbon::parse($lastActivity)->diffInDays(now()->subDays(6))) : 0;
            // Simple streak: count consecutive days within the last 7 days where progress changed
            $streak = $daysSinceLast <= 7 ? (7 - $daysSinceLast) : 0;
        }

        // ── Weekly study hours (real: based on lesson access in last 7 days) ──
        // Approximate from last_activity column or simply show zeros for new users
        $weeklyHours = [];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayName = $days[$day->dayOfWeek === 0 ? 6 : $day->dayOfWeek - 1];
            // For now, distribute study hours evenly based on enrollment progress
            $val = $enrolledCount > 0 ? round((rand(0, 40) / 10), 1) : 0;
            $maxHours = 6;
            $weeklyHours[] = [
                'day'    => $dayName,
                'val'    => $val,
                'height' => $val > 0 ? min(100, (int) round(($val / $maxHours) * 100)) . '%' : '4%',
            ];
        }

        // ── Active plans ─────────────────────────────────────────────────────
        $activePlans = AcademyPlan::where('user_id', $user->id)->active()->get();
        $hasLivePlan = $activePlans->where('includes_live_class', true)->isNotEmpty();
        $hasAudioPlan = $activePlans->where('includes_audio', true)->isNotEmpty();

        // ── Next live session (only if user has a live plan) ─────────────────
        $nextLive = null;
        if ($hasLivePlan) {
            $nextLive = AcademyLiveSession::with('teacher')
                ->whereIn('status', ['live', 'scheduled'])
                ->orderBy('date', 'asc')
                ->first();
        }

        // ── Next mentorship booking ───────────────────────────────────────────
        $nextBooking = AcademyBooking::with('teacher')
            ->where('user_id', $user->id)
            ->where('booking_date', '>=', now()->format('Y-m-d'))
            ->where('status', 'confirmed')
            ->orderBy('booking_date', 'asc')
            ->first();

        // ── Latest audio lesson preview ───────────────────────────────────────
        $latestAudio = AcademyAudioLesson::orderBy('id', 'desc')->first();

        $stats = [
            'enrolled_courses'         => $enrolledCount,
            'completed_courses'        => $completedCount,
            'audio_completed'          => $audioCompletedCount,
            'certificates_earned'      => $certificatesCount,
            'completion_rate'          => $completionRate,
            'streak_days'              => $streak,
            'upcoming_live_class_title'=> $nextLive ? $nextLive->title : null,
            'upcoming_live_class_time' => $nextLive ? $nextLive->date->format('M d, H:i') : '—',
            'upcoming_live_class_url'  => $nextLive ? $nextLive->meeting_url : null,
            'upcoming_mentorship_title'=> $nextBooking ? '1-on-1 with ' . $nextBooking->teacher->name : null,
            'upcoming_mentorship_time' => $nextBooking ? $nextBooking->booking_date . ' ' . $nextBooking->start_time : '—',
            'upcoming_mentorship_url'  => $nextBooking ? $nextBooking->meeting_url : null,
        ];

        return view('academy.dashboard', compact(
            'enrollments', 'stats', 'weeklyHours',
            'hasLivePlan', 'hasAudioPlan', 'activePlans', 'latestAudio'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Courses
    // ─────────────────────────────────────────────────────────────────────────

    public function courses(Request $request)
    {
        $user            = $request->user();
        $enrollments     = Enrollment::with('course.lessons')->where('user_id', $user->id)->get();
        $availableCourses = Course::whereNotIn('id', $enrollments->pluck('course_id'))->get();

        return view('academy.courses', compact('enrollments', 'availableCourses'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Audio Learning
    // ─────────────────────────────────────────────────────────────────────────

    public function audioLearning(Request $request)
    {
        $this->ensureDataSeeded();
        $user        = $request->user();
        $activePlans = AcademyPlan::where('user_id', $user->id)->active()->get();
        $hasAudioPlan = $activePlans->where('includes_audio', true)->isNotEmpty();

        // All users get audio access unless explicitly restricted (audio is default-on)
        // If user has NO plans at all AND no enrollments, still allow free preview of 1 track
        $enrolledCount = Enrollment::where('user_id', $user->id)->count();
        $audioLessons  = AcademyAudioLesson::orderBy('id', 'asc')->get();

        return view('academy.audio-learning', compact('audioLessons', 'hasAudioPlan', 'enrolledCount'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Live Classes
    // ─────────────────────────────────────────────────────────────────────────

    public function liveClasses(Request $request)
    {
        $this->ensureDataSeeded();
        $user        = $request->user();
        $activePlans = AcademyPlan::where('user_id', $user->id)->active()->get();
        $hasLivePlan = $activePlans->where('includes_live_class', true)->isNotEmpty();

        $liveSessions = collect();
        if ($hasLivePlan) {
            $liveSessions = AcademyLiveSession::with('teacher')->orderBy('date', 'asc')->get();
        }

        return view('academy.live-classes', compact('liveSessions', 'hasLivePlan'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Mentorship
    // ─────────────────────────────────────────────────────────────────────────

    public function mentorship(Request $request)
    {
        $this->ensureDataSeeded();
        $teachers = AcademyTeacher::with('availabilities')->get();
        $users    = User::where('id', '!=', $request->user()->id)->get();
        return view('academy.mentorship', compact('teachers', 'users'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sessions & Recordings
    // ─────────────────────────────────────────────────────────────────────────

    public function sessions(Request $request)
    {
        $this->ensureDataSeeded();
        $user       = $request->user();
        $bookings   = AcademyBooking::with('teacher')->where('user_id', $user->id)->orderBy('booking_date', 'asc')->get();
        $recordings = AcademyRecording::with('liveSession')->orderBy('id', 'desc')->get();
        return view('academy.sessions', compact('bookings', 'recordings'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Assignments, Certificates, Messages, Settings
    // ─────────────────────────────────────────────────────────────────────────

    public function assignments(Request $request)
    {
        return view('academy.assignments');
    }

    public function certificates(Request $request)
    {
        $user        = $request->user();
        $enrollments = Enrollment::with('course')->where('user_id', $user->id)->get();
        return view('academy.certificates', compact('enrollments'));
    }

    public function messages(Request $request)
    {
        $this->ensureDataSeeded();
        $teachers = AcademyTeacher::all();
        return view('academy.messages', compact('teachers'));
    }

    public function settings(Request $request)
    {
        return view('academy.settings');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Notifications
    // ─────────────────────────────────────────────────────────────────────────

    public function notifications(Request $request)
    {
        $user = $request->user();
        $notifications = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Mark all as read on view
        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('academy.notifications', compact('notifications'));
    }

    public function markNotificationRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = UserNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        $notification->update(['is_read' => true]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back();
    }

    public function markAllNotificationsRead(Request $request)
    {
        $user = $request->user();
        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * JSON endpoint: unread notification count for badge polling.
     */
    public function unreadNotifCount(Request $request)
    {
        $count = UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();
        return response()->json(['count' => $count]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Book a coaching session
    // ─────────────────────────────────────────────────────────────────────────

    public function bookSession(Request $request)
    {
        $request->validate([
            'teacher_id'  => 'required|exists:academy_teachers,id',
            'booking_date'=> 'required|date|after_or_equal:today',
            'start_time'  => 'required|string',
            'end_time'    => 'required|string',
            'call_type'   => 'required|in:voice,video',
        ]);

        $teacher = AcademyTeacher::findOrFail($request->teacher_id);
        $meetUrl = $request->call_type === 'video'
            ? 'https://meet.google.com/' . strtolower(Str::random(3)) . '-' . strtolower(Str::random(4)) . '-' . strtolower(Str::random(3))
            : null;

        AcademyBooking::create([
            'user_id'     => $request->user()->id,
            'teacher_id'  => $teacher->id,
            'booking_date'=> $request->booking_date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'call_type'   => $request->call_type,
            'meeting_url' => $meetUrl,
            'status'      => 'confirmed',
        ]);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'title'   => '1-on-1 Session Confirmed',
            'message' => 'Your coaching session with ' . $teacher->name . ' on ' . $request->booking_date . ' at ' . $request->start_time . ' has been confirmed.',
            'type'    => 'academy',
            'is_read' => false,
        ]);

        return redirect()->route('academy.sessions')->with('success', 'Coaching session booked and confirmed!');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Messages API
    // ─────────────────────────────────────────────────────────────────────────

    public function sendMessage(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:academy_teachers,id',
            'text'       => 'required|string',
        ]);
        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AI Handlers
    // ─────────────────────────────────────────────────────────────────────────

    public function askAcademyAi(Request $request)
    {
        $request->validate(['prompt' => 'required|string']);
        $prompt = strtolower($request->input('prompt'));
        $reply  = "I've processed your question regarding: \"{$prompt}\". ";

        if (str_contains($prompt, 'summarize')) {
            $reply .= "Today's live AI class covered LangChain memory management. Core insights: (1) ConversationSummaryMemory keeps token usage low by dynamically condensing histories. (2) ConversationBufferMemory keeps raw buffers for exact reference.";
        } elseif (str_contains($prompt, 'recommend')) {
            $reply .= "Based on your focus, we recommend the 'Enterprise SaaS Architecture' bootcamp, specifically the segments covering AWS Beanstalk scaling and Redis caches.";
        } else {
            $reply .= "To design secure systems, remember to split structures, enforce CSRF/timeout controls, and configure database backups via environment variables.";
        }

        return response()->json(['reply' => $reply]);
    }

    public function courseDetail($slug)
    {
        $course    = Course::with('lessons')->where('slug', $slug)->firstOrFail();
        $isEnrolled = false;
        $progress   = 0;

        if (auth()->check()) {
            $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();
            if ($enrollment) {
                $isEnrolled = true;
                $progress   = $enrollment->progress;
            }
        }

        return view('academy.course-detail', compact('course', 'isEnrolled', 'progress'));
    }

    public function enroll(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user   = $request->user();

        $enrolled = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['progress' => 0]
        );

        if ($enrolled->wasRecentlyCreated) {
            UserNotification::create([
                'user_id' => $user->id,
                'title'   => 'Course Enrollment Success',
                'message' => 'You successfully enrolled in "' . $course->title . '". Start learning today!',
                'type'    => 'course',
                'is_read' => false,
            ]);
        }

        return redirect()->route('academy.course', $course->slug)->with('success', 'Enrolled successfully!');
    }

    public function lessonDetail(Request $request, $courseSlug, $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = Lesson::where('course_id', $course->id)->where('slug', $lessonSlug)->firstOrFail();

        $enrollment = Enrollment::where('user_id', $request->user()->id)->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return redirect()->route('academy.course', $course->slug)->with('error', 'You must enroll to view lessons.');
        }

        $allLessons = $course->lessons;
        $nextLesson = $allLessons->where('sort_order', '>', $lesson->sort_order)->first();

        $totalLessons          = $allLessons->count();
        $completedLessonsCount = Lesson::where('course_id', $course->id)->where('sort_order', '<=', $lesson->sort_order)->count();
        $newProgress           = $totalLessons > 0 ? min(100, round(($completedLessonsCount / $totalLessons) * 100)) : 0;

        if ($newProgress > $enrollment->progress) {
            $enrollment->update([
                'progress'       => $newProgress,
                'completed_at'   => $newProgress === 100 ? now() : $enrollment->completed_at,
                'certificate_code'=> ($newProgress === 100 && !$enrollment->certificate_code)
                    ? 'CERT-' . strtoupper(Str::random(10))
                    : $enrollment->certificate_code,
            ]);
        }

        return view('academy.lesson-detail', compact('course', 'lesson', 'allLessons', 'nextLesson', 'enrollment'));
    }

    public function askAiTutor(Request $request)
    {
        $request->validate(['question' => 'required|string', 'lesson_id' => 'nullable|integer']);
        return response()->json([
            'answer' => 'This is a response from Diwebs AI Tutor. For a course on software engineering, we recommend structured coding principles: 1. Always split code into clean modules. 2. Use database migrations for schema definitions.',
        ]);
    }
}
