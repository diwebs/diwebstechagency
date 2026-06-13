@extends('layouts.app')

@section('title', 'CBT Candidate Portal - Assessment Engine')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Assessment Dashboard</h1>
            <p class="text-sm text-brand-gray">Review active examination schedules and view completed scores.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Available Exams -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">Available Assessments</h3>
            
            @forelse($exams as $exam)
                <div class="glass-card rounded-2xl p-6 border-l-4 border-l-brand-teal flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-lg font-bold text-brand-white">{{ $exam->title }}</h4>
                        <p class="text-xs text-brand-gray mt-1 max-w-md">{{ $exam->description }}</p>
                        <div class="mt-4 flex items-center gap-4 text-xs text-brand-gray">
                            <span class="flex items-center gap-1">⏱ {{ $exam->duration_minutes }} Min</span>
                            <span class="flex items-center gap-1">📋 {{ $exam->total_questions }} Questions</span>
                            <span class="flex items-center gap-1">🎯 Pass Score: {{ $exam->passing_score }}%</span>
                        </div>
                    </div>
                    <div>
                        <form action="{{ route('cbt.exam.start', $exam->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">Launch Exam</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="glass-card rounded-2xl p-8 text-center text-brand-gray text-sm">
                    No active examinations are scheduled at this time.
                </div>
            @endforelse
        </div>

        <!-- Exam Session History -->
        <div class="space-y-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">Test Records</h3>
            
            <div class="glass-card rounded-2xl p-6 space-y-4">
                @forelse($sessions as $session)
                    <div class="border-b border-brand-teal/10 pb-4 last:border-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-xs font-bold text-brand-white">{{ $session->exam->title }}</h4>
                                <span class="text-[10px] text-brand-gray">{{ $session->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <span class="rounded px-2 py-0.5 text-[10px] font-bold 
                                @if($session->status === 'completed') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                @elseif($session->status === 'flagged') bg-rose-950 text-rose-400 border border-rose-500/20
                                @else bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                                @endif">
                                {{ strtoupper($session->status) }}
                            </span>
                        </div>
                        <div class="mt-2 flex justify-between items-center text-xs">
                            <span class="text-brand-gray">Grade Score:</span>
                            <strong class="{{ $session->score >= $session->exam->passing_score ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $session->score !== null ? $session->score . '%' : 'N/A' }}
                            </strong>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No exam session entries recorded.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
