<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherAvailability;
use App\Models\AcademyAudioLesson;
use App\Models\AcademyLiveSession;
use App\Models\AcademyBooking;
use App\Models\AcademyRecording;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AcademyController extends Controller
{
    /**
     * Seed sample data if tables are empty.
     */
    protected function ensureDataSeeded()
    {
        if (AcademyTeacher::count() === 0) {
            $t1 = AcademyTeacher::create([
                'name' => 'David Miller',
                'expertise' => 'Laravel, Docker, AWS, DB Normalization',
                'bio' => 'Core architect with 12+ years experience in enterprise cloud infrastructures and database clustering. Author of Laravel Enterprise Design Patterns.',
                'certifications' => ['AWS Solutions Architect', 'Docker Certified Associate'],
                'voice_only_enabled' => true,
                'video_enabled' => true,
                'hourly_rate' => 75.00,
                'role' => 'instructor',
                'avatar' => '👨‍💻',
                'email' => 'david.m@diwebstechagency.website'
            ]);

            $t2 = AcademyTeacher::create([
                'name' => 'Sarah Connor',
                'expertise' => 'Python, FastAPI, LangChain, PyTorch',
                'bio' => 'Research engineer focused on LangChain, vector databases, and private LLM model tuning. Previously at OpenAI.',
                'certifications' => ['TensorFlow Developer', 'Google Cloud ML Engineer'],
                'voice_only_enabled' => true,
                'video_enabled' => true,
                'hourly_rate' => 90.00,
                'role' => 'mentor',
                'avatar' => '👩‍💻',
                'email' => 'sarah.c@diwebstechagency.website'
            ]);

            $t3 = AcademyTeacher::create([
                'name' => 'Alan Turing',
                'expertise' => 'Cybersecurity, Cryptography, OAuth2, HSM',
                'bio' => 'Guest speaker specializing in zero-knowledge proofs, system encryptions, and security shields.',
                'certifications' => ['CISSP', 'CEH'],
                'voice_only_enabled' => true,
                'video_enabled' => false,
                'hourly_rate' => 120.00,
                'role' => 'guest_speaker',
                'avatar' => '👨‍🎨',
                'email' => 'alan.t@diwebstechagency.website'
            ]);

            // Seed Availabilities
            foreach ([$t1, $t2] as $t) {
                AcademyTeacherAvailability::create([
                    'teacher_id' => $t->id,
                    'day_of_week' => 1, // Mon
                    'start_time' => '09:00',
                    'end_time' => '17:00'
                ]);
                AcademyTeacherAvailability::create([
                    'teacher_id' => $t->id,
                    'day_of_week' => 3, // Wed
                    'start_time' => '10:00',
                    'end_time' => '18:00'
                ]);
            }
        }

        if (AcademyAudioLesson::count() === 0) {
            $course = Course::first();
            AcademyAudioLesson::create([
                'course_id' => $course ? $course->id : null,
                'title' => 'Enterprise Software Scaling Brief',
                'slug' => 'enterprise-software-scaling-brief',
                'instructor_name' => 'David Miller',
                'duration_seconds' => 720,
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'format' => 'mp3',
                'summary' => 'A comprehensive summary of enterprise software scaling techniques, database read-write isolation, and load balancer setups.',
                'transcript' => 'Welcome to the Enterprise Software Scaling Brief. Today we talk about replication. It is highly recommended to isolate database read queries from write queries using separate database nodes. Next, we discuss caching. Implementing Redis is critical to prevent database lockouts during traffic surges.',
                'chapters' => [
                    ['title' => 'Introduction', 'time' => 0],
                    ['title' => 'Database Replication', 'time' => 180],
                    ['title' => 'Caching with Redis', 'time' => 420],
                    ['title' => 'Load Balancing', 'time' => 600]
                ],
                'is_downloadable' => true
            ]);

            AcademyAudioLesson::create([
                'course_id' => $course ? $course->id : null,
                'title' => 'AI Prompt Engineering and LangChain',
                'slug' => 'ai-prompt-engineering-langchain',
                'instructor_name' => 'Sarah Connor',
                'duration_seconds' => 900,
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
                'format' => 'mp3',
                'summary' => 'Summary of prompt engineering strategies, zero-shot learning, and LangChain memory structures.',
                'transcript' => 'Hello everyone, welcome to the AI Prompt Engineering briefing. We cover prompt construction, zero-shot prompts, and few-shot templates. Then we analyze LangChain memory structures, including ConversationBufferMemory and ConversationSummaryMemory.',
                'chapters' => [
                    ['title' => 'Intro to Prompt Engineering', 'time' => 0],
                    ['title' => 'LangChain Framework', 'time' => 300],
                    ['title' => 'Vector Databases', 'time' => 600]
                ],
                'is_downloadable' => true
            ]);
        }

        if (AcademyLiveSession::count() === 0) {
            $t = AcademyTeacher::where('name', 'Sarah Connor')->first();
            $t2 = AcademyTeacher::where('name', 'David Miller')->first();
            
            AcademyLiveSession::create([
                'title' => 'Advanced AI Engineering Workshop',
                'teacher_id' => $t ? $t->id : null,
                'meeting_provider' => 'google_meet',
                'meeting_url' => 'https://meet.google.com/abc-defg-hij',
                'date' => now()->addMinutes(15),
                'duration_minutes' => 60,
                'session_type' => 'group_session',
                'status' => 'live',
                'description' => 'Deep dive session into LangChain memory management, agent logic flow, and production scaling with FastAPI.',
                'target_role' => 'all'
            ]);

            AcademyLiveSession::create([
                'title' => 'Database Normalization Sprint Session',
                'teacher_id' => $t2 ? $t2->id : null,
                'meeting_provider' => 'google_meet',
                'meeting_url' => 'https://meet.google.com/xyz-uvwx-yza',
                'date' => now()->addDays(2),
                'duration_minutes' => 90,
                'session_type' => 'public_class',
                'status' => 'scheduled',
                'description' => 'Designing optimal 3NF schemas, indexes optimization, and Laravel migration strategies for enterprise microservices.',
                'target_role' => 'all'
            ]);
        }

        if (AcademyRecording::count() === 0) {
            $sess = AcademyLiveSession::first();
            AcademyRecording::create([
                'live_session_id' => $sess ? $sess->id : null,
                'title' => 'Introduction to Cloud Hosting (Replay)',
                'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
                'notes' => 'Use AWS EC2 instances with auto-scaling triggers based on CPU utilization > 70%. Configure Cloudflare CDN edge rules to cache public static assets.',
                'ai_summary' => 'Summary: Cloud scaling requires load balancing, auto-scaling groups, and edge caching techniques to maintain 99.9% uptime.',
                'retention_days' => 30
            ]);
        }
    }

    public function dashboard(Request $request)
    {
        $this->ensureDataSeeded();
        $user = $request->user();
        
        $enrollments = Enrollment::with('course.lessons')->where('user_id', $user->id)->get();
        $enrolledCount = $enrollments->count();
        $completedCount = $enrollments->where('progress', 100)->count();
        $certificatesCount = $enrollments->whereNotNull('certificate_code')->count();
        
        // Find next live session
        $nextLive = AcademyLiveSession::with('teacher')
            ->whereIn('status', ['live', 'scheduled'])
            ->orderBy('date', 'asc')
            ->first();
            
        // Find next mentorship booking
        $nextBooking = AcademyBooking::with('teacher')
            ->where('user_id', $user->id)
            ->where('booking_date', '>=', now()->format('Y-m-d'))
            ->where('status', 'confirmed')
            ->orderBy('booking_date', 'asc')
            ->first();

        $stats = [
            'enrolled_courses' => $enrolledCount,
            'completed_courses' => $completedCount,
            'audio_completed' => AcademyAudioLesson::count(),
            'certificates_earned' => $certificatesCount,
            'upcoming_live_class_title' => $nextLive ? $nextLive->title : 'No Live Classes',
            'upcoming_live_class_time' => $nextLive ? $nextLive->date->format('M d, H:i') : '—',
            'upcoming_live_class_url' => $nextLive ? $nextLive->meeting_url : null,
            'upcoming_mentorship_title' => $nextBooking ? '1-on-1 with ' . $nextBooking->teacher->name : 'No Bookings',
            'upcoming_mentorship_time' => $nextBooking ? $nextBooking->booking_date . ' ' . $nextBooking->start_time : '—',
            'upcoming_mentorship_url' => $nextBooking ? $nextBooking->meeting_url : null,
        ];

        return view('academy.dashboard', compact('enrollments', 'stats'));
    }

    public function courses(Request $request)
    {
        $user = $request->user();
        $enrollments = Enrollment::with('course.lessons')->where('user_id', $user->id)->get();
        $availableCourses = Course::whereNotIn('id', $enrollments->pluck('course_id'))->get();

        return view('academy.courses', compact('enrollments', 'availableCourses'));
    }

    public function audioLearning(Request $request)
    {
        $this->ensureDataSeeded();
        $audioLessons = AcademyAudioLesson::orderBy('id', 'asc')->get();
        return view('academy.audio-learning', compact('audioLessons'));
    }

    public function liveClasses(Request $request)
    {
        $this->ensureDataSeeded();
        $liveSessions = AcademyLiveSession::with('teacher')->orderBy('date', 'asc')->get();
        return view('academy.live-classes', compact('liveSessions'));
    }

    public function mentorship(Request $request)
    {
        $this->ensureDataSeeded();
        $teachers = AcademyTeacher::with('availabilities')->get();
        $users = User::where('id', '!=', $request->user()->id)->get();
        return view('academy.mentorship', compact('teachers', 'users'));
    }

    public function sessions(Request $request)
    {
        $this->ensureDataSeeded();
        $user = $request->user();
        $bookings = AcademyBooking::with('teacher')->where('user_id', $user->id)->orderBy('booking_date', 'asc')->get();
        $recordings = AcademyRecording::with('liveSession')->orderBy('id', 'desc')->get();
        return view('academy.sessions', compact('bookings', 'recordings'));
    }

    public function assignments(Request $request)
    {
        return view('academy.assignments');
    }

    public function certificates(Request $request)
    {
        $user = $request->user();
        $enrollments = Enrollment::with('course')->where('user_id', $user->id)->get();
        return view('academy.certificates', compact('enrollments'));
    }

    public function messages(Request $request)
    {
        $this->ensureDataSeeded();
        $teachers = AcademyTeacher::all();
        return view('academy.messages', compact('teachers'));
    }

    public function notifications(Request $request)
    {
        return view('academy.notifications');
    }

    public function settings(Request $request)
    {
        return view('academy.settings');
    }

    /**
     * Book a new coaching session.
     */
    public function bookSession(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:academy_teachers,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'call_type' => 'required|in:voice,video'
        ]);

        $teacher = AcademyTeacher::findOrFail($request->teacher_id);
        
        // Auto-generate a simulated Meet URL
        $meetUrl = $request->call_type === 'video' 
            ? 'https://meet.google.com/' . strtolower(Str::random(3)) . '-' . strtolower(Str::random(4)) . '-' . strtolower(Str::random(3))
            : null;

        AcademyBooking::create([
            'user_id' => $request->user()->id,
            'teacher_id' => $teacher->id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'call_type' => $request->call_type,
            'meeting_url' => $meetUrl,
            'status' => 'confirmed' // Instant confirmed
        ]);

        return redirect()->route('academy.sessions')->with('success', 'Coaching session booked successfully! The meeting is confirmed.');
    }

    /**
     * API for messages.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:academy_teachers,id',
            'text' => 'required|string'
        ]);

        // Simulated storage or log
        return response()->json(['success' => true]);
    }

    /**
     * AI query handler.
     */
    public function askAcademyAi(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string'
        ]);

        $prompt = strtolower($request->input('prompt'));
        $reply = "I've processed your question regarding: \"{$prompt}\". ";

        if (str_contains($prompt, 'summarize')) {
            $reply .= "Today's live AI class covered LangChain memory management. Core insights: (1) ConversationSummaryMemory keeps token usage low by dynamically condensing histories. (2) ConversationBufferMemory keeps raw buffers for exact reference. We advise FastAPI hybrid structures for production APIs.";
        } elseif (str_contains($prompt, 'recommend')) {
            $reply .= "Based on your focus, we recommend the 'Enterprise SaaS Architecture' bootcamp, specifically the segments covering AWS Beanstalk scaling and Redis caches.";
        } else {
            $reply .= "To design secure systems, remember to split structures, enforce CSRF/timeout controls, and configure database backups via environment variables.";
        }

        return response()->json([
            'reply' => $reply
        ]);
    }

    // Existing LMS methods untouched
    public function courseDetail($slug)
    {
        $course = Course::with('lessons')->where('slug', $slug)->firstOrFail();
        $isEnrolled = false;
        $progress = 0;

        if (auth()->check()) {
            $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();
            if ($enrollment) {
                $isEnrolled = true;
                $progress = $enrollment->progress;
            }
        }

        return view('academy.course-detail', compact('course', 'isEnrolled', 'progress'));
    }

    public function enroll(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = $request->user();

        Enrollment::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'progress' => 0
        ]);

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

        // Increment progress slightly
        $totalLessons = $allLessons->count();
        $completedLessonsCount = Lesson::where('course_id', $course->id)
            ->where('sort_order', '<=', $lesson->sort_order)
            ->count();
        $newProgress = min(100, round(($completedLessonsCount / $totalLessons) * 100));

        if ($newProgress > $enrollment->progress) {
            $enrollment->update([
                'progress' => $newProgress,
                'completed_at' => $newProgress === 100 ? now() : $enrollment->completed_at,
                'certificate_code' => ($newProgress === 100 && !$enrollment->certificate_code) 
                    ? 'CERT-' . strtoupper(Str::random(10)) 
                    : $enrollment->certificate_code
            ]);
        }

        return view('academy.lesson-detail', compact('course', 'lesson', 'allLessons', 'nextLesson', 'enrollment'));
    }

    public function askAiTutor(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'lesson_id' => 'nullable|integer'
        ]);

        $question = $request->input('question');
        $response = "This is a response from Diwebs AI Tutor regarding your query. For a course on software engineering, we recommend structured coding principles: 1. Always split code into clean modules. 2. Use database migrations for schema definitions.";

        return response()->json([
            'answer' => $response
        ]);
    }
}
