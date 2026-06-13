<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
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
}
