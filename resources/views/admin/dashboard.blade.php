
@extends('layouts.admin')

@section('title', 'Admin Control Center - Diwebs Tech Operations')

@section('admin_content')
<div>
    <!-- Header -->
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Operations Control Center</h1>
            <p class="text-sm text-brand-gray mt-1">Real-time oversight of all ecosystem modules and security metrics.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs text-brand-gray">System Status: <strong class="text-emerald-400">All Operational</strong></span>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-12">
        <div class="glass-card rounded-2xl p-5 text-center col-span-1">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Users</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1">{{ $stats['total_users'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-5 text-center">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Leads</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1">{{ $stats['total_leads'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-5 text-center">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Courses</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1">{{ $stats['total_courses'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-5 text-center">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Sessions</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1">{{ $stats['total_sessions'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-5 text-center">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Flagged</span>
            <strong class="block text-2xl font-bold text-rose-400 mt-1">{{ $stats['flagged_sessions'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-5 text-center">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Revenue</span>
            <strong class="block text-2xl font-bold text-emerald-400 mt-1">{{ \App\Helpers\PaymentHelper::format($stats['total_revenue'], 0) }}</strong>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Security Alerts -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-rose-400 animate-ping"></span>
                Security Activity Feed
            </h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($recentLogs as $log)
                    <div class="flex items-start gap-3 text-xs py-2 border-b border-brand-teal/10 last:border-0">
                        <span class="mt-0.5 rounded px-1.5 py-0.5 font-bold uppercase text-[9px]
                            @if(str_contains($log->event_type, 'switch') || str_contains($log->event_type, 'exit'))
                                bg-rose-950 text-rose-400 border border-rose-500/20
                            @else
                                bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                            @endif">
                            {{ str_replace('_', ' ', $log->event_type) }}
                        </span>
                        <div class="flex-1">
                            <span class="text-brand-gray">{{ $log->user ? $log->user->name : 'Unknown User' }}</span>
                            <span class="text-brand-gray/60 ml-2 text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <span class="text-brand-gray/50 text-[10px]">{{ $log->ip_address }}</span>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No security events recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- CBT Centers Status -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4">CBT Centers Status</h3>
            <div class="space-y-3">
                @forelse($centers as $center)
                    <div class="flex items-center justify-between rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/40 p-3 text-xs">
                        <div>
                            <h4 class="font-bold text-brand-white">{{ $center->name }}</h4>
                            <span class="text-brand-gray">{{ $center->city }} — Capacity: {{ $center->capacity }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold {{ $center->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ strtoupper($center->status) }}
                            </span>
                            <span class="text-brand-gray">{{ $center->seats_count ?? 0 }} seats</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No CBT centers registered.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Exam Sessions -->
        <div class="glass-card rounded-2xl p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4">Recent Exam Sessions</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Candidate</th>
                            <th class="pb-3">Examination</th>
                            <th class="pb-3">Score</th>
                            <th class="pb-3">Flags</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5">
                        @forelse($recentSessions as $session)
                            <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                                <td class="py-3 text-brand-white font-medium">{{ $session->user->name }}</td>
                                <td class="py-3 text-brand-gray">{{ $session->exam->title }}</td>
                                <td class="py-3 font-mono font-bold {{ $session->score >= $session->exam->passing_score ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $session->score !== null ? $session->score . '%' : 'N/A' }}
                                </td>
                                <td class="py-3 {{ $session->anti_cheat_flags > 0 ? 'text-rose-400' : 'text-brand-gray' }}">
                                    {{ $session->anti_cheat_flags }}
                                </td>
                                <td class="py-3">
                                    <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase
                                        @if($session->status === 'completed') bg-emerald-950 text-emerald-400
                                        @elseif($session->status === 'flagged') bg-rose-950 text-rose-400
                                        @else bg-brand-teal/10 text-brand-cyan
                                        @endif">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-brand-gray">{{ $session->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-brand-gray">No exam sessions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
