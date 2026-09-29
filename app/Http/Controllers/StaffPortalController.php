<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\StaffRole;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffPayroll;
use App\Models\StaffPerformanceReview;
use App\Models\StaffLeave;
use App\Models\StaffActivityLog;
use App\Models\Project;
use App\Models\Course;
use App\Models\Message;
use App\Helpers\TotpHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class StaffPortalController extends Controller
{
    private function logStaffAction($action, $description, $staffId = null)
    {
        $id = $staffId ?? (auth()->guard('staff')->check() ? auth()->guard('staff')->id() : null);
        StaffActivityLog::create([
            'staff_id' => $id,
            'action' => $action,
            'description' => 'Staff: ' . $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function showLogin()
    {
        if (auth()->guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }

        // Generate Math Captcha
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        Session::put('staff_captcha_answer', $num1 + $num2);

        $captchaText = "What is {$num1} + {$num2}?";

        return view('staff.login', compact('captchaText'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_field' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|integer',
        ]);

        // Captcha verification
        if ($request->captcha !== Session::get('staff_captcha_answer')) {
            return back()->withInput()->with('error', 'Invalid security verification code. Please solve the math problem again.');
        }

        // Retrieve staff
        $loginField = $request->login_field;
        $staff = Staff::where('email', $loginField)
            ->orWhere('username', $loginField)
            ->orWhere('staff_id', $loginField)
            ->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            $this->logStaffAction('Failed Login', "Attempted login with credentials '{$loginField}'");
            return back()->withInput()->with('error', 'Invalid credentials. Please verify your Staff ID, email, or password.');
        }

        if ($staff->status !== 'Active') {
            return back()->with('error', 'Your staff account is currently suspended or inactive.');
        }

        // 2FA Verification Check
        if ($staff->two_factor_secret && $staff->two_factor_confirmed_at) {
            Session::put('staff_2fa_temp_id', $staff->id);
            Session::put('staff_remember', $request->filled('remember'));
            return redirect()->route('staff.login.2fa');
        }

        // Perform login
        Auth::guard('staff')->login($staff, $request->filled('remember'));

        $this->logStaffAction('Successful Login', "Logged in as {$staff->name}", $staff->id);

        if ($staff->force_password_change) {
            return redirect()->route('staff.password.change');
        }

        return redirect()->route('staff.dashboard');
    }

    public function show2fa()
    {
        if (!Session::has('staff_2fa_temp_id')) {
            return redirect()->route('staff.login');
        }
        return view('staff.login_2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        if (!Session::has('staff_2fa_temp_id')) {
            return redirect()->route('staff.login');
        }

        $staffId = Session::get('staff_2fa_temp_id');
        $staff = Staff::findOrFail($staffId);

        if (TotpHelper::verifyTotp($staff->two_factor_secret, $request->code)) {
            Auth::guard('staff')->login($staff, Session::get('staff_remember', false));
            
            Session::forget(['staff_2fa_temp_id', 'staff_remember']);
            
            $this->logStaffAction('Successful 2FA Login', "Authenticated via Google 2FA", $staff->id);

            if ($staff->force_password_change) {
                return redirect()->route('staff.password.change');
            }

            return redirect()->route('staff.dashboard');
        }

        $this->logStaffAction('Failed 2FA Login', "Incorrect TOTP code input attempt", $staff->id);

        return back()->with('error', 'Invalid Google Authenticator code. Please try again.');
    }

    public function showPasswordChange()
    {
        $staff = Auth::guard('staff')->user();
        if (!$staff) return redirect()->route('staff.login');
        
        return view('staff.password_change');
    }

    public function changePassword(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        if (!$staff) return redirect()->route('staff.login');

        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $staff->update([
            'password' => Hash::make($request->password),
            'force_password_change' => false
        ]);

        $this->logStaffAction('Password Reset', "Forced password change completed.", $staff->id);

        return redirect()->route('staff.dashboard')->with('success', 'Password updated successfully. Welcome to your workspace.');
    }

    public function dashboard()
    {
        $staff = Auth::guard('staff')->user();
        
        // Attendance Today
        $today = date('Y-m-d');
        $attendance = StaffAttendance::where('staff_id', $staff->id)->where('date', $today)->first();

        // Tasks / Projects
        // Display projects associated with their department or generic active tasks
        $projects = Project::latest()->take(4)->get();

        // Leave Balance (e.g. 24 annual days standard minus approved leaves)
        $approvedLeaveDays = StaffLeave::where('staff_id', $staff->id)->where('status', 'Approved')->sum('days');
        $leaveBalance = max(0, 24 - $approvedLeaveDays);

        // Payroll stats
        $lastPayroll = StaffPayroll::where('staff_id', $staff->id)->orderBy('month', 'desc')->first();

        // Performance average
        $avgScore = StaffPerformanceReview::where('staff_id', $staff->id)->avg('productivity_score') ?? 90;

        // Training Courses count
        $academyProgress = Course::count() > 0 ? 35 : 0; // Simulated progress indicator

        // Dynamic 2FA QR code settings if not verified
        $totpSecret = $staff->two_factor_secret;
        if (!$totpSecret) {
            $totpSecret = TotpHelper::generateSecret();
        }
        $qrCodeUrl = TotpHelper::getQrCodeUrl($staff->email, $totpSecret, 'Diwebs Staff');

        return view('staff.dashboard', compact(
            'staff', 'attendance', 'projects', 'leaveBalance', 'lastPayroll', 'avgScore', 'academyProgress', 'totpSecret', 'qrCodeUrl'
        ));
    }

    public function enable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'secret' => 'required|string|size:16'
        ]);

        $staff = Auth::guard('staff')->user();

        if (TotpHelper::verifyTotp($request->secret, $request->code)) {
            $staff->update([
                'two_factor_secret' => $request->secret,
                'two_factor_confirmed_at' => now()
            ]);

            $this->logStaffAction('2FA Enabled', "Google Authenticator successfully configured.", $staff->id);

            return back()->with('success', 'Google Authenticator 2FA configured successfully.');
        }

        return back()->with('error', 'Invalid verification code. Please confirm time synchronization on your device.');
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $staff = Auth::guard('staff')->user();

        if (TotpHelper::verifyTotp($staff->two_factor_secret, $request->code)) {
            $staff->update([
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => null
            ]);

            $this->logStaffAction('2FA Disabled', "Google Authenticator disabled.", $staff->id);

            return back()->with('success', '2FA has been disabled.');
        }

        return back()->with('error', 'Invalid verification code.');
    }

    public function clockInOut()
    {
        $staff = Auth::guard('staff')->user();
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $record = StaffAttendance::where('staff_id', $staff->id)->where('date', $today)->first();

        if (!$record) {
            // Clock In
            $lateMinutes = 0;
            $status = 'Present';

            // Say office shift starts at 09:00 AM
            $limit = strtotime('09:00:00');
            $clockInTime = time();

            if ($clockInTime > $limit) {
                $lateMinutes = round(($clockInTime - $limit) / 60);
                $status = 'Late';
            }

            StaffAttendance::create([
                'staff_id' => $staff->id,
                'date' => $today,
                'clock_in' => now(),
                'status' => $status,
                'late_minutes' => $lateMinutes,
                'ip_address' => request()->ip(),
                'device_info' => request()->userAgent()
            ]);

            $this->logStaffAction('Clocked In', "Registered Clock-In at {$now} - Status: {$status}", $staff->id);

            return back()->with('success', "Clock-in successfully recorded at " . date('H:i A') . ($status === 'Late' ? " (Late by {$lateMinutes} min)" : ''));
        }

        if (!$record->clock_out) {
            // Clock Out
            $record->update([
                'clock_out' => now()
            ]);

            $this->logStaffAction('Clocked Out', "Registered Clock-Out at {$now}", $staff->id);

            return back()->with('success', "Clock-out successfully recorded at " . date('H:i A'));
        }

        return back()->with('error', 'You have already completed your attendance tracking shift for today.');
    }

    public function projects()
    {
        $staff = Auth::guard('staff')->user();
        $projects = Project::latest()->get(); // Display current corporate pipelines
        return view('staff.projects', compact('staff', 'projects'));
    }

    public function attendance()
    {
        $staff = Auth::guard('staff')->user();
        $attendanceLogs = StaffAttendance::where('staff_id', $staff->id)->orderBy('date', 'desc')->paginate(15);
        return view('staff.attendance', compact('staff', 'attendanceLogs'));
    }

    public function payroll()
    {
        $staff = Auth::guard('staff')->user();
        $payrolls = StaffPayroll::where('staff_id', $staff->id)->orderBy('month', 'desc')->get();
        return view('staff.payroll', compact('staff', 'payrolls'));
    }

    public function downloadPayslip($id)
    {
        $staff = Auth::guard('staff')->user();
        $payroll = StaffPayroll::where('staff_id', $staff->id)->where('id', $id)->firstOrFail();

        $pdf = Pdf::loadView('staff.pdf_payslip', compact('staff', 'payroll'));
        return $pdf->download("Payslip_{$staff->staff_id}_{$payroll->month}.pdf");
    }

    public function leaves()
    {
        $staff = Auth::guard('staff')->user();
        $leaves = StaffLeave::where('staff_id', $staff->id)->latest()->get();
        
        $approvedLeaveDays = StaffLeave::where('staff_id', $staff->id)->where('status', 'Approved')->sum('days');
        $leaveBalance = max(0, 24 - $approvedLeaveDays);

        return view('staff.leaves', compact('staff', 'leaves', 'leaveBalance'));
    }

    public function applyLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string'
        ]);

        $staff = Auth::guard('staff')->user();

        // Calculate days
        $start = new \DateTime($request->start_date);
        $end = new \DateTime($request->end_date);
        $days = $start->diff($end)->days + 1;

        // Check leave balance
        $approvedLeaveDays = StaffLeave::where('staff_id', $staff->id)->where('status', 'Approved')->sum('days');
        $leaveBalance = max(0, 24 - $approvedLeaveDays);

        if ($days > $leaveBalance) {
            return back()->withInput()->with('error', "Insufficient leave days balance. Your remaining balance is {$leaveBalance} days, but you requested {$days} days.");
        }

        StaffLeave::create([
            'staff_id' => $staff->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'reason' => $request->reason,
            'status' => 'Pending'
        ]);

        $this->logStaffAction('Applied Leave', "Submitted leave application for {$request->leave_type} (Days: {$days})", $staff->id);

        return back()->with('success', "Leave request submitted successfully. Waiting for admin approval.");
    }

    public function performance()
    {
        $staff = Auth::guard('staff')->user();
        $reviews = StaffPerformanceReview::where('staff_id', $staff->id)->latest()->get();
        return view('staff.performance', compact('staff', 'reviews'));
    }

    public function academy()
    {
        $staff = Auth::guard('staff')->user();
        $courses = Course::latest()->take(3)->get();
        return view('staff.academy', compact('staff', 'courses'));
    }

    public function messages()
    {
        $staff = Auth::guard('staff')->user();
        // Dynamic messaging simulated via notifications list or messages
        $announcements = [
            [
                'title' => 'Quarterly Strategic Alignment Meeting',
                'date' => '2026-06-20',
                'sender' => 'C-Suite Board',
                'content' => 'Please note that our alignment check for Q3 will take place on Monday 29th June, 2026. Attendance is mandatory for all Lagos and remote teams.'
            ],
            [
                'title' => 'Security Architecture Updates',
                'date' => '2026-06-15',
                'sender' => 'SecOps CTO',
                'content' => 'Two-Factor Authentication is now fully active across all staff portals. Please ensure you configure Google Authenticator in your profile settings.'
            ]
        ];
        return view('staff.messages', compact('staff', 'announcements'));
    }

    public function logout()
    {
        $staff = Auth::guard('staff')->user();
        if ($staff) {
            $this->logStaffAction('Logged Out', "Logged out from staff session", $staff->id);
        }
        
        Auth::guard('staff')->logout();
        Session::forget(['staff_2fa_temp_id', 'staff_remember']);

        return redirect()->route('staff.login')->with('success', 'Logged out successfully.');
    }
}
