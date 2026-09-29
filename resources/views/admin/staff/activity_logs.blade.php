@extends('layouts.admin')

@section('title', 'Staff Activity Logs - Admin Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Staff Activity Logs</h1>
            <p class="text-xs text-brand-gray mt-1">Review operational actions, security login failures, and audit IP access locations.</p>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
        <form action="{{ route('admin.staff.activity-logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Staff member filter -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Staff Member</label>
                <select name="staff_id" 
                        class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                    <option value="">All Staff</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}" {{ request('staff_id') == $member->id ? 'selected' : '' }}>{{ $member->name }} ({{ $member->staff_id }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Action type search -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Action Type</label>
                <input type="text" 
                       name="action" 
                       value="{{ request('action') }}"
                       placeholder="e.g. Clock In, Login..."
                       class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-brand-teal/20 border border-brand-teal/30 hover:bg-brand-teal/30 px-4 py-2 text-xs font-bold text-brand-cyan transition-all select-none">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.staff.activity-logs') }}" class="rounded-lg border border-white/5 px-4 py-2 text-xs font-bold text-brand-gray hover:bg-white/5 transition-all text-center select-none">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs table -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Staff Details</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Action Type</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Description &amp; Context</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">IP / Browser Client</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($logs as $log)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4">
                                @if($log->staff)
                                    <span class="font-bold block text-sm">{{ $log->staff->name }}</span>
                                    <span class="text-[9px] text-brand-cyan font-mono block">{{ $log->staff->staff_id }} | {{ $log->staff->department->name ?? '' }}</span>
                                @else
                                    <span class="font-bold block text-sm text-brand-gray">Anonymous / System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-brand-teal/10 px-2.5 py-0.5 text-[9px] font-bold text-brand-cyan border border-brand-teal/20">{{ $log->action }}</span>
                            </td>
                            <td class="px-6 py-4 max-w-[250px] leading-relaxed">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[9px] text-brand-gray font-mono block">IP: {{ $log->ip_address }}</span>
                                <span class="text-[9px] text-brand-gray block truncate max-w-[200px]" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                            </td>
                            <td class="px-6 py-4 text-right text-brand-gray font-mono text-[10px]">
                                {{ $log->created_at->format('Y-m-d H:i:s A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-brand-gray">No activity logs matching these filters recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 bg-brand-dark-secondary/20 border-t border-brand-teal/10">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
