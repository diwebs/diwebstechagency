<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lead;
use App\Models\Course;
use App\Models\ExamSession;
use App\Models\SecurityLog;
use App\Models\CbtCenter;
use App\Models\Invoice;
use Illuminate\Http\Request;

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
}
