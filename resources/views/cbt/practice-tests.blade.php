@extends('layouts.cbt')

@section('title', 'CBT Mock Prep Center - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Mock &amp; Practice Prep Center</h1>
            <p class="text-sm text-brand-gray mt-1">Practice with unlimited attempts. View correct explanations immediately after grading.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($exams as $exam)
            <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border border-brand-teal/10 hover:border-brand-teal/20 transition-all">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <h4 class="text-base font-bold text-brand-white">{{ $exam->title }}</h4>
                        <span class="rounded bg-brand-teal/10 text-brand-cyan border border-brand-teal/20 px-2.5 py-0.5 text-[10px] font-bold uppercase">Mock</span>
                    </div>
                    <p class="text-xs text-brand-gray/80 mt-3 leading-relaxed">{{ $exam->description }}</p>
                    
                    <div class="mt-4 flex items-center gap-4 text-[10px] text-brand-gray uppercase font-bold tracking-wider">
                        <span>⏱ {{ $exam->duration_minutes }} Min</span>
                        <span>📋 {{ $exam->total_questions }} Questions</span>
                        <span>🎯 Pass: {{ $exam->passing_score }}%</span>
                    </div>
                </div>

                <div class="mt-6 border-t border-brand-teal/5 pt-4 flex justify-between items-center">
                    <span class="text-[10px] text-brand-cyan font-bold uppercase tracking-wider">Unlimited Attempts</span>
                    
                    <form action="{{ route('cbt.practice-tests.start', $exam->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                            Start Practice
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                No mock practice tests are currently configured in the syllabus bank.
            </div>
        @endforelse
    </div>
</div>
@endsection
