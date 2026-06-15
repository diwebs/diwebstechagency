<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\SecurityLog;
use App\Models\CbtLiveExam;
use App\Models\CbtExamSession;
use App\Models\CbtExamAttempt;
use App\Models\CbtProctorLog;
use App\Models\CbtCenterEnrollment;
use App\Models\CbtCenterDevice;
use App\Models\CbtCandidateFlag;
use App\Models\CbtCertificate;
use App\Models\CbtCenter;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CbtController extends Controller
{
    public function startExam(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        $user = $request->user();

        // Check if there is already an active session
        $existingSession = ExamSession::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'scheduled'])
            ->first();

        if ($existingSession) {
            if ($existingSession->status === 'scheduled') {
                $existingSession->update([
                    'status' => 'active',
                    'started_at' => now(),
                ]);
            }
            return redirect()->route('cbt.exam.session', $existingSession->id);
        }

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now(),
            'anti_cheat_flags' => 0,
            'logs' => [],
        ]);

        return redirect()->route('cbt.exam.session', $session->id);
    }

    public function showSession($sessionId)
    {
        $session = ExamSession::with(['exam.questions', 'user'])->findOrFail($sessionId);

        // Check if session has ended
        if ($session->status === 'completed' || $session->status === 'void' || $session->status === 'flagged') {
            return redirect()->route('cbt.results', $session->id)->with('error', 'This exam session has already ended.');
        }

        // Check duration and auto-submit if time exceeded
        $exam = $session->exam;
        $elapsedMinutes = now()->diffInMinutes($session->started_at);
        if ($elapsedMinutes >= $exam->duration_minutes) {
            $this->autoGradeSession($session, []);
            return redirect()->route('cbt.results', $session->id)->with('warning', 'Time limit exceeded. Exam submitted automatically.');
        }

        return view('cbt.exam-interface', compact('session', 'exam'));
    }

    public function logSecurityEvent(Request $request, $sessionId)
    {
        $session = ExamSession::findOrFail($sessionId);
        $user = $request->user();
        
        $validated = $request->validate([
            'event_type' => 'required|string',
            'details' => 'nullable|array'
        ]);

        // Create log record
        SecurityLog::create([
            'user_id' => $user->id,
            'exam_session_id' => $session->id,
            'event_type' => $validated['event_type'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => $validated['details'] ?? []
        ]);

        // Append to exam session logs
        $currentLogs = $session->logs ?? [];
        $currentLogs[] = [
            'timestamp' => now()->toIso8601String(),
            'event' => $validated['event_type'],
            'details' => $validated['details'] ?? []
        ];

        $incrementFlags = 0;
        if (in_array($validated['event_type'], ['tab_switch', 'webcam_verification_failed', 'fullscreen_exit'])) {
            $incrementFlags = 1;
        }

        $session->update([
            'logs' => $currentLogs,
            'anti_cheat_flags' => $session->anti_cheat_flags + $incrementFlags
        ]);

        // Auto-terminate session if flags exceed a limit (e.g. 5 tab switches)
        if ($session->anti_cheat_flags >= 5) {
            $this->autoGradeSession($session, $request->input('answers', []));
            $session->update(['status' => 'flagged']);
            return response()->json([
                'status' => 'terminated',
                'message' => 'Exam terminated due to multiple security violations.'
            ]);
        }

        return response()->json(['status' => 'success', 'flags' => $session->anti_cheat_flags]);
    }

    public function submitExam(Request $request, $sessionId)
    {
        $session = ExamSession::findOrFail($sessionId);
        $answers = $request->input('answers', []);

        $this->autoGradeSession($session, $answers);

        return redirect()->route('cbt.results', $session->id);
    }

    private function autoGradeSession(ExamSession $session, array $answers)
    {
        $exam = Exam::with('questions')->findOrFail($session->exam_id);
        $questions = $exam->questions;
        $totalQuestionsCount = $questions->count();
        
        if ($totalQuestionsCount === 0) {
            $score = 0.00;
        } else {
            $correctCount = 0;
            foreach ($questions as $question) {
                $qId = $question->id;
                $submittedAnswer = $answers[$qId] ?? null; // Can be string or array
                $correctAnswers = $question->correct_answers; // array

                // Compare answers
                if ($question->question_type === 'single_choice') {
                    if (is_array($submittedAnswer)) {
                        $submittedAnswer = $submittedAnswer[0] ?? null;
                    }
                    $correctAnswer = $correctAnswers[0] ?? null;
                    if ($submittedAnswer !== null && strtolower(trim($submittedAnswer)) === strtolower(trim($correctAnswer))) {
                        $correctCount++;
                    }
                } elseif ($question->question_type === 'multiple_choice') {
                    // Check if array matches
                    if (is_array($submittedAnswer)) {
                        sort($submittedAnswer);
                        sort($correctAnswers);
                        if ($submittedAnswer === $correctAnswers) {
                            $correctCount++;
                        }
                    }
                }
            }

            $score = ($correctCount / $totalQuestionsCount) * 100;
        }

        $status = 'completed';
        if ($session->anti_cheat_flags >= 5) {
            $status = 'flagged';
        }

        $session->update([
            'status' => $status,
            'score' => round($score, 2),
            'ended_at' => now()
        ]);
    }

    public function showResults($sessionId)
    {
        $session = ExamSession::with(['exam', 'user'])->findOrFail($sessionId);
        return view('cbt.results', compact('session'));
    }

    /**
     * Enhanced CBT Candidate/Partner Dashboard overview
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $this->ensureCbtDataSeeded($user);

        // Fetch standard candidate data
        $exams = Exam::where('is_active', true)->get();
        
        // Extended CBT Sessions (candidate attempts)
        $extendedSessions = CbtExamSession::with('exam')->where('user_id', $user->id)->get();
        $activeCount = $extendedSessions->where('status', 'active')->count();
        $completedCount = $extendedSessions->where('status', 'completed')->count();
        $avgScore = $extendedSessions->where('status', 'completed')->avg('score') ?? 0;
        
        $certificatesCount = CbtCertificate::where('user_id', $user->id)->count();
        $practiceCount = $extendedSessions->where('exam_mode', 'practice')->where('status', 'completed')->count();

        // Check enrollment / center ownership
        $center = CbtCenter::where('owner_id', $user->id)->first();
        $isPartner = !is_null($center);
        $enrollment = CbtCenterEnrollment::where('user_id', $user->id)->first();

        // Build stats array
        $stats = [
            'active_exams' => $activeCount,
            'completed_exams' => $completedCount,
            'avg_score' => round($avgScore, 1),
            'certificates_earned' => $certificatesCount,
            'practice_completed' => $practiceCount,
            'upcoming_exams' => CbtLiveExam::where('status', 'scheduled')->count()
        ];

        // Fetch upcoming live sessions
        $upcomingLive = CbtLiveExam::with('exam')->where('status', 'scheduled')->orderBy('scheduled_at', 'asc')->first();

        return view('cbt.dashboard', compact('exams', 'extendedSessions', 'isPartner', 'enrollment', 'stats', 'upcomingLive'));
    }

    public function practiceTests(Request $request)
    {
        $user = $request->user();
        $this->ensureCbtDataSeeded($user);
        $exams = Exam::where('is_active', true)->get();
        return view('cbt.practice-tests', compact('exams'));
    }

    public function liveExams(Request $request)
    {
        $user = $request->user();
        $this->ensureCbtDataSeeded($user);
        $liveExams = CbtLiveExam::with('exam')->whereIn('status', ['scheduled', 'active'])->get();
        return view('cbt.live-exams', compact('liveExams'));
    }

    public function scheduledExams(Request $request)
    {
        $user = $request->user();
        $exams = Exam::where('is_active', true)->get();
        return view('cbt.exams', compact('exams'));
    }

    public function resultsHistory(Request $request)
    {
        $user = $request->user();
        $extendedSessions = CbtExamSession::with('exam')->where('user_id', $user->id)->orderBy('started_at', 'desc')->get();
        return view('cbt.results-list', compact('extendedSessions'));
    }

    public function certificatesList(Request $request)
    {
        $user = $request->user();
        $certificates = CbtCertificate::with('exam')->where('user_id', $user->id)->get();
        return view('cbt.certificates', compact('certificates'));
    }

    public function sessionsList(Request $request)
    {
        $user = $request->user();
        $extendedSessions = CbtExamSession::with(['exam', 'center'])->where('user_id', $user->id)->orderBy('started_at', 'desc')->get();
        return view('cbt.sessions', compact('extendedSessions'));
    }

    public function notificationsFeed(Request $request)
    {
        return view('cbt.notifications');
    }

    public function profileInfo(Request $request)
    {
        return view('cbt.profile');
    }

    public function downloadCertificate(Request $request, $id)
    {
        $certificate = CbtCertificate::with(['exam', 'user'])->findOrFail($id);
        return view('cbt.certificate-download', compact('certificate'));
    }

    public function liveLobby(Request $request, $id)
    {
        $liveExam = CbtLiveExam::with('exam')->findOrFail($id);
        return view('cbt.live-lobby', compact('liveExam'));
    }

    public function startLiveExam(Request $request, $id)
    {
        $liveExam = CbtLiveExam::findOrFail($id);
        $user = $request->user();
        $uuid = (string) \Illuminate\Support\Str::uuid();

        // Create extended session first so standard event created hook skips duplicate
        $session = CbtExamSession::create([
            'id' => $uuid,
            'exam_id' => $liveExam->exam_id,
            'user_id' => $user->id,
            'cbt_live_exam_id' => $liveExam->id,
            'exam_mode' => 'live',
            'status' => 'active',
            'started_at' => now(),
            'anti_cheat_flags' => 0
        ]);

        // Create parent ExamSession next
        ExamSession::create([
            'id' => $uuid,
            'exam_id' => $liveExam->exam_id,
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now(),
            'anti_cheat_flags' => 0,
            'logs' => []
        ]);

        return redirect()->route('cbt.exam.session', $session->id);
    }

    public function startPracticeExam(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $user = $request->user();
        $uuid = (string) \Illuminate\Support\Str::uuid();

        // Create extended session first
        $session = CbtExamSession::create([
            'id' => $uuid,
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'exam_mode' => 'practice',
            'status' => 'active',
            'started_at' => now(),
            'anti_cheat_flags' => 0
        ]);

        // Create parent ExamSession next
        ExamSession::create([
            'id' => $uuid,
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now(),
            'anti_cheat_flags' => 0,
            'logs' => []
        ]);

        return redirect()->route('cbt.exam.session', $session->id);
    }

    public function centerEnrollment(Request $request)
    {
        $user = $request->user();
        $enrollment = CbtCenterEnrollment::where('user_id', $user->id)->first();
        return view('cbt.center-enrollment', compact('enrollment'));
    }

    public function storeCenterEnrollment(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'organization_name' => 'required|string',
            'center_type' => 'required|string',
            'has_physical_location' => 'required|string',
            'systems_count' => 'required|string',
            'internet_quality' => 'required|string',
            'power_backup' => 'required|string',
        ]);

        $enrollment = CbtCenterEnrollment::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($validated, ['status' => 'pending'])
        );

        // Also create a notification inside academy/notifications or admin systems
        return redirect()->route('cbt.dashboard')->with('success', 'Your application to Become a CBT Center Partner has been received! Our inspect team will review your infrastructure parameters.');
    }

    public function cbtCenters(Request $request)
    {
        $user = $request->user();
        $this->ensureCbtDataSeeded($user);
        $centers = CbtCenter::where('status', 'active')->get();
        return view('cbt.cbt-centers', compact('centers'));
    }

    public function institutionManagement(Request $request)
    {
        $user = $request->user();
        $exams = Exam::where('is_active', true)->get();
        return view('cbt.institution-management', compact('exams'));
    }

    public function storeInstitutionExam(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'code' => 'required|string|unique:exams,code',
            'duration_minutes' => 'required|integer|min:5',
            'passing_score' => 'required|numeric'
        ]);

        Exam::create([
            'title' => $request->title,
            'description' => 'Custom institution exam created via manager portal.',
            'code' => $request->code,
            'duration_minutes' => $request->duration_minutes,
            'total_questions' => 0,
            'passing_score' => $request->passing_score,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Custom examination created successfully! Proceed to upload question blocks.');
    }

    public function storeInstitutionQuestion(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'correct_answer' => 'required|string'
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'question_text' => $request->question_text,
            'options' => [
                ['id' => 'A', 'text' => $request->option_a],
                ['id' => 'B', 'text' => $request->option_b]
            ],
            'correct_answers' => [$request->correct_answer]
        ]);

        $exam->increment('total_questions');

        return redirect()->back()->with('success', 'Question added to syllabus successfully.');
    }

    // ════════════════════════════════════════════════════════════════════════
    // PARTNER PORTS
    // ════════════════════════════════════════════════════════════════════════

    public function partnerDashboard(Request $request)
    {
        $user = $request->user();
        $this->ensureCbtDataSeeded($user);

        $center = CbtCenter::where('owner_id', $user->id)->first();
        if (!$center) {
            return redirect()->route('cbt.center-enrollment')->with('error', 'You must become a certified CBT Center Partner first.');
        }

        $devices = CbtCenterDevice::where('cbt_center_id', $center->id)->get();
        $sessions = CbtExamSession::with(['exam', 'user'])->where('cbt_center_id', $center->id)->get();

        $stats = [
            'total_seats' => $center->capacity,
            'active_candidates' => $sessions->where('status', 'active')->count(),
            'scheduled_exams' => CbtLiveExam::where('status', 'scheduled')->count(),
            'revenue' => $center->revenue,
            'devices_online' => $devices->where('system_status', 'online')->count()
        ];

        return view('cbt.partner.dashboard', compact('center', 'devices', 'sessions', 'stats'));
    }

    public function partnerCenters(Request $request)
    {
        $user = $request->user();
        $centers = CbtCenter::where('owner_id', $user->id)->get();
        return view('cbt.partner.centers', compact('centers'));
    }

    public function partnerCenterSeats(Request $request, $centerId)
    {
        $user = $request->user();
        $center = CbtCenter::where('owner_id', $user->id)->findOrFail($centerId);
        $devices = CbtCenterDevice::where('cbt_center_id', $center->id)->get();
        return view('cbt.partner.seats', compact('center', 'devices'));
    }

    public function partnerCandidates(Request $request)
    {
        $user = $request->user();
        $center = CbtCenter::where('owner_id', $user->id)->first();
        if (!$center) {
            return redirect()->route('cbt.center-enrollment');
        }

        $sessions = CbtExamSession::with(['exam', 'user', 'flags'])->where('cbt_center_id', $center->id)->get();
        return view('cbt.partner.candidates', compact('sessions', 'center'));
    }

    public function partnerWarnCandidate(Request $request, $sessionId)
    {
        $session = CbtExamSession::findOrFail($sessionId);
        CbtProctorLog::create([
            'cbt_exam_session_id' => $session->id,
            'proctor_id' => $request->user()->id,
            'action_type' => 'warn',
            'message' => $request->input('message', 'Webcam verification warning: Please face your terminal direct.')
        ]);

        return response()->json(['success' => true]);
    }

    public function partnerTerminateCandidate(Request $request, $sessionId)
    {
        $session = CbtExamSession::findOrFail($sessionId);
        $session->update(['status' => 'void']);

        CbtProctorLog::create([
            'cbt_exam_session_id' => $session->id,
            'proctor_id' => $request->user()->id,
            'action_type' => 'terminate',
            'message' => 'Terminal lock triggered: Proctor terminated the session immediately.'
        ]);

        return response()->json(['success' => true]);
    }

    public function partnerRevenue(Request $request)
    {
        $user = $request->user();
        $center = CbtCenter::where('owner_id', $user->id)->first();
        return view('cbt.partner.revenue', compact('center'));
    }

    public function partnerReports(Request $request)
    {
        $user = $request->user();
        $center = CbtCenter::where('owner_id', $user->id)->first();
        return view('cbt.partner.reports', compact('center'));
    }

    public function partnerSettings(Request $request)
    {
        $user = $request->user();
        $center = CbtCenter::where('owner_id', $user->id)->first();
        return view('cbt.partner.settings', compact('center'));
    }

    /**
     * Seeds sample data for testing interfaces instantly
     */
    protected function ensureCbtDataSeeded(User $user)
    {
        if (Exam::count() <= 1) {
            $e1 = Exam::create([
                'title' => 'AWS Certified Cloud Practitioner (Mock Practice)',
                'description' => 'Practice exam covering basic cloud architecture, billing support models, and VPC security groups. Includes instant answers mode.',
                'code' => 'AWS-CLF-C02',
                'duration_minutes' => 90,
                'total_questions' => 2,
                'passing_score' => 70.00,
                'is_active' => true,
                'settings' => ['mode' => 'practice']
            ]);

            Question::create([
                'exam_id' => $e1->id,
                'question_text' => 'Which AWS service is used to distribute traffic across multiple EC2 instances?',
                'question_type' => 'single_choice',
                'options' => [
                    ['id' => 'A', 'text' => 'AWS Route 53'],
                    ['id' => 'B', 'text' => 'Elastic Load Balancing (ELB)'],
                    ['id' => 'C', 'text' => 'Amazon CloudFront'],
                    ['id' => 'D', 'text' => 'AWS Auto Scaling']
                ],
                'correct_answers' => ['B'],
                'explanation' => 'Elastic Load Balancing distributes incoming application traffic across multiple targets, such as Amazon EC2 instances.',
                'difficulty' => 'easy'
            ]);

            Question::create([
                'exam_id' => $e1->id,
                'question_text' => 'True or False: S3 storage classes include S3 Glacier for long-term archiving.',
                'question_type' => 'single_choice',
                'options' => [
                    ['id' => 'A', 'text' => 'True'],
                    ['id' => 'B', 'text' => 'False']
                ],
                'correct_answers' => ['A'],
                'explanation' => 'S3 Glacier is a secure, durable, and low-cost storage class for data archiving and long-term backup.',
                'difficulty' => 'easy'
            ]);

            $e2 = Exam::create([
                'title' => 'National IT Scholarship & Recruitment Test (Live Proctored)',
                'description' => 'National selection exam for IT trainees. Strictly timed, webcam required, window swaps monitored by live proctors.',
                'code' => 'NAT-IT-2026',
                'duration_minutes' => 60,
                'total_questions' => 2,
                'passing_score' => 60.00,
                'is_active' => true,
                'settings' => ['mode' => 'live']
            ]);

            Question::create([
                'exam_id' => $e2->id,
                'question_text' => 'What is the default port for secure HTTPS communication?',
                'question_type' => 'single_choice',
                'options' => [
                    ['id' => 'A', 'text' => '80'],
                    ['id' => 'B', 'text' => '8080'],
                    ['id' => 'C', 'text' => '443'],
                    ['id' => 'D', 'text' => '22']
                ],
                'correct_answers' => ['C'],
                'explanation' => 'HTTPS uses TCP port 443 by default to securely encrypt network communication.',
                'difficulty' => 'easy'
            ]);

            Question::create([
                'exam_id' => $e2->id,
                'question_text' => 'Which SQL keyword is used to sort the result-set in descending order?',
                'question_type' => 'single_choice',
                'options' => [
                    ['id' => 'A', 'text' => 'SORT DESC'],
                    ['id' => 'B', 'text' => 'ORDER BY DESC'],
                    ['id' => 'C', 'text' => 'DESC'],
                    ['id' => 'D', 'text' => 'SORT BY DESC']
                ],
                'correct_answers' => ['C'],
                'explanation' => 'In standard SQL, adding the DESC keyword after ORDER BY sorts the result-set in descending order.',
                'difficulty' => 'medium'
            ]);
        }

        if (CbtLiveExam::count() === 0) {
            $liveExamInfo = Exam::where('code', 'NAT-IT-2026')->first();
            if ($liveExamInfo) {
                CbtLiveExam::create([
                    'exam_id' => $liveExamInfo->id,
                    'scheduled_at' => now()->addMinutes(15),
                    'proctor_id' => User::where('role', 'super_admin')->first()?->id,
                    'camera_required' => true,
                    'mic_required' => true,
                    'browser_lock_required' => true,
                    'status' => 'scheduled'
                ]);
            }
        }

        if (CbtCenter::where('owner_id', $user->id)->count() === 0) {
            $center = CbtCenter::create([
                'owner_id' => $user->id,
                'name' => 'Diwebs Premier Lagos CBT Center',
                'code' => 'CBT-LOS-001',
                'address' => '15 Herbert Macaulay Road, Yaba',
                'city' => 'Lagos',
                'capacity' => 150,
                'contact_email' => 'lagoshub@diwebstechagency.website',
                'contact_phone' => '+2349064130817',
                'status' => 'active',
                'center_type' => 'jamb_style',
                'has_physical_location' => 'yes',
                'systems_count' => '100+',
                'internet_quality' => 'enterprise',
                'power_backup' => 'full_redundancy',
                'commission_rate' => 15.00,
                'revenue' => 4500.00
            ]);

            for ($i = 1; $i <= 5; $i++) {
                CbtCenterDevice::create([
                    'cbt_center_id' => $center->id,
                    'seat_number' => 'SEAT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'device_name' => 'Terminal-LOS-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'ip_address' => '192.168.1.' . (10 + $i),
                    'system_status' => $i === 3 ? 'testing' : 'online',
                    'cpu_usage' => rand(5, 45),
                    'ram_usage' => rand(20, 60),
                    'webcam_status' => 'active',
                    'battery_level' => 100
                ]);
            }
        }

        $anotherUser = User::where('id', '!=', $user->id)->first();
        if ($anotherUser) {
            $liveExamInfo = Exam::where('code', 'NAT-IT-2026')->first();
            $center = CbtCenter::where('owner_id', $user->id)->first();
            if ($liveExamInfo && $center) {
                $mockSession = CbtExamSession::firstOrCreate([
                    'exam_id' => $liveExamInfo->id,
                    'user_id' => $anotherUser->id,
                    'cbt_center_id' => $center->id,
                    'exam_mode' => 'live',
                    'status' => 'active',
                ], [
                    'score' => null,
                    'anti_cheat_flags' => 2,
                    'started_at' => now()->subMinutes(10),
                ]);

                if (CbtCandidateFlag::where('cbt_exam_session_id', $mockSession->id)->count() === 0) {
                    CbtCandidateFlag::create([
                        'cbt_exam_session_id' => $mockSession->id,
                        'violation_type' => 'tab_switch',
                        'details' => 'Candidate switched tabs to search engine.'
                    ]);
                    CbtCandidateFlag::create([
                        'cbt_exam_session_id' => $mockSession->id,
                        'violation_type' => 'multiple_faces',
                        'details' => 'Second face detected in candidate camera frame.'
                    ]);
                }
            }
        }

        if (CbtCertificate::where('user_id', $user->id)->count() === 0) {
            $mockExam = Exam::first();
            if ($mockExam) {
                CbtCertificate::create([
                    'user_id' => $user->id,
                    'exam_id' => $mockExam->id,
                    'certificate_number' => 'CERT-CBT-' . strtoupper(Str::random(10)),
                    'grade' => 85.00,
                    'issue_date' => now()->subDays(5),
                    'qr_code_content' => 'https://diwebstechagency.website/verify/cert/' . Str::random(12)
                ]);
            }
        }
    }
}
