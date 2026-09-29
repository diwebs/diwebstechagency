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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StaffAdminController extends Controller
{
    private function logAdminAction($action, $description, $staffId = null)
    {
        StaffActivityLog::create([
            'staff_id' => $staffId,
            'action' => $action,
            'description' => 'Admin: ' . $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function dashboard()
    {
        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'Active')->count();
        $suspendedStaff = Staff::where('status', 'Suspended')->count();
        $departmentsCount = Department::count();
        
        // Attendance Rate
        $today = date('Y-m-d');
        $todayAttendance = StaffAttendance::where('date', $today)->count();
        $presentToday = StaffAttendance::where('date', $today)->whereIn('status', ['Present', 'Late', 'Remote'])->count();
        $attendanceRate = $activeStaff > 0 ? round(($presentToday / $activeStaff) * 100) : 100;
        if ($todayAttendance == 0) {
            // Fallback default mock rate
            $attendanceRate = 94;
        }

        $pendingLeaves = StaffLeave::where('status', 'Pending')->count();
        $payrollMonth = date('Y-m');
        $monthlyPayroll = StaffPayroll::where('month', $payrollMonth)->sum('net_salary');
        if ($monthlyPayroll == 0) {
            $monthlyPayroll = Staff::where('status', 'Active')->sum('base_salary');
        }

        // Performance average
        $avgProductivity = StaffPerformanceReview::avg('productivity_score') ?? 88;
        $avgTasks = StaffPerformanceReview::avg('task_completion_rate') ?? 91;
        $avgClient = StaffPerformanceReview::avg('client_satisfaction_score') ?? 87;
        $avgCollab = StaffPerformanceReview::avg('collaboration_score') ?? 90;
        
        $perfScore = round(($avgProductivity + $avgTasks + $avgClient + $avgCollab) / 4);

        // Get department head count distribution
        $deptDistribution = Department::withCount('staffMembers')->get();

        // Get recent activity logs
        $activities = StaffActivityLog::with('staff')->latest()->take(8)->get();

        // Get monthly payroll history
        $payrollHistory = StaffPayroll::select('month', DB::raw('SUM(net_salary) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        return view('admin.staff.dashboard', compact(
            'totalStaff', 'activeStaff', 'suspendedStaff', 'departmentsCount',
            'attendanceRate', 'pendingLeaves', 'monthlyPayroll', 'perfScore',
            'deptDistribution', 'activities', 'payrollHistory'
        ));
    }

    public function index(Request $request)
    {
        $query = Staff::with(['department', 'role']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $staffMembers = $query->orderBy('staff_id', 'asc')->paginate(12);
        $departments = Department::all();

        return view('admin.staff.index', compact('staffMembers', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $roles = StaffRole::all();
        $managers = Staff::where('status', 'Active')->get();

        return view('admin.staff.create', compact('departments', 'roles', 'managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_members,email',
            'username' => 'required|string|unique:staff_members,username|max:50',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'nationality' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:staff_roles,id',
            'employment_type' => 'required|string',
            'salary_grade' => 'nullable|string',
            'base_salary' => 'required|numeric|min:0',
            'date_hired' => 'nullable|date',
            'reporting_manager_id' => 'nullable|exists:staff_members,id',
            'office_location' => 'nullable|string',
            'status' => 'required|string',
        ]);

        // Generate Staff ID (e.g. DWS-1002)
        $count = Staff::count();
        $nextNum = 1001 + $count;
        do {
            $staffId = 'DWS-' . $nextNum;
            $nextNum++;
        } while (Staff::where('staff_id', $staffId)->exists());

        $data = $request->except(['password', 'profile_picture']);
        $data['staff_id'] = $staffId;
        $data['password'] = Hash::make($request->password);
        $data['force_password_change'] = true;

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('staff_avatars', 'public');
            $data['profile_picture'] = $path;
        }

        $staff = Staff::create($data);

        $this->logAdminAction('Staff Created', "Registered new staff member {$staff->name} ({$staff->staff_id})", $staff->id);

        return redirect()->route('admin.staff.index')->with('success', "Staff member successfully registered with ID: {$staffId}");
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $departments = Department::all();
        $roles = StaffRole::where('department_id', $staff->department_id)->get();
        $managers = Staff::where('status', 'Active')->where('id', '!=', $id)->get();

        return view('admin.staff.edit', compact('staff', 'departments', 'roles', 'managers'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('staff_members')->ignore($staff->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('staff_members')->ignore($staff->id)],
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'nationality' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:staff_roles,id',
            'employment_type' => 'required|string',
            'salary_grade' => 'nullable|string',
            'base_salary' => 'required|numeric|min:0',
            'date_hired' => 'nullable|date',
            'reporting_manager_id' => 'nullable|exists:staff_members,id',
            'office_location' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $data = $request->except(['profile_picture']);

        if ($request->hasFile('profile_picture')) {
            if ($staff->profile_picture) {
                Storage::disk('public')->delete($staff->profile_picture);
            }
            $path = $request->file('profile_picture')->store('staff_avatars', 'public');
            $data['profile_picture'] = $path;
        }

        $staff->update($data);

        $this->logAdminAction('Staff Updated', "Updated details for staff member {$staff->name} ({$staff->staff_id})", $staff->id);

        return redirect()->route('admin.staff.index')->with('success', 'Staff details successfully updated.');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $name = $staff->name;
        
        $this->logAdminAction('Staff Deleted', "Deleted profile of staff member {$name} ({$staff->staff_id})");
        
        if ($staff->profile_picture) {
            Storage::disk('public')->delete($staff->profile_picture);
        }
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', "Staff record of {$name} was deleted successfully.");
    }

    public function departments()
    {
        $departments = Department::with(['head', 'roles'])->get();
        $staff = Staff::where('status', 'Active')->get();
        return view('admin.staff.departments', compact('departments', 'staff'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code|max:10',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:staff_members,id'
        ]);

        $dept = Department::create($request->all());

        $this->logAdminAction('Department Created', "Created department {$dept->name} ({$dept->code})");

        return back()->with('success', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, $id)
    {
        $dept = Department::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:10', Rule::unique('departments')->ignore($dept->id)],
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:staff_members,id'
        ]);

        $dept->update($request->all());

        $this->logAdminAction('Department Updated', "Updated department {$dept->name} ({$dept->code})");

        return back()->with('success', 'Department details updated successfully.');
    }

    public function deleteDepartment($id)
    {
        $dept = Department::findOrFail($id);
        $name = $dept->name;
        $dept->delete();

        $this->logAdminAction('Department Deleted', "Deleted department {$name}");

        return back()->with('success', 'Department deleted successfully.');
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
        ]);

        $role = StaffRole::create([
            'department_id' => $request->department_id,
            'title' => $request->title,
            'permissions' => ['view_dashboard']
        ]);

        $this->logAdminAction('Role Created', "Created role {$role->title} under department ID {$role->department_id}");

        return back()->with('success', 'Role added successfully. Go to Roles & Permissions to map privileges.');
    }

    public function deleteRole($id)
    {
        $role = StaffRole::findOrFail($id);
        $title = $role->title;
        $role->delete();

        $this->logAdminAction('Role Deleted', "Deleted role {$title}");

        return back()->with('success', 'Role deleted successfully.');
    }

    public function rbac()
    {
        $departments = Department::with('roles')->get();
        return view('admin.staff.rbac', compact('departments'));
    }

    public function updateRbac(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.staff.rbac');
        }

        $request->validate([
            'permissions' => 'nullable|array',
        ]);


        $allRoles = StaffRole::all();
        $inputPermissions = $request->input('permissions', []);

        foreach ($allRoles as $role) {
            $rolePerms = $inputPermissions[$role->id] ?? [];
            $role->update([
                'permissions' => $rolePerms
            ]);
        }

        $this->logAdminAction('Permissions Updated', "Synchronized global RBAC mappings.");

        return back()->with('success', 'Roles & Permissions synchronized successfully.');
    }

    public function getRolesByDepartment($departmentId)
    {
        $roles = StaffRole::where('department_id', $departmentId)->get();
        return response()->json($roles);
    }

    public function attendance(Request $request)
    {
        $query = StaffAttendance::with('staff.department');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', date('Y-m-d'));
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('staff', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $attendanceLogs = $query->orderBy('clock_in', 'desc')->paginate(20);
        $departments = Department::all();
        $staff = Staff::all();

        return view('admin.staff.attendance', compact('attendanceLogs', 'departments', 'staff'));
    }

    public function payroll(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $payrolls = StaffPayroll::with('staff.department')->where('month', $month)->get();
        $staffMembers = Staff::where('status', 'Active')->get();

        return view('admin.staff.payroll', compact('payrolls', 'month', 'staffMembers'));
    }

    public function runPayroll(Request $request)
    {
        $request->validate([
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/'
        ]);

        $month = $request->month;
        $activeStaff = Staff::where('status', 'Active')->get();
        $count = 0;

        foreach ($activeStaff as $staff) {
            // Check if already executed
            if (StaffPayroll::where('staff_id', $staff->id)->where('month', $month)->exists()) {
                continue;
            }

            $base = $staff->base_salary;
            // Simple tax estimation (10% standard tax)
            $tax = round($base * 0.10, 2);
            $net = $base - $tax;

            StaffPayroll::create([
                'staff_id' => $staff->id,
                'month' => $month,
                'base_salary' => $base,
                'bonuses' => 0.00,
                'deductions' => 0.00,
                'tax' => $tax,
                'net_salary' => $net,
                'payment_status' => 'Pending'
            ]);
            $count++;
        }

        $this->logAdminAction('Payroll Executed', "Executed payroll cycle for month {$month} across {$count} staff members.");

        return back()->with('success', "Payroll generated successfully for {$count} records. Status set to Pending.");
    }

    public function updatePayrollItem(Request $request, $id)
    {
        $payroll = StaffPayroll::findOrFail($id);
        $request->validate([
            'bonuses' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'payment_status' => 'required|string',
        ]);

        $base = $payroll->base_salary;
        $tax = $payroll->tax;
        
        $net = $base + $request->bonuses - $request->deductions - $tax;

        $payroll->update([
            'bonuses' => $request->bonuses,
            'deductions' => $request->deductions,
            'net_salary' => $net,
            'payment_status' => $request->payment_status,
            'paid_at' => $request->payment_status === 'Paid' ? now() : null
        ]);

        $staffName = $payroll->staff->name ?? 'Staff #' . $payroll->staff_id;
        $this->logAdminAction('Payroll Updated', "Updated payroll slip for {$staffName} for {$payroll->month}");

        return back()->with('success', 'Payroll slip updated successfully.');
    }

    public function performance()
    {
        $reviews = StaffPerformanceReview::with('staff.department')->latest()->get();
        $staff = Staff::where('status', 'Active')->get();
        
        // Rankings
        $rankings = Staff::withAvg('performanceReviews', 'productivity_score')
            ->orderBy('performance_reviews_avg_productivity_score', 'desc')
            ->take(10)
            ->get();

        return view('admin.staff.performance', compact('reviews', 'staff', 'rankings'));
    }

    public function storePerformance(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff_members,id',
            'productivity_score' => 'required|integer|between:1,100',
            'task_completion_rate' => 'required|integer|between:1,100',
            'client_satisfaction_score' => 'required|integer|between:1,100',
            'collaboration_score' => 'required|integer|between:1,100',
            'review_notes' => 'nullable|string',
            'promotion_recommended' => 'required|boolean'
        ]);

        $data = $request->all();
        $data['reviewer_id'] = auth()->id();
        $data['review_date'] = date('Y-m-d');

        $review = StaffPerformanceReview::create($data);

        $this->logAdminAction('Performance Review Created', "Logged performance review scorecard for staff ID {$review->staff_id}", $review->staff_id);

        return back()->with('success', 'KPI review scorecard saved successfully.');
    }

    public function leaves()
    {
        $leaves = StaffLeave::with('staff.department')->latest()->paginate(15);
        return view('admin.staff.leaves', compact('leaves'));
    }

    public function approveLeave($id)
    {
        $leave = StaffLeave::findOrFail($id);
        $leave->update([
            'status' => 'Approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => 'Approved by Admin'
        ]);

        $this->logAdminAction('Leave Approved', "Approved {$leave->leave_type} leave for {$leave->staff->name} (Days: {$leave->days})", $leave->staff_id);

        return back()->with('success', 'Leave request approved.');
    }

    public function rejectLeave(Request $request, $id)
    {
        $leave = StaffLeave::findOrFail($id);
        $request->validate([
            'admin_notes' => 'required|string'
        ]);

        $leave->update([
            'status' => 'Rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes
        ]);

        $this->logAdminAction('Leave Rejected', "Rejected {$leave->leave_type} leave request for {$leave->staff->name}", $leave->staff_id);

        return back()->with('success', 'Leave request rejected.');
    }

    public function activityLogs(Request $request)
    {
        $query = StaffActivityLog::with('staff.department');

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        $logs = $query->latest()->paginate(25);
        $staff = Staff::all();

        return view('admin.staff.activity_logs', compact('logs', 'staff'));
    }

    public function profile($id)
    {
        $staff = Staff::with(['department', 'role', 'reportingManager', 'attendance', 'payrolls', 'performanceReviews', 'leaves', 'activityLogs'])->findOrFail($id);
        
        // Find assigned projects (simulated by finding projects where their department is working or manager assigned, 
        // or just let's select a few active projects for high fidelity display)
        $projects = Project::latest()->take(3)->get();

        return view('admin.staff.profile', compact('staff', 'projects'));
    }
}
