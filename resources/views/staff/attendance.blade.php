@extends('layouts.staff')

@section('title', 'My Attendance Logs - Staff Portal')

@section('staff_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">My Attendance History</h1>
            <p class="text-xs text-brand-gray mt-1">Review clock-in timestamps, clock-out logs, shift status, and check late minutes.</p>
        </div>
    </div>

    <!-- Directory table -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Date</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Clock In</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Clock Out</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Status</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Late Arrival</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Access Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($attendanceLogs as $log)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4 font-mono font-bold">
                                {{ $log->date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 font-mono text-emerald-400">
                                {{ $log->clock_in ? $log->clock_in->format('h:i:s A') : '--' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-rose-400">
                                {{ $log->clock_out ? $log->clock_out->format('h:i:s A') : '--' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $log->status === 'Present' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : ($log->status === 'Late' ? 'bg-amber-500/10 border-amber-500/25 text-amber-400' : 'bg-rose-500/10 border-rose-500/25 text-rose-400') }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono {{ $log->late_minutes > 0 ? 'text-amber-400' : 'text-brand-gray' }}">
                                {{ $log->late_minutes ? $log->late_minutes . ' min' : '0 min' }}
                            </td>
                            <td class="px-6 py-4 text-brand-gray font-mono text-[10px]">
                                IP: {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-brand-gray">No attendance history records logged in your workspace yet.</td>
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
