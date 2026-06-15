@extends('layouts.cbt')

@section('title', 'Exam Graded Records - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Examination Graded Records</h1>
            <p class="text-sm text-brand-gray mt-1">Review scored performance records, subject accuracy rates, and time breakdowns.</p>
        </div>
    </div>

    <!-- Analytical Graphs (Score Performance & Subject Breakdown) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart 1: Accuracies (Circular indicators) -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex flex-col justify-between">
            <h3 class="text-xs font-semibold uppercase text-brand-cyan tracking-wider mb-4">Core Accuracy Parameters</h3>
            <div class="flex items-center justify-around py-2">
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center h-20 w-20">
                        <svg class="absolute inset-0 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-brand-dark-secondary" stroke="currentColor" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-brand-cyan" stroke-dasharray="82, 100" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="text-xs font-extrabold text-brand-white">82%</span>
                    </div>
                    <span class="text-[9px] text-brand-gray font-bold mt-2.5">General Knowledge</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center h-20 w-20">
                        <svg class="absolute inset-0 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-brand-dark-secondary" stroke="currentColor" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-brand-teal" stroke-dasharray="64, 100" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="text-xs font-extrabold text-brand-white">64%</span>
                    </div>
                    <span class="text-[9px] text-brand-gray font-bold mt-2.5">Technical Syntax</span>
                </div>
            </div>
        </div>

        <!-- Subject Accuracy Matrix -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-semibold uppercase text-brand-cyan tracking-wider mb-2">Subject Performance Matrix</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between text-[11px] text-brand-gray mb-1">
                        <span>Cloud Architecture Concepts</span>
                        <span class="text-brand-cyan font-bold">85% Accuracy</span>
                    </div>
                    <div class="h-1.5 w-full bg-brand-dark-secondary rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-brand-teal to-brand-cyan" style="width: 85%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-[11px] text-brand-gray mb-1">
                        <span>VPC Security &amp; Networks</span>
                        <span class="text-brand-cyan font-bold">70% Accuracy</span>
                    </div>
                    <div class="h-1.5 w-full bg-brand-dark-secondary rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-brand-teal to-brand-cyan" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scored records list -->
    <div class="space-y-4">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Graded Sessions History</h3>
        
        <div class="glass-card rounded-2xl p-6 space-y-4">
            @forelse($extendedSessions as $session)
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-brand-teal/10 pb-4 last:border-0 last:pb-0">
                    <div>
                        <h4 class="text-sm font-bold text-brand-white">{{ $session->exam->title }}</h4>
                        <p class="text-xs text-brand-gray mt-1">Code: {{ $session->exam->code }} · Started: {{ $session->started_at->format('M d, H:i') }}</p>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <span class="block text-[10px] text-brand-gray uppercase font-bold tracking-wider">Score</span>
                            <strong class="text-base font-mono font-black {{ $session->score >= $session->exam->passing_score ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $session->score !== null ? $session->score . '%' : 'N/A' }}
                            </strong>
                        </div>

                        <span class="rounded px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider
                            @if($session->status === 'completed') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                            @elseif($session->status === 'flagged') bg-rose-950 text-rose-400 border border-rose-500/20
                            @else bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                            @endif">
                            {{ strtoupper($session->status) }}
                        </span>

                        <a href="{{ route('cbt.results', $session->id) }}" class="rounded bg-brand-dark-secondary border border-brand-teal/30 hover:border-brand-teal px-3 py-1.5 text-xs text-brand-cyan font-bold transition-all">
                            Verify Breakdown
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-brand-gray text-center py-6">No graded exam sessions are recorded in your profile logs.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
