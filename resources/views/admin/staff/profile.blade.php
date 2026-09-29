@extends('layouts.admin')

@section('title', $staff->name . ' - Staff Profile Details')

@section('admin_content')
<div class="space-y-8" x-data="{ activeTab: 'overview' }">
    <!-- Header Back link -->
    <div>
        <a href="{{ route('admin.staff.index') }}" class="text-xs text-brand-cyan hover:underline">← Back to staff directory</a>
    </div>

    <!-- Staff Header Card -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4 flex-col md:flex-row text-center md:text-left">
            <div class="h-20 w-20 rounded-full border-2 border-brand-cyan bg-brand-teal/10 flex items-center justify-center text-2xl font-extrabold text-brand-cyan overflow-hidden">
                @if($staff->profile_picture)
                    <img src="/storage/{{ $staff->profile_picture }}" alt="Profile" class="h-full w-full object-cover">
                @else
                    <span>{{ strtoupper(substr($staff->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <h1 class="text-xl font-bold text-brand-white">{{ $staff->name }}</h1>
                <span class="text-xs text-brand-cyan font-mono font-bold">{{ $staff->staff_id }}</span>
                <span class="text-xs text-brand-gray block">{{ $staff->role->title ?? 'Unassigned' }} | {{ $staff->department->name ?? 'Unassigned' }}</span>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="inline-flex rounded-full bg-emerald-500/10 border border-emerald-500/25 px-3 py-1 text-[10px] font-bold text-emerald-400">
                Status: {{ $staff->status }}
            </span>
            <span class="inline-flex rounded-full bg-brand-teal/10 border border-brand-teal/25 px-3 py-1 text-[10px] font-bold text-brand-cyan">
                {{ $staff->employment_type }}
            </span>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-brand-teal/10 flex gap-4 overflow-x-auto select-none">
        <button type="button" @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-brand-cyan border-b-2 border-brand-cyan font-bold' : 'text-brand-gray hover:text-brand-white'" class="pb-2.5 text-xs font-semibold px-2 cursor-pointer transition-all shrink-0">Overview</button>
        <button type="button" @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'text-brand-cyan border-b-2 border-brand-cyan font-bold' : 'text-brand-gray hover:text-brand-white'" class="pb-2.5 text-xs font-semibold px-2 cursor-pointer transition-all shrink-0">Attendance History</button>
        <button type="button" @click="activeTab = 'salary'" :class="activeTab === 'salary' ? 'text-brand-cyan border-b-2 border-brand-cyan font-bold' : 'text-brand-gray hover:text-brand-white'" class="pb-2.5 text-xs font-semibold px-2 cursor-pointer transition-all shrink-0">Salary History</button>
        <button type="button" @click="activeTab = 'leaves'" :class="activeTab === 'leaves' ? 'text-brand-cyan border-b-2 border-brand-cyan font-bold' : 'text-brand-gray hover:text-brand-white'" class="pb-2.5 text-xs font-semibold px-2 cursor-pointer transition-all shrink-0">Leave History</button>
        <button type="button" @click="activeTab = 'kpis'" :class="activeTab === 'kpis' ? 'text-brand-cyan border-b-2 border-brand-cyan font-bold' : 'text-brand-gray hover:text-brand-white'" class="pb-2.5 text-xs font-semibold px-2 cursor-pointer transition-all shrink-0">Performance KPI</button>
        <button type="button" @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'text-brand-cyan border-b-2 border-brand-cyan font-bold' : 'text-brand-gray hover:text-brand-white'" class="pb-2.5 text-xs font-semibold px-2 cursor-pointer transition-all shrink-0">Projects</button>
        <button type="button" @click="activeTab = 'security'" :class="activeTab === 'security' ? 'text-brand-cyan border-b-2 border-brand-cyan font-bold' : 'text-brand-gray hover:text-brand-white'" class="pb-2.5 text-xs font-semibold px-2 cursor-pointer transition-all shrink-0">Security Logs</button>
    </div>

    <!-- Tabs Content -->
    <div>
        
        <!-- Tab 1: Overview -->
        <div x-show="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Personal Details</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Email Address</span> <span class="font-bold text-brand-white">{{ $staff->email }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Phone Number</span> <span class="font-bold text-brand-white">{{ $staff->phone ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Date of Birth</span> <span class="font-bold text-brand-white font-mono">{{ $staff->dob ? $staff->dob->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Gender</span> <span class="font-bold text-brand-white">{{ $staff->gender ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Nationality</span> <span class="font-bold text-brand-white">{{ $staff->nationality ?: 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col gap-1.5 pt-1">
                        <span class="text-brand-gray">Residential Address</span>
                        <p class="font-bold text-brand-white leading-relaxed">{{ $staff->address ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Employment &amp; System Details</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Reporting Manager</span>
                        <span class="font-bold text-brand-white">
                            {{ $staff->reportingManager ? $staff->reportingManager->name : 'No direct manager' }}
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Office Location</span> <span class="font-bold text-brand-white">{{ $staff->office_location ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Date Hired</span> <span class="font-bold text-brand-white font-mono">{{ $staff->date_hired ? $staff->date_hired->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Salary Grade</span> <span class="font-bold text-brand-white font-mono">{{ $staff->salary_grade ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">Base Salary (Monthly)</span> <span class="font-bold text-brand-cyan font-mono">₦{{ number_format($staff->base_salary, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/5 pb-2">
                        <span class="text-brand-gray">System Access Username</span> <span class="font-bold text-brand-white font-mono">{{ $staff->username }}</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="text-brand-gray">Two-Factor Authentication</span>
                        <span class="font-bold {{ $staff->two_factor_confirmed_at ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $staff->two_factor_confirmed_at ? 'Google Authenticator Active' : 'Not configured' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Attendance History -->
        <div x-show="activeTab === 'attendance'" class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Clock-In History</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-brand-gray border-b border-brand-teal/10">
                            <th class="py-2.5 font-bold uppercase text-[9px]">Date</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Clock In</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Clock Out</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Status</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Late Min</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($staff->attendance as $att)
                            <tr>
                                <td class="py-3 font-mono">{{ $att->date->format('Y-m-d') }}</td>
                                <td class="py-3 font-mono text-emerald-400">{{ $att->clock_in ? $att->clock_in->format('H:i:s A') : '--' }}</td>
                                <td class="py-3 font-mono text-rose-400">{{ $att->clock_out ? $att->clock_out->format('H:i:s A') : '--' }}</td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold border {{ $att->status === 'Present' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : 'bg-amber-500/10 border-amber-500/25 text-amber-400' }}">{{ $att->status }}</span>
                                </td>
                                <td class="py-3 font-mono">{{ $att->late_minutes ?: '0' }} min</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-brand-gray">No attendance records logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: Salary History -->
        <div x-show="activeTab === 'salary'" class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Monthly Invoiced Slips</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-brand-gray border-b border-brand-teal/10">
                            <th class="py-2.5 font-bold uppercase text-[9px]">Period</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Base Salary</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Bonuses</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Deductions</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Tax Paid</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Net Payout</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($staff->payrolls as $pr)
                            <tr>
                                <td class="py-3 font-mono font-bold">{{ $pr->month }}</td>
                                <td class="py-3 font-mono">₦{{ number_format($pr->base_salary, 2) }}</td>
                                <td class="py-3 font-mono text-emerald-400">+₦{{ number_format($pr->bonuses, 2) }}</td>
                                <td class="py-3 font-mono text-rose-400">-₦{{ number_format($pr->deductions, 2) }}</td>
                                <td class="py-3 font-mono text-rose-400">-₦{{ number_format($pr->tax, 2) }}</td>
                                <td class="py-3 font-mono font-extrabold text-brand-cyan">₦{{ number_format($pr->net_salary, 2) }}</td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold border {{ $pr->payment_status === 'Paid' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : 'bg-amber-500/10 border-amber-500/25 text-amber-400' }}">{{ $pr->payment_status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-brand-gray">No payroll invoice slips run yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 4: Leave History -->
        <div x-show="activeTab === 'leaves'" class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Allocated Leaves &amp; History</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-brand-gray border-b border-brand-teal/10">
                            <th class="py-2.5 font-bold uppercase text-[9px]">Leave Type</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Requested Duration</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Reason</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Status</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Reviewer Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($staff->leaves as $lv)
                            <tr>
                                <td class="py-3 font-bold">{{ $lv->leave_type }}</td>
                                <td class="py-3 font-mono">
                                    <span class="block font-bold">{{ $lv->days }} Days</span>
                                    <span class="text-[9px] text-brand-gray block">{{ $lv->start_date->format('Y-m-d') }} to {{ $lv->end_date->format('Y-m-d') }}</span>
                                </td>
                                <td class="py-3 text-brand-gray">{{ $lv->reason }}</td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold border {{ $lv->status === 'Approved' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : ($lv->status === 'Pending' ? 'bg-amber-500/10 border-amber-500/25 text-amber-400' : 'bg-rose-500/10 border-rose-500/25 text-rose-400') }}">{{ $lv->status }}</span>
                                </td>
                                <td class="py-3 text-brand-gray text-[10px]">{{ $lv->admin_notes ?: 'No review notes.' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-brand-gray">No leave applications filed yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 5: Performance KPI -->
        <div x-show="activeTab === 'kpis'" class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Evaluation KPI Scorecards</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-brand-gray border-b border-brand-teal/10">
                            <th class="py-2.5 font-bold uppercase text-[9px]">Review Date</th>
                            <th class="py-2.5 font-bold uppercase text-[9px] text-center">Productivity</th>
                            <th class="py-2.5 font-bold uppercase text-[9px] text-center">Tasks Complete</th>
                            <th class="py-2.5 font-bold uppercase text-[9px] text-center">Client Sat</th>
                            <th class="py-2.5 font-bold uppercase text-[9px] text-center">Collab</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Review Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($staff->performanceReviews as $kpi)
                            <tr>
                                <td class="py-3 font-mono">{{ $kpi->review_date->format('Y-m-d') }}</td>
                                <td class="py-3 text-center font-bold text-brand-cyan font-mono">{{ $kpi->productivity_score }}%</td>
                                <td class="py-3 text-center font-bold text-brand-cyan font-mono">{{ $kpi->task_completion_rate }}%</td>
                                <td class="py-3 text-center font-bold text-brand-cyan font-mono">{{ $kpi->client_satisfaction_score }}%</td>
                                <td class="py-3 text-center font-bold text-brand-cyan font-mono">{{ $kpi->collaboration_score }}%</td>
                                <td class="py-3 text-brand-gray max-w-[200px] truncate" title="{{ $kpi->review_notes }}">"{{ $kpi->review_notes }}"</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-brand-gray">No performance reviews recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 6: Projects -->
        <div x-show="activeTab === 'projects'" class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Assigned Projects Pipelines</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-brand-gray border-b border-brand-teal/10">
                            <th class="py-2.5 font-bold uppercase text-[9px]">Project Name</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Budget</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Status</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Agreement Stamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($projects as $proj)
                            <tr>
                                <td class="py-3">
                                    <span class="font-bold block text-sm">{{ $proj->title }}</span>
                                    <span class="text-[9px] text-brand-gray block max-w-sm truncate">{{ $proj->description }}</span>
                                </td>
                                <td class="py-3 font-mono">₦{{ number_format($proj->budget, 2) }}</td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-full bg-brand-teal/10 px-2 py-0.5 text-[9px] font-bold text-brand-cyan border border-brand-teal/20">{{ $proj->status }}</span>
                                </td>
                                <td class="py-3 font-mono text-brand-gray">
                                    {{ $proj->agreement_signed_at ? $proj->agreement_signed_at->format('Y-m-d') : 'No stamp' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-brand-gray">No projects assigned at this time.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 7: Security Logs -->
        <div x-show="activeTab === 'security'" class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">User Access &amp; Audit Logs</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-brand-gray border-b border-brand-teal/10">
                            <th class="py-2.5 font-bold uppercase text-[9px]">Action</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Description</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">IP Location</th>
                            <th class="py-2.5 font-bold uppercase text-[9px]">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($staff->activityLogs as $log)
                            <tr>
                                <td class="py-3">
                                    <span class="inline-flex rounded-full bg-brand-teal/10 px-2.5 py-0.5 text-[9px] font-bold text-brand-cyan border border-brand-teal/20">{{ $log->action }}</span>
                                </td>
                                <td class="py-3 leading-relaxed text-brand-gray">{{ $log->description }}</td>
                                <td class="py-3 font-mono text-brand-gray">{{ $log->ip_address }}</td>
                                <td class="py-3 font-mono text-brand-gray">{{ $log->created_at->format('Y-m-d H:i:s A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-brand-gray">No security activity logs recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
