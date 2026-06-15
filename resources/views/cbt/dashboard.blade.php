@extends('layouts.cbt')

@section('title', 'Diwebs Assessment Center - Candidate Dashboard')

@section('cbt_content')
<div class="space-y-8">
    
    <!-- Top banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Candidate Assessment Center</h1>
            <p class="text-sm text-brand-gray mt-1">Enroll in certification tracks, practice mock sessions, and take proctored live exams.</p>
        </div>
        
        <!-- Live indicators -->
        <div class="flex items-center gap-2 bg-brand-teal/10 border border-brand-teal/20 px-4 py-2 rounded-xl">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-xs text-brand-cyan font-bold">Secure Browser Shield Online</span>
        </div>
    </div>

    <!-- Stats Widgets Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Active Sessions</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1.5">{{ $stats['active_exams'] }}</strong>
            <span class="block text-[9px] text-brand-cyan mt-1">In Progress</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Completed</span>
            <strong class="block text-2xl font-bold text-emerald-400 mt-1.5">{{ $stats['completed_exams'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Graded Tests</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Average Score</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1.5">{{ $stats['avg_score'] }}%</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Across Attempts</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Practice Passed</span>
            <strong class="block text-2xl font-bold text-purple-400 mt-1.5">{{ $stats['practice_completed'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Training Mockups</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Certificates</span>
            <strong class="block text-2xl font-bold text-amber-400 mt-1.5">{{ $stats['certificates_earned'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Secure Credentials</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Sync Lobby</span>
            <strong class="block text-2xl font-bold text-rose-400 mt-1.5">{{ $stats['upcoming_exams'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Live Schedules</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-4">Quick Shortcuts</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('cbt.practice-tests') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">🛡️</span>
                <span class="block text-xs font-bold text-brand-white">Start Practice Test</span>
            </a>
            
            <a href="{{ route('cbt.live-exams') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">📺</span>
                <span class="block text-xs font-bold text-brand-white">Join Live Exam</span>
            </a>

            <a href="{{ route('cbt.results.history') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">📊</span>
                <span class="block text-xs font-bold text-brand-white">View Results</span>
            </a>

            <a href="{{ route('cbt.certificates') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">🏆</span>
                <span class="block text-xs font-bold text-brand-white">Download Certificate</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Available Exams Catalog -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Available Assessments</h3>
            
            @forelse($exams as $exam)
                <div class="glass-card rounded-2xl p-6 border-l-4 border-l-brand-teal flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-base font-bold text-brand-white">{{ $exam->title }}</h4>
                        <p class="text-xs text-brand-gray mt-1.5 leading-relaxed">{{ $exam->description }}</p>
                        <div class="mt-4 flex items-center gap-4 text-[10px] text-brand-gray uppercase font-bold tracking-wider">
                            <span>⏱ {{ $exam->duration_minutes }} Min</span>
                            <span>📋 {{ $exam->total_questions }} Questions</span>
                            <span>🎯 Pass Score: {{ $exam->passing_score }}%</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <form action="{{ route('cbt.exam.start', $exam->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                                Launch Standard
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                    No active examinations are scheduled at this time.
                </div>
            @endforelse
        </div>

        <!-- Upcoming Proctored Panel -->
        <div class="space-y-6">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Upcoming Live Event</h3>
            
            <div class="glass-card rounded-2xl p-6 space-y-4">
                @if($upcomingLive)
                    <div class="text-center p-2">
                        <span class="text-3xl animate-bounce block mb-2">📺</span>
                        <h4 class="text-sm font-bold text-brand-white">{{ $upcomingLive->exam->title }}</h4>
                        <p class="text-xs text-brand-gray mt-1">Code: {{ $upcomingLive->exam->code }}</p>
                        
                        <div class="my-4 text-xs font-mono text-brand-cyan bg-[#1A1D21] border border-brand-teal/10 py-2 rounded-xl">
                            Scheduled: {{ $upcomingLive->scheduled_at->format('M d, H:i') }}
                        </div>

                        <a href="{{ route('cbt.live-exams.lobby', $upcomingLive->id) }}" class="w-full block text-center rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-extrabold text-brand-dark-secondary hover:opacity-90 active:scale-95 transition-all">
                            ⚡ Join Proctor Lobby
                        </a>
                    </div>
                @else
                    <p class="text-xs text-brand-gray text-center py-6">No synchronized proctored exam sessions are currently scheduled.</p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
