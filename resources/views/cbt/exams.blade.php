@extends('layouts.cbt')

@section('title', 'Scheduled Examinations - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Scheduled Examinations</h1>
            <p class="text-sm text-brand-gray mt-1">Select an exam session. You will need the passcode provided by your institution or CBT supervisor to launch the terminal.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ activeCodePrompt: null, accessCode: '' }">
        @forelse($exams as $exam)
            <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border border-brand-teal/10 hover:border-brand-teal/20 transition-all">
                <div>
                    <h4 class="text-base font-bold text-brand-white">{{ $exam->title }}</h4>
                    <p class="text-xs text-brand-gray/80 mt-2 leading-relaxed">{{ $exam->description }}</p>
                    
                    <div class="mt-4 flex items-center gap-4 text-[10px] text-brand-gray uppercase font-bold tracking-wider">
                        <span>⏱ {{ $exam->duration_minutes }} Min</span>
                        <span>📋 {{ $exam->total_questions }} Questions</span>
                        <span>🎯 Pass: {{ $exam->passing_score }}%</span>
                    </div>
                </div>

                <div class="mt-6 border-t border-brand-teal/5 pt-4">
                    <template x-if="activeCodePrompt === {{ $exam->id }}">
                        <div class="space-y-3">
                            <label class="block text-[10px] text-brand-gray uppercase font-bold tracking-wider">Enter Access Code:</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="accessCode" placeholder="Code (e.g. CBT-101)" class="flex-1 rounded bg-[#1A1D21] border border-brand-teal/30 px-3.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan">
                                <form action="{{ route('cbt.exam.start', $exam->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary cursor-pointer">
                                        Verify &amp; Start
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>
                    <template x-if="activeCodePrompt !== {{ $exam->id }}">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] text-brand-cyan font-bold uppercase tracking-wider">Verification Required</span>
                            <button @click="activeCodePrompt = {{ $exam->id }}" class="rounded bg-brand-teal/10 border border-brand-teal/20 px-4 py-2 text-xs font-bold text-brand-cyan hover:bg-brand-teal/20 transition-all cursor-pointer">
                                Enter Access Code
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                No scheduled certifications or academic exams exist in this profile.
            </div>
        @endforelse
    </div>
</div>
@endsection
