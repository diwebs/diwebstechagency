@extends('layouts.admin')

@section('title', 'Staff Attendance Logs - Admin Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Staff Attendance Logs</h1>
            <p class="text-xs text-brand-gray mt-1">Review clock-in/out stamps, remote work sessions, and calculate late minutes anomalies.</p>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
        <form action="{{ route('admin.staff.attendance') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date Filter -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Target Date</label>
                <input type="date" 
                       name="date" 
                       value="{{ request('date', date('Y-m-d')) }}"
                       class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
            </div>

            <!-- Staff member filter -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Staff Member</label>
                <select name="staff_id" 
                        class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    <option value="">All Staff</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}" {{ request('staff_id') == $member->id ? 'selected' : '' }}>{{ $member->name }} ({{ $member->staff_id }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Department filter -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Department</label>
                <select name="department_id" 
                        class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-brand-teal/20 border border-brand-teal/30 hover:bg-brand-teal/30 px-4 py-2 text-xs font-bold text-brand-cyan transition-all select-none">
                    🔍 Search
                </button>
                <a href="{{ route('admin.staff.attendance') }}" class="rounded-lg border border-white/5 px-4 py-2 text-xs font-bold text-brand-gray hover:bg-white/5 transition-all text-center select-none">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Staff details</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Date</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Clock In</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Clock Out</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Status</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Late Min</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Device &amp; IP Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($attendanceLogs as $log)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full border border-brand-teal/30 bg-brand-teal/10 flex items-center justify-center text-xs font-extrabold text-brand-cyan overflow-hidden flex-shrink-0">
                                        @if($log->staff->profile_picture)
                                            <img src="/storage/{{ $log->staff->profile_picture }}" alt="Profile" class="h-full w-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($log->staff->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-bold block text-sm">{{ $log->staff->name }}</span>
                                        <span class="text-[9px] text-brand-cyan font-mono block">{{ $log->staff->staff_id }} | {{ $log->staff->department->name ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-[10px] text-brand-white">
                                {{ $log->date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 font-mono text-[10px] text-emerald-400">
                                {{ $log->clock_in ? $log->clock_in->format('H:i:s A') : '--:--:--' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-[10px] text-rose-400">
                                {{ $log->clock_out ? $log->clock_out->format('H:i:s A') : '--:--:--' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $log->status === 'Present' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : ($log->status === 'Late' ? 'bg-amber-500/10 border-amber-500/25 text-amber-400' : 'bg-rose-500/10 border-rose-500/25 text-rose-400') }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-[10px] {{ $log->late_minutes > 0 ? 'text-amber-400 font-bold' : 'text-brand-gray' }}">
                                {{ $log->late_minutes ? $log->late_minutes . ' min' : '0 min' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[9px] text-brand-gray font-mono block">IP: {{ $log->ip_address }}</span>
                                <span class="text-[9px] text-brand-gray truncate max-w-[200px] block" title="{{ $log->device_info }}">{{ $log->device_info }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-brand-gray">No attendance records found for this date.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendanceLogs->hasPages())
            <div class="px-6 py-4 bg-brand-dark-secondary/20 border-t border-brand-teal/10">
                {{ $attendanceLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
