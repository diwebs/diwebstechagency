@extends('layouts.admin')

@section('title', 'HR & Staff Overview - Admin Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Staff &amp; HR Overview</h1>
            <p class="text-xs text-brand-gray mt-1">Manage agency staff members, departments, attendance, payroll, leaves, and roles permissions.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.staff.create') }}" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all select-none">
                ➕ Register Staff Member
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Staff -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-brand-gray text-[10px] font-extrabold uppercase tracking-wider">Total Staff</span>
                <span class="text-brand-cyan text-lg">👥</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-brand-white">{{ $totalStaff }}</h3>
                <p class="text-[10px] text-brand-cyan mt-1 font-mono">Diwebs Ecosystem</p>
            </div>
        </div>

        <!-- Card 2: Active / Suspended -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-brand-gray text-[10px] font-extrabold uppercase tracking-wider">Active Staff</span>
                <span class="text-emerald-500 text-lg">●</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-brand-white">{{ $activeStaff }}</h3>
                <p class="text-[10px] text-brand-gray mt-1 font-mono">{{ $suspendedStaff }} Suspended / Resigned</p>
            </div>
        </div>

        <!-- Card 3: Attendance Rate -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-brand-gray text-[10px] font-extrabold uppercase tracking-wider">Attendance Rate</span>
                <span class="text-brand-cyan text-lg">⚡</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-brand-white">{{ $attendanceRate }}%</h3>
                <p class="text-[10px] text-brand-cyan mt-1 font-mono">Today's Avg Clock-in</p>
            </div>
        </div>

        <!-- Card 4: Leaves Pending -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-brand-gray text-[10px] font-extrabold uppercase tracking-wider">Pending Leaves</span>
                <span class="text-amber-500 text-lg">✉️</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-brand-white">{{ $pendingLeaves }}</h3>
                <p class="text-[10px] text-brand-gray mt-1 font-mono">Requires Admin Action</p>
            </div>
        </div>

        <!-- Card 5: Monthly Payroll Outlay -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-brand-gray text-[10px] font-extrabold uppercase tracking-wider">Payroll Outlay</span>
                <span class="text-emerald-400 text-lg">💵</span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-extrabold text-brand-white">₦{{ number_format($monthlyPayroll, 2) }}</h3>
                <p class="text-[10px] text-brand-cyan mt-1 font-mono">Current Month Outlay</p>
            </div>
        </div>
    </div>

    <!-- Analytics & Activity Rows -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Charts / Tables -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Analytics Cards / Visualizations -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 space-y-6">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Monthly Payroll Outlay Trend</h3>
                <div class="h-48 flex items-end justify-between gap-2.5 pt-4">
                    @foreach($payrollHistory as $ph)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-brand-dark-secondary rounded-lg overflow-hidden h-32 flex flex-col justify-end">
                                <div class="bg-gradient-to-t from-brand-teal to-brand-cyan rounded-t-md transition-all hover:opacity-85" style="height: {{ min(100, max(15, ($ph->total / 1000000) * 100)) }}%"></div>
                            </div>
                            <span class="text-[9px] font-mono text-brand-gray font-bold uppercase">{{ $ph->month }}</span>
                            <span class="text-[9px] font-mono text-brand-cyan font-bold">₦{{ number_format($ph->total / 1000, 1) }}k</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Staff Activities Audit -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 space-y-4">
                <div class="flex justify-between items-center border-b border-brand-teal/10 pb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">HR Security Activity logs</h3>
                    <a href="{{ route('admin.staff.activity-logs') }}" class="text-[10px] text-brand-cyan hover:underline">View All Logs →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-brand-gray border-b border-brand-teal/10">
                                <th class="py-2.5 font-bold uppercase text-[10px]">Staff</th>
                                <th class="py-2.5 font-bold uppercase text-[10px]">Action</th>
                                <th class="py-2.5 font-bold uppercase text-[10px]">IP / Device</th>
                                <th class="py-2.5 font-bold uppercase text-[10px] text-right">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                            @forelse($activities as $act)
                                <tr>
                                    <td class="py-3">
                                        <span class="font-bold block">{{ $act->staff->name ?? 'System' }}</span>
                                        <span class="text-[9px] text-brand-gray font-mono block">{{ $act->staff->staff_id ?? '' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="inline-flex rounded-full bg-brand-teal/10 px-2 py-0.5 text-[9px] font-bold text-brand-cyan border border-brand-teal/20">{{ $act->action }}</span>
                                        <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">{{ $act->description }}</p>
                                    </td>
                                    <td class="py-3 font-mono text-[9px] text-brand-gray">
                                        {{ $act->ip_address }}
                                    </td>
                                    <td class="py-3 text-right text-brand-gray font-mono text-[10px]">
                                        {{ $act->created_at->format('M d, H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-brand-gray">No logged actions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Col: Departments Distribution -->
        <div class="space-y-8">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 space-y-4">
                <div class="flex justify-between items-center border-b border-brand-teal/10 pb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Departments Overview</h3>
                    <a href="{{ route('admin.staff.departments') }}" class="text-[10px] text-brand-cyan hover:underline">Manage Departments</a>
                </div>
                
                <div class="space-y-4 pt-2">
                    @foreach($deptDistribution as $dept)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-brand-white">{{ $dept->name }}</span>
                                <span class="font-mono text-brand-cyan font-bold">{{ $dept->staff_members_count }} Members</span>
                            </div>
                            <div class="w-full bg-brand-dark-secondary rounded-full h-1.5 overflow-hidden">
                                <div class="bg-brand-cyan h-full rounded-full" style="width: {{ $totalStaff > 0 ? ($dept->staff_members_count / $totalStaff) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Efficiency & KPIs score widget -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Agency Performance Evaluation</h3>
                <div class="flex items-center gap-4 py-2">
                    <div class="relative h-16 w-16 rounded-full border-4 border-brand-cyan flex items-center justify-center font-bold text-brand-white text-lg font-mono">
                        {{ $perfScore }}%
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-brand-white">KPI Evaluation Index</h4>
                        <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">Aggregated productivity logs, task completeness indexes, client success feedback and reviews.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
