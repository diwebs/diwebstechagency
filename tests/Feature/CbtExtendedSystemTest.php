<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\CbtCenter;
use App\Models\CbtCenterEnrollment;
use App\Models\CbtCenterDevice;
use App\Models\CbtLiveExam;
use App\Models\CbtExamSession;
use App\Models\CbtCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CbtExtendedSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $candidate;
    protected User $partner;
    protected User $admin;
    protected Exam $exam1;
    protected Exam $exam2;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create users
        $this->candidate = User::create([
            'name' => 'Candidate User',
            'email' => 'candidate@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'status' => 'active'
        ]);

        $this->partner = User::create([
            'name' => 'Partner User',
            'email' => 'partner@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'status' => 'active'
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        // 2. Create exams
        $this->exam1 = Exam::create([
            'title' => 'AWS Cloud Practice Exam',
            'description' => 'Mock questions AWS certified.',
            'code' => 'AWS-CLF-C02',
            'duration_minutes' => 90,
            'total_questions' => 2,
            'passing_score' => 70.00,
            'is_active' => true,
            'settings' => ['mode' => 'practice']
        ]);

        $this->exam2 = Exam::create([
            'title' => 'National IT Scholarship & Recruitment Test',
            'description' => 'Timed recruitment test.',
            'code' => 'NAT-IT-2026',
            'duration_minutes' => 60,
            'total_questions' => 2,
            'passing_score' => 60.00,
            'is_active' => true,
            'settings' => ['mode' => 'live']
        ]);
    }

    public function test_candidate_can_access_cbt_dashboards(): void
    {
        $response = $this->actingAs($this->candidate)->get(route('cbt.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('AWS Cloud Practice Exam');

        $response = $this->actingAs($this->candidate)->get(route('cbt.practice-tests'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->candidate)->get(route('cbt.live-exams'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->candidate)->get(route('cbt.exams'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->candidate)->get(route('cbt.results.history'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->candidate)->get(route('cbt.certificates'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->candidate)->get(route('cbt.sessions'));
        $response->assertStatus(200);
    }

    public function test_candidate_can_start_practice_exam_and_log_security_events(): void
    {
        // Start practice exam
        $response = $this->actingAs($this->candidate)
            ->post(route('cbt.practice-tests.start', $this->exam1->id));

        $response->assertStatus(302);
        
        // Assert session created
        $session = CbtExamSession::where('user_id', $this->candidate->id)
            ->where('exam_id', $this->exam1->id)
            ->where('exam_mode', 'practice')
            ->first();

        $this->assertNotNull($session);

        // Find parent session mapping if applicable
        $parentSession = ExamSession::where('user_id', $this->candidate->id)
            ->where('exam_id', $this->exam1->id)
            ->first();
        
        $this->assertNotNull($parentSession);

        // Log security events
        $logResponse = $this->actingAs($this->candidate)
            ->post(route('cbt.exam.log-event', $parentSession->id), [
                'event_type' => 'tab_switch',
                'details' => ['tab_title' => 'Google Search']
            ]);

        $logResponse->assertStatus(200);
        $logResponse->assertJson(['status' => 'success']);

        $parentSession->refresh();
        $this->assertEquals(1, $parentSession->anti_cheat_flags);
    }

    public function test_candidate_anti_cheat_flags_auto_terminate(): void
    {
        // Setup session
        $parentSession = ExamSession::create([
            'exam_id' => $this->exam1->id,
            'user_id' => $this->candidate->id,
            'status' => 'active',
            'started_at' => now(),
            'anti_cheat_flags' => 4
        ]);

        // Trigger 5th flag event
        $logResponse = $this->actingAs($this->candidate)
            ->post(route('cbt.exam.log-event', $parentSession->id), [
                'event_type' => 'tab_switch',
                'details' => ['tab_title' => 'Google Search']
            ]);

        $logResponse->assertStatus(200);
        $logResponse->assertJson(['status' => 'terminated']);

        $parentSession->refresh();
        $this->assertEquals('flagged', $parentSession->status);
    }

    public function test_partner_center_enrollment_and_approval(): void
    {
        // 1. Submit application to become a partner
        $response = $this->actingAs($this->partner)
            ->post(route('cbt.center-enrollment.store'), [
                'organization_name' => 'Lagos Tech Hub Ltd',
                'center_type' => 'jamb_style',
                'has_physical_location' => 'yes',
                'systems_count' => '100+',
                'internet_quality' => 'enterprise',
                'power_backup' => 'full_redundancy'
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('cbt_center_enrollments', [
            'user_id' => $this->partner->id,
            'organization_name' => 'Lagos Tech Hub Ltd',
            'status' => 'pending'
        ]);

        $enrollment = CbtCenterEnrollment::where('user_id', $this->partner->id)->first();

        // 2. Admin inspects and approves the application
        $approveResponse = $this->actingAs($this->admin)
            ->post(route('admin.cbt.enrollment.status', $enrollment->id), [
                'status' => 'approved'
            ]);

        $approveResponse->assertStatus(302);

        $enrollment->refresh();
        $this->assertEquals('approved', $enrollment->status);

        // Check if physical center got spawned automatically
        $this->assertDatabaseHas('cbt_centers', [
            'owner_id' => $this->partner->id,
            'name' => 'Lagos Tech Hub Ltd Certification Center'
        ]);
    }

    public function test_partner_monitoring_dashboard_and_proctor_actions(): void
    {
        // Setup center owned by partner
        $center = CbtCenter::create([
            'owner_id' => $this->partner->id,
            'name' => 'Lagos Hub Center',
            'code' => 'CBT-LAG-TEST',
            'address' => 'Yaba',
            'city' => 'Lagos',
            'capacity' => 100,
            'contact_email' => 'partner@test.com',
            'contact_phone' => '080',
            'status' => 'active',
            'center_type' => 'jamb_style',
            'has_physical_location' => 'yes',
            'systems_count' => '100+',
            'internet_quality' => 'enterprise',
            'power_backup' => 'full_redundancy',
            'commission_rate' => 12.50,
            'revenue' => 500.00
        ]);

        // Setup center devices
        CbtCenterDevice::create([
            'cbt_center_id' => $center->id,
            'seat_number' => 'SEAT-001',
            'device_name' => 'Client PC 1',
            'ip_address' => '192.168.1.5',
            'system_status' => 'online',
            'cpu_usage' => 10,
            'ram_usage' => 30,
            'webcam_status' => 'active',
            'battery_level' => 100
        ]);

        // Setup examinee session
        $examSession = CbtExamSession::create([
            'exam_id' => $this->exam1->id,
            'user_id' => $this->candidate->id,
            'cbt_center_id' => $center->id,
            'exam_mode' => 'live',
            'status' => 'active',
            'started_at' => now(),
            'anti_cheat_flags' => 1
        ]);

        // Access partner views
        $response = $this->actingAs($this->partner)->get(route('cbt.partner.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Lagos Hub Center');

        $response = $this->actingAs($this->partner)->get(route('cbt.partner.centers'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->partner)->get(route('cbt.partner.centers.seats', $center->id));
        $response->assertStatus(200);
        $response->assertSee('SEAT-001');

        $response = $this->actingAs($this->partner)->get(route('cbt.partner.candidates'));
        $response->assertStatus(200);

        // Proctor issues warning
        $warnResponse = $this->actingAs($this->partner)
            ->post(route('cbt.partner.candidates.warn', $examSession->id), [
                'message' => 'Please face the terminal screen.'
            ]);

        $warnResponse->assertStatus(200);
        $warnResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('cbt_proctor_logs', [
            'cbt_exam_session_id' => $examSession->id,
            'action_type' => 'warn',
            'message' => 'Please face the terminal screen.'
        ]);

        // Proctor terminates candidate
        $termResponse = $this->actingAs($this->partner)
            ->post(route('cbt.partner.candidates.terminate', $examSession->id));

        $termResponse->assertStatus(200);
        $termResponse->assertJson(['success' => true]);

        $examSession->refresh();
        $this->assertEquals('void', $examSession->status);
    }

    public function test_super_admin_can_schedule_live_exams(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.cbt.command'));
        $response->assertStatus(200);

        $scheduleResponse = $this->actingAs($this->admin)
            ->post(route('admin.cbt.live-exam.store'), [
                'exam_id' => $this->exam2->id,
                'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'camera_required' => '1',
                'mic_required' => '1',
                'browser_lock_required' => '1'
            ]);

        $scheduleResponse->assertStatus(302);

        $this->assertDatabaseHas('cbt_live_exams', [
            'exam_id' => $this->exam2->id,
            'camera_required' => 1,
            'mic_required' => 1,
            'browser_lock_required' => 1,
            'status' => 'scheduled'
        ]);
    }
}
