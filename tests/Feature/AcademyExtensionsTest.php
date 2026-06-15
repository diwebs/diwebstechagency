<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AcademyTeacher;
use App\Models\AcademyLiveSession;
use App\Models\AcademyBooking;
use App\Models\AcademyAudioLesson;
use App\Models\AcademyRecording;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademyExtensionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $admin;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => 'active'
        ]);

        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        $this->course = Course::create([
            'title' => 'Software Engineering Bootcamp',
            'slug' => 'software-engineering-bootcamp',
            'description' => 'A structured guide to cloud programming.',
            'instructor_name' => 'David Miller',
            'price' => 299.99,
            'difficulty' => 'Intermediate',
            'category' => 'Engineering'
        ]);
    }

    public function test_student_can_view_academy_dashboard_and_tabs(): void
    {
        // Enroll student first
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'progress' => 30
        ]);

        $response = $this->actingAs($this->student)->get(route('academy.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Academy Control Overview');

        // Check My Courses sub-page
        $response = $this->actingAs($this->student)->get(route('academy.courses'));
        $response->assertStatus(200);
        $response->assertSee('Active Learning Bootcamps');
        $response->assertSee('Software Engineering Bootcamp');

        // Check other subpages load ok
        $response = $this->actingAs($this->student)->get(route('academy.assignments'));
        $response->assertStatus(200);
        $response->assertSee('Course Assignments');

        $response = $this->actingAs($this->student)->get(route('academy.certificates'));
        $response->assertStatus(200);
        $response->assertSee('Earned Certificates');

        $response = $this->actingAs($this->student)->get(route('academy.messages'));
        $response->assertStatus(200);
        $response->assertSee('Instructor Chat');
    }

    public function test_audio_learning_lists_lessons_correctly(): void
    {
        $audio = AcademyAudioLesson::create([
            'course_id' => $this->course->id,
            'title' => 'Advanced DB Scaling Podcast',
            'slug' => 'advanced-db-scaling-podcast',
            'instructor_name' => 'David Miller',
            'duration_seconds' => 450,
            'audio_url' => 'https://example.com/audio.mp3',
            'format' => 'mp3',
            'summary' => 'This is a brief podcast summary.',
            'transcript' => 'Welcome to DB scaling.',
            'chapters' => []
        ]);

        $response = $this->actingAs($this->student)->get(route('academy.audio-learning'));
        $response->assertStatus(200);
        $response->assertSee('Advanced DB Scaling Podcast');
        $response->assertSee('David Miller');
    }

    public function test_live_classes_dashboard_displays_schedules(): void
    {
        $teacher = AcademyTeacher::create([
            'name' => 'David Miller',
            'expertise' => 'Laravel, Docker',
            'bio' => 'Senior architect.',
            'role' => 'instructor'
        ]);

        // 1. Check scheduled session (shows "Lock Pending")
        $sessionScheduled = AcademyLiveSession::create([
            'title' => 'Kubernetes Deployments Deep-Dive (Scheduled)',
            'teacher_id' => $teacher->id,
            'meeting_provider' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/xyz-uvwx-yza',
            'date' => now()->addHour(),
            'duration_minutes' => 60,
            'session_type' => 'group_session',
            'status' => 'scheduled'
        ]);

        // 2. Check live session (shows "Enter Class" and meeting URL)
        $sessionLive = AcademyLiveSession::create([
            'title' => 'Kubernetes Deployments Deep-Dive (Live)',
            'teacher_id' => $teacher->id,
            'meeting_provider' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/live-room-abc',
            'date' => now(),
            'duration_minutes' => 60,
            'session_type' => 'group_session',
            'status' => 'live'
        ]);

        $response = $this->actingAs($this->student)->get(route('academy.live-classes'));
        $response->assertStatus(200);
        $response->assertSee('Kubernetes Deployments Deep-Dive (Scheduled)');
        $response->assertSee('Lock Pending');
        $response->assertSee('Kubernetes Deployments Deep-Dive (Live)');
        $response->assertSee('Enter Class');
        $response->assertSee('https://meet.google.com/live-room-abc');
    }

    public function test_student_can_book_coaching_session(): void
    {
        $teacher = AcademyTeacher::create([
            'name' => 'Sarah Connor',
            'expertise' => 'LangChain, LLMs',
            'bio' => 'AI Engineer.',
            'role' => 'mentor'
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('academy.bookings.store'), [
                'teacher_id' => $teacher->id,
                'booking_date' => now()->addDays(5)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '15:00',
                'call_type' => 'video'
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('academy_bookings', [
            'user_id' => $this->student->id,
            'teacher_id' => $teacher->id,
            'call_type' => 'video',
            'status' => 'confirmed'
        ]);
    }

    public function test_admin_can_manage_teachers_and_live_sessions(): void
    {
        // 1. Store teacher
        $response = $this->actingAs($this->admin)
            ->post(route('admin.academy.teachers.store'), [
                'name' => 'Professor John',
                'expertise' => 'Go, Microservices',
                'bio' => 'Systems engineer.',
                'role' => 'mentor',
                'hourly_rate' => 85.00,
                'email' => 'john@test.com'
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('academy_teachers', [
            'name' => 'Professor John',
            'role' => 'mentor',
            'hourly_rate' => 85.00
        ]);

        $teacher = AcademyTeacher::where('name', 'Professor John')->first();

        // 2. Schedule live session
        $response = $this->actingAs($this->admin)
            ->post(route('admin.academy.live-sessions.store'), [
                'title' => 'Go Channels Synchronization',
                'teacher_id' => $teacher->id,
                'date' => now()->addDays(2)->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
                'meeting_provider' => 'google_meet',
                'session_type' => 'public_class',
                'description' => 'Handling parallel go routines.'
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('academy_live_sessions', [
            'title' => 'Go Channels Synchronization',
            'teacher_id' => $teacher->id,
            'meeting_provider' => 'google_meet'
        ]);

        $sess = AcademyLiveSession::where('title', 'Go Channels Synchronization')->first();

        // 3. Update session status to ended (which generates a playback recording)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.academy.live-sessions.status', $sess->id), [
                'status' => 'ended'
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('academy_recordings', [
            'live_session_id' => $sess->id,
            'title' => 'Go Channels Synchronization (Playback Recording)'
        ]);
    }

    public function test_student_can_ask_academy_ai_assistant(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('academy.ai.query'), [
                'prompt' => 'Please recommend a course'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        $response->assertSee('recommend');
    }
}
