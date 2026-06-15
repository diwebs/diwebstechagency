@extends('layouts.cbt')

@section('title', 'Live Proctored Exams - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Live Proctored Exam Center</h1>
            <p class="text-sm text-brand-gray mt-1">Synchronized nationwide or corporate testing. Webcam and screen share tracking required.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($liveExams as $liveExam)
            @php
                $isLive = $liveExam->status === 'active';
            @endphp
            <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border {{ $isLive ? 'border-rose-500/30 bg-rose-500/5 shadow' : 'border-brand-teal/10' }}">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <h4 class="text-base font-bold text-brand-white">{{ $liveExam->exam->title }}</h4>
                        <span class="rounded px-2.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wide
                              {{ $isLive ? 'bg-rose-950 text-rose-400 border border-rose-500/20 animate-pulse' : 'bg-brand-teal/10 text-brand-cyan border border-brand-teal/20' }}">
                            {{ strtoupper($liveExam->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-brand-gray/80 mt-3 leading-relaxed">{{ $liveExam->exam->description }}</p>
                    
                    <div class="mt-4 flex items-center gap-4 text-[10px] text-brand-gray uppercase font-bold tracking-wider">
                        <span>⏱ {{ $liveExam->exam->duration_minutes }} Min</span>
                        <span>📋 {{ $liveExam->exam->total_questions }} Questions</span>
                        <span>🎯 Pass: {{ $liveExam->exam->passing_score }}%</span>
                    </div>
                </div>

                <div class="mt-6 border-t border-brand-teal/5 pt-4 flex justify-between items-center">
                    <div class="text-[10px] text-brand-gray">
                        <span class="block">Scheduled Time:</span>
                        <span class="font-bold text-brand-white">{{ $liveExam->scheduled_at->format('M d, Y H:i') }}</span>
                    </div>
                    
                    <a href="{{ route('cbt.live-exams.lobby', $liveExam->id) }}" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all">
                        {{ $isLive ? 'Enter Lobby' : 'Device Test Lobby' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                No active or scheduled live proctored exams are currently listed.
            </div>
        @endforelse
    </div>
</div>
@endsection
