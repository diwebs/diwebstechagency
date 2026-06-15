@extends('layouts.cbt')

@section('title', 'CBT Partner Dashboard - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    
    <!-- Top banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Partner Command Console</h1>
            <p class="text-sm text-brand-gray mt-1">Manage physical infrastructure, monitor active examinees, and inspect hardware diagnostics.</p>
        </div>
        
        <!-- Center identifier -->
        <div class="flex items-center gap-2 bg-brand-cyan/10 border border-brand-cyan/25 px-4 py-2 rounded-xl">
            <span class="text-xs text-brand-cyan font-bold">🏫 Center Hub: {{ $center->name }} ({{ $center->code }})</span>
        </div>
    </div>

    <!-- Stats Widgets Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Seat Capacity -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/30 transition-all">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Total Seat Capacity</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1.5">{{ $stats['total_seats'] }}</strong>
            <span class="block text-[9px] text-brand-cyan mt-1">Workstations Registered</span>
        </div>

        <!-- Active Examinees -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/30 transition-all">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Active Candidates</span>
            <strong class="block text-2xl font-bold text-emerald-400 mt-1.5">{{ $stats['active_candidates'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Live in Terminals</span>
        </div>

        <!-- Hardware Health -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/30 transition-all">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Terminals Online</span>
            <strong class="block text-2xl font-bold text-brand-cyan mt-1.5">{{ $stats['devices_online'] }} / {{ $devices->count() }}</strong>
            <span class="block text-[9px] text-emerald-400 mt-1">Ping Diagnostics OK</span>
        </div>

        <!-- Revenue Earned -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/30 transition-all">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Accumulated Revenue</span>
            <strong class="block text-2xl font-bold text-amber-400 mt-1.5">${{ number_format($stats['revenue'], 2) }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Commission Model: {{ $center->commission_rate }}%</span>
        </div>

        <!-- Scheduled exams -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/30 transition-all">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Scheduled Events</span>
            <strong class="block text-2xl font-bold text-purple-400 mt-1.5">{{ $stats['scheduled_exams'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Live Assessment Slots</span>
        </div>
    </div>

    <!-- Quick Partner Actions -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-4">Partner Quick Shortcuts</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('cbt.partner.centers.seats', $center->id) }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">💺</span>
                <span class="block text-xs font-bold text-brand-white">Terminal Grid Grid</span>
            </a>
            
            <a href="{{ route('cbt.partner.candidates') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">👥</span>
                <span class="block text-xs font-bold text-brand-white">Candidates Proctoring</span>
            </a>

            <a href="{{ route('cbt.partner.revenue') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">💳</span>
                <span class="block text-xs font-bold text-brand-white">Commission Payouts</span>
            </a>

            <a href="{{ route('cbt.partner.settings') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">⚙️</span>
                <span class="block text-xs font-bold text-brand-white">Center Config</span>
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Live Devices Monitoring -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between border-b border-brand-teal/10 pb-2">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Terminal Nodes Status</h3>
                <a href="{{ route('cbt.partner.centers.seats', $center->id) }}" class="text-xs text-brand-cyan hover:underline">View Seating Grid &rarr;</a>
            </div>
            
            <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-brand-dark-secondary/40 text-brand-gray border-b border-brand-teal/10">
                                <th class="p-4 font-bold">Seat</th>
                                <th class="p-4 font-bold">Device IP</th>
                                <th class="p-4 font-bold">Status</th>
                                <th class="p-4 font-bold">System CPU</th>
                                <th class="p-4 font-bold">RAM load</th>
                                <th class="p-4 font-bold">Webcam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5">
                            @forelse($devices->take(5) as $device)
                                <tr class="hover:bg-brand-teal/5 transition-all text-brand-white">
                                    <td class="p-4 font-mono font-bold text-brand-cyan">{{ $device->seat_number }}</td>
                                    <td class="p-4 font-mono text-brand-gray">{{ $device->ip_address }}</td>
                                    <td class="p-4">
                                        @if($device->system_status === 'online')
                                            <span class="inline-flex items-center gap-1 rounded bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[10px] font-bold text-emerald-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Online
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 text-[10px] font-bold text-amber-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> {{ ucfirst($device->system_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono">{{ $device->cpu_usage }}%</td>
                                    <td class="p-4 font-mono">{{ $device->ram_usage }}%</td>
                                    <td class="p-4">
                                        @if($device->webcam_status === 'active')
                                            <span class="text-emerald-400">🟢 Active</span>
                                        @else
                                            <span class="text-rose-400">🔴 Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-brand-gray">No terminal nodes registered. Use auto-discovery in settings.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Live Candidate Proctor log -->
        <div class="space-y-6">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Active Exams</h3>
            
            <div class="glass-card rounded-2xl p-6 space-y-4">
                @php
                    $activeSessions = $sessions->where('status', 'active');
                @endphp
                @forelse($activeSessions as $session)
                    <div class="p-4 rounded-xl bg-brand-dark-secondary/40 border border-brand-teal/10 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-brand-white">{{ $session->user->name }}</span>
                            <span class="text-[9px] font-mono bg-rose-500/15 text-rose-400 px-2 py-0.5 border border-rose-500/20 rounded-full">
                                Flags: {{ $session->anti_cheat_flags }}
                            </span>
                        </div>
                        <p class="text-[11px] text-brand-gray">{{ $session->exam->title }}</p>
                        <div class="flex items-center justify-between text-[10px] text-brand-cyan pt-2">
                            <span>Started: {{ $session->started_at->diffForHumans() }}</span>
                            <a href="{{ route('cbt.partner.candidates') }}" class="underline hover:text-brand-white">Manage &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-brand-gray text-xs">
                        <span class="text-2xl block mb-2">💤</span>
                        No candidates are currently taking exams.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
