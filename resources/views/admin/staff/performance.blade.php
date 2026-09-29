@extends('layouts.admin')

@section('title', 'Performance Reviews - Admin Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white font-sans">Performance Reviews</h1>
            <p class="text-xs text-brand-gray mt-1 font-sans">Record key performance indicators (KPIs), evaluate staff efficiency, and check promotion recommendations.</p>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Pane: Record Review Form -->
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">➕ Log KPI Scorecard</h3>
                
                <form action="{{ route('admin.staff.performance.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Staff Member</label>
                        <select name="staff_id" required
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:outline-none">
                            <option value="">Select Employee</option>
                            @foreach($staff as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->staff_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Productivity (1-100)</label>
                            <input type="number" name="productivity_score" required min="1" max="100" value="90"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Tasks Comp (1-100)</label>
                            <input type="number" name="task_completion_rate" required min="1" max="100" value="90"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Client Sat (1-100)</label>
                            <input type="number" name="client_satisfaction_score" required min="1" max="100" value="90"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Collab (1-100)</label>
                            <input type="number" name="collaboration_score" required min="1" max="100" value="90"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Promotion Recommended?</label>
                        <select name="promotion_recommended" required
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:outline-none">
                            <option value="0">No Recommendation</option>
                            <option value="1">Recommend Promotion</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Review Summary Notes</label>
                        <textarea name="review_notes" rows="3" placeholder="Describe achievements, areas to improve, and observations..."
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-3 text-xs text-brand-white focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer select-none">
                        Save Review Scorecard
                    </button>
                </form>
            </div>
            
            <!-- Rankings Widget -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">🏆 Efficiency Ranking</h3>
                <div class="space-y-3 pt-2">
                    @foreach($rankings as $idx => $st)
                        @if($st->performance_reviews_avg_productivity_score > 0)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-brand-cyan">#{{ $idx + 1 }}</span>
                                    <span class="font-bold text-brand-white">{{ $st->name }}</span>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold">{{ round($st->performance_reviews_avg_productivity_score) }}%</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Pane: Review Logs Table -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Evaluation History</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                                <th class="px-4 py-3 font-bold uppercase text-[9px]">Staff Details</th>
                                <th class="px-4 py-3 font-bold uppercase text-[9px] text-center">Score Card</th>
                                <th class="px-4 py-3 font-bold uppercase text-[9px] text-center">Promo Rec</th>
                                <th class="px-4 py-3 font-bold uppercase text-[9px]">Reviewer &amp; Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                            @forelse($reviews as $rev)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="font-bold block text-sm">{{ $rev->staff->name }}</span>
                                        <span class="text-[9px] text-brand-cyan font-mono block">{{ $rev->staff->staff_id }} | {{ $rev->staff->department->name ?? '' }}</span>
                                        <span class="text-[9px] text-brand-gray block">Date: {{ $rev->review_date->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-1 text-[10px]">
                                            <div class="flex justify-between gap-4 font-mono">
                                                <span>Productivity:</span> <span class="font-bold text-brand-cyan">{{ $rev->productivity_score }}%</span>
                                            </div>
                                            <div class="flex justify-between gap-4 font-mono">
                                                <span>Tasks Comp:</span> <span class="font-bold text-brand-cyan">{{ $rev->task_completion_rate }}%</span>
                                            </div>
                                            <div class="flex justify-between gap-4 font-mono">
                                                <span>Client Sat:</span> <span class="font-bold text-brand-cyan">{{ $rev->client_satisfaction_score }}%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($rev->promotion_recommended)
                                            <span class="inline-flex rounded-full bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-extrabold text-emerald-400">Yes</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-brand-dark border border-brand-teal/20 px-2 py-0.5 text-[9px] text-brand-gray">No</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-[9px] text-brand-gray block">Reviewer: {{ $rev->reviewer->name ?? 'System' }}</span>
                                        <p class="text-[10px] text-brand-white mt-1 italic leading-relaxed">"{{ $rev->review_notes }}"</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-brand-gray">No KPI reviews logged in the database yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
