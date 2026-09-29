@extends('layouts.staff')

@section('title', 'My KPI Scorecards - Staff Portal')

@section('staff_content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-brand-white">My KPI &amp; Performance Scorecards</h1>
        <p class="text-xs text-brand-gray mt-1">Review operational performance metrics evaluations, productivity charts, and check management recommendation notes.</p>
    </div>

    <!-- Metrics table -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-teal/10 bg-brand-dark-secondary/20">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Performance Evaluation History</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Review Date</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-center">Productivity</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-center">Tasks Complete</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-center">Client Satisfaction</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-center">Collaboration</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Management Recommendation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4 font-bold font-mono text-[11px]">
                                {{ $rev->review_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-brand-cyan text-sm">
                                {{ $rev->productivity_score }}%
                            </td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-brand-cyan text-sm">
                                {{ $rev->task_completion_rate }}%
                            </td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-brand-cyan text-sm">
                                {{ $rev->client_satisfaction_score }}%
                            </td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-brand-cyan text-sm">
                                {{ $rev->collaboration_score }}%
                            </td>
                            <td class="px-6 py-4">
                                @if($rev->promotion_recommended)
                                    <span class="inline-flex rounded-full bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-bold text-emerald-400">Promoted Recommended</span>
                                @endif
                                <p class="text-[10px] text-brand-gray mt-1 italic">"{{ $rev->review_notes }}"</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-brand-gray">No performance reviews recorded for your account yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
