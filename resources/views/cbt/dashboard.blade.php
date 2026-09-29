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
                <div class="h-10 w-10 rounded-xl bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-center text-brand-cyan mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-white">Start Practice Test</span>
            </a>
            
            <a href="{{ route('cbt.live-exams') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <div class="h-10 w-10 rounded-xl bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-center text-brand-cyan mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-white">Join Live Exam</span>
            </a>

            <a href="{{ route('cbt.results.history') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <div class="h-10 w-10 rounded-xl bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-center text-brand-cyan mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6a2 2 0 002 2h2a2 2 0 002-2V9a2 2 0 00-2-2h-3l-1-2H9L8 7H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-white">View Results</span>
            </a>

            <a href="{{ route('cbt.certificates') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <div class="h-10 w-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
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
                        <p class="text-xs sm:text-sm text-brand-gray mt-1.5 leading-relaxed">{{ $exam->description }}</p>
                        <div class="mt-4 flex items-center gap-4 text-xs text-brand-gray uppercase font-bold tracking-wider">
                            <span>Duration: {{ $exam->duration_minutes }} Min</span>
                            <span>Questions: {{ $exam->total_questions }}</span>
                            <span>Pass Score: {{ $exam->passing_score }}%</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <form action="{{ route('cbt.exam.start', $exam->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-5 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                                Launch Assessment
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
                        <div class="h-12 w-12 rounded-2xl bg-brand-cyan/15 border border-brand-cyan/30 flex items-center justify-center text-brand-cyan mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-base font-bold text-brand-white">{{ $upcomingLive->exam->title }}</h4>
                        <p class="text-xs text-brand-gray mt-1">Code: {{ $upcomingLive->exam->code }}</p>
                        
                        <div class="my-4 text-xs font-mono text-brand-cyan bg-[#1A1D21] border border-brand-teal/10 py-2.5 rounded-xl">
                            Scheduled: {{ $upcomingLive->scheduled_at->format('M d, H:i') }}
                        </div>

                        <a href="{{ route('cbt.live-exams.lobby', $upcomingLive->id) }}" class="w-full block text-center rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-extrabold text-brand-dark-secondary hover:opacity-90 active:scale-95 transition-all">
                            Join Proctor Lobby
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
