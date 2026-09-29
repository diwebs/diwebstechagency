@extends('layouts.admin')

@section('title', 'CRM Dashboard - Diwebs CRM Operations')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">CRM Control Center</h1>
            <p class="text-xs text-brand-gray mt-1">Unified view of sales operations, clients, and support indicators. Account: <span class="text-brand-cyan font-bold">{{ $auth['role'] }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs text-brand-gray">Sync Status: <strong class="text-emerald-400">Live</strong></span>
        </div>
    </div>

    <!-- Stats Cards Grid (10 Stats) -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Total Leads</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1">{{ $stats['total_leads'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Qualified Leads</span>
            <strong class="block text-2xl font-bold text-brand-cyan mt-1">{{ $stats['qualified_leads'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Active Clients</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1">{{ $stats['active_clients'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Closed Won</span>
            <strong class="block text-2xl font-bold text-emerald-400 mt-1">{{ $stats['closed_deals'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Closed Lost</span>
            <strong class="block text-2xl font-bold text-rose-400 mt-1">{{ $stats['lost_deals'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Pending Tasks</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1">{{ $stats['pending_tasks'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300 col-span-2 md:col-span-1">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Revenue Generated</span>
            <strong class="block text-xl font-bold text-emerald-400 mt-2">₦{{ number_format($stats['revenue_generated'], 0) }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Win Rate</span>
            <strong class="block text-2xl font-bold text-brand-cyan mt-1">{{ $stats['conversion_rate'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Open Tickets</span>
            <strong class="block text-2xl font-bold text-amber-400 mt-1">{{ $stats['open_tickets'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-4 text-center border border-brand-teal/10 hover:border-brand-teal/20 transition-all hover:scale-[1.02] duration-300">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Active Campaigns</span>
            <strong class="block text-2xl font-bold text-brand-cyan mt-1">{{ $stats['active_campaigns'] }}</strong>
        </div>
    </div>

    <!-- Charts Row (CSS Visualizations) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lead Conversion Chart & Sales Pipeline -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 lg:col-span-2">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4">Sales Pipeline Stages Breakdown</h3>
            <div class="space-y-4">
                @forelse($pipelineStats as $st)
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-brand-white font-medium">{{ $st['stage'] }}</span>
                            <span class="font-bold text-brand-cyan">{{ $st['count'] }} Deals</span>
                        </div>
                        <div class="w-full h-2 bg-brand-dark rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-brand-teal to-brand-cyan rounded-full" style="width: {{ min(100, max(5, $st['count'] * 15)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No pipeline deal data registered yet.</p>
                @endforelse
            </div>
            
            <div class="mt-6 border-t border-brand-teal/10 pt-4 grid grid-cols-3 text-center text-xs">
                <div>
                    <span class="block text-[10px] text-brand-gray">Lead Conversion</span>
                    <strong class="text-brand-white font-bold block mt-1">45.2%</strong>
                </div>
                <div>
                    <span class="block text-[10px] text-brand-gray">Client Retention</span>
                    <strong class="text-emerald-400 font-bold block mt-1">94.8%</strong>
                </div>
                <div>
                    <span class="block text-[10px] text-brand-gray">Deal growth</span>
                    <strong class="text-brand-cyan font-bold block mt-1">+12.5%</strong>
                </div>
            </div>
        </div>

        <!-- Lead acquisition sources -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4">Lead Capture Channels</h3>
            <div class="space-y-4">
                @forelse($leadSourceStats as $src)
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-brand-white font-medium">{{ ucfirst(str_replace('_', ' ', $src['source'])) }}</span>
                            <span class="font-bold text-brand-cyan">{{ $src['count'] }} Leads</span>
                        </div>
                        <div class="w-full h-2 bg-brand-dark rounded-full overflow-hidden">
                            <div class="h-full bg-brand-teal rounded-full" style="width: {{ min(100, max(10, $src['count'] * 20)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No lead acquisition logs recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Details/Widgets Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Leads -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4 flex items-center gap-2">
                <span>🎯</span> Recent Captured Leads
            </h3>
            <div class="space-y-3">
                @forelse($recentLeads as $lead)
                    <div class="flex items-center justify-between border-b border-brand-teal/10 pb-3 last:border-0 last:pb-0 text-xs">
                        <div>
                            <strong class="block text-brand-white text-sm">{{ $lead->full_name }}</strong>
                            <span class="text-brand-gray text-[10px]">{{ $lead->company_name }} — Interest: {{ $lead->service_interest }}</span>
                        </div>
                        <div class="text-right">
                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase {{ $lead->status === 'New' ? 'bg-brand-cyan/20 text-brand-cyan' : 'bg-brand-teal/10 text-brand-white' }}">
                                {{ $lead->status }}
                            </span>
                            <span class="block text-[9px] text-brand-gray/60 mt-1">Score: {{ $lead->lead_score }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No new leads registered.</p>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4 flex items-center gap-2">
                <span>📅</span> Upcoming Meetings &amp; Demos
            </h3>
            <div class="space-y-3">
                @forelse($upcomingMeetings as $meet)
                    <div class="flex items-center justify-between border-b border-brand-teal/10 pb-3 last:border-0 last:pb-0 text-xs">
                        <div>
                            <strong class="block text-brand-white text-sm">{{ $meet->title }}</strong>
                            <span class="text-brand-gray text-[10px]">{{ $meet->meeting_type }} — Duration: {{ $meet->duration_minutes }} min</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-brand-cyan font-bold">{{ $meet->scheduled_at->format('M d, H:i A') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No upcoming appointments registered.</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4 flex items-center gap-2">
                <span>✅</span> Pending Tasks Checklist
            </h3>
            <div class="space-y-3">
                @forelse($pendingTasks as $task)
                    <div class="flex items-center justify-between border-b border-brand-teal/10 pb-3 last:border-0 last:pb-0 text-xs">
                        <div>
                            <strong class="block text-brand-white text-sm">{{ $task->title }}</strong>
                            <span class="text-brand-gray text-[10px]">{{ $task->task_type }} — Priority: <strong class="text-amber-400">{{ $task->priority }}</strong></span>
                        </div>
                        <div class="text-right">
                            <span class="text-brand-gray block text-[10px]">Due: {{ $task->due_date ? $task->due_date->format('M d') : 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No active tasks registered.</p>
                @endforelse
            </div>
        </div>

        <!-- Contract Expirations -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4 flex items-center gap-2">
                <span>📜</span> Contracts Expiry Alerts
            </h3>
            <div class="space-y-3">
                @forelse($contractExpirations as $contract)
                    <div class="flex items-center justify-between border-b border-brand-teal/10 pb-3 last:border-0 last:pb-0 text-xs">
                        <div>
                            <strong class="block text-brand-white text-sm">{{ $contract->title }}</strong>
                            <span class="text-brand-gray text-[10px]">No: {{ $contract->contract_number }} — Value: ₦{{ number_format($contract->value, 0) }}</span>
                        </div>
                        <div class="text-right text-[10px]">
                            <span class="block text-rose-400 font-bold">Expires: {{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No expiring contracts inside the 60-day window.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
