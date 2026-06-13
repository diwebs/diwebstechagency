@extends('layouts.app')

@section('title', 'CBT Results - Digital Assessment Portal')

@section('content')
<div class="mx-auto max-w-xl px-4">
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden text-center">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10 space-y-6">
            <h1 class="text-xl font-bold text-brand-white">Exam Submission Complete</h1>
            
            <!-- Icon/Visual based on score -->
            <div class="mx-auto h-24 w-24 rounded-full border-4 flex items-center justify-center text-4xl 
                @if($session->score >= $session->exam->passing_score) border-emerald-500 text-emerald-400 bg-emerald-950/20
                @else border-rose-500 text-rose-400 bg-rose-950/20
                @endif">
                {{ $session->score >= $session->exam->passing_score ? '✔' : '✘' }}
            </div>

            <div>
                <span class="block text-xs uppercase text-brand-cyan tracking-wider font-semibold">Your Final Score</span>
                <span class="text-5xl font-mono font-extrabold text-brand-white">{{ $session->score }}%</span>
            </div>

            <!-- Detailed stats -->
            <div class="border-t border-brand-teal/10 pt-6 space-y-3 text-sm text-brand-gray text-left">
                <div class="flex justify-between">
                    <span>Passing Score:</span>
                    <strong class="text-brand-white">{{ $session->exam->passing_score }}%</strong>
                </div>
                <div class="flex justify-between">
                    <span>Outcome:</span>
                    <strong class="{{ $session->score >= $session->exam->passing_score ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $session->score >= $session->exam->passing_score ? 'PASSED' : 'FAILED' }}
                    </strong>
                </div>
                <div class="flex justify-between">
                    <span>Anti-Cheat Status:</span>
                    <strong class="{{ $session->status === 'flagged' ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ $session->status === 'flagged' ? 'Flagged (Audit Needed)' : 'Verified Clean' }}
                    </strong>
                </div>
            </div>

            <div class="pt-6">
                <a href="{{ route('cbt.dashboard') }}" class="inline-block rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
