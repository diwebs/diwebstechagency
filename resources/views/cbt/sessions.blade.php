@extends('layouts.cbt')

@section('title', 'Examination Logs - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">My Active &amp; Scored Sessions</h1>
            <p class="text-sm text-brand-gray mt-1">Review validation timestamps, connected devices terminal logs, and proctor parameters.</p>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-6">
        @forelse($extendedSessions as $session)
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-brand-teal/10 pb-6 last:border-0 last:pb-0">
                <div class="space-y-1">
                    <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[8px] font-extrabold uppercase text-brand-cyan">
                        {{ strtoupper($session->exam_mode) }} SESSION
                    </span>
                    <h4 class="text-sm font-bold text-brand-white mt-1">{{ $session->exam->title }}</h4>
                    <p class="text-[11px] text-brand-gray">Terminal Seat: <span class="text-brand-cyan">{{ $session->center ? $session->center->name : 'Remote Desktop Client' }}</span></p>
                    <p class="text-[10px] text-brand-gray/60 font-mono">Session ID: {{ $session->id }}</p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="block text-[9px] uppercase tracking-wider text-brand-gray/60 font-bold">Timestamps</span>
                        <span class="block text-xs text-brand-white font-medium">Started: {{ $session->started_at->format('M d, H:i') }}</span>
                        @if($session->ended_at)
                            <span class="block text-xs text-brand-gray/80 mt-0.5">Ended: {{ $session->ended_at->format('M d, H:i') }}</span>
                        @else
                            <span class="block text-xs text-emerald-400 font-bold mt-0.5">Active Grader Clock</span>
                        @endif
                    </div>

                    <span class="rounded px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider
                        @if($session->status === 'completed') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                        @elseif($session->status === 'flagged') bg-rose-950 text-rose-400 border border-rose-500/20
                        @else bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                        @endif">
                        {{ strtoupper($session->status) }}
                    </span>

                    @if($session->status === 'active')
                        <a href="{{ route('cbt.exam.session', $session->id) }}" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all">
                            Enter Session
                        </a>
                    @else
                        <a href="{{ route('cbt.results', $session->id) }}" class="rounded bg-brand-dark-secondary border border-brand-teal/30 hover:border-brand-teal px-4 py-2 text-xs text-brand-cyan font-bold transition-all">
                            Review Attempt
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-brand-gray text-xs">
                No active or completed assessment sessions recorded on this profile.
            </div>
        @endforelse
    </div>
</div>
@endsection
