@extends('layouts.academy')

@section('title', 'Academy Dashboard - Student Overview')

@section('academy_content')
<div>

    {{-- ─── Header ──────────────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-brand-white">Academy Control Overview</h1>
            <p class="text-sm sm:text-base text-brand-gray mt-1">Accelerate your technical skills with video courses, audio briefs, and live mentor sessions.</p>
        </div>

        {{-- Streak badge (only when active) --}}
        @if($stats['streak_days'] > 0)
        <div class="flex items-center gap-3 bg-brand-teal/10 border border-brand-teal/20 px-4 py-2 rounded-xl">
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            <div class="text-xs">
                <span class="block font-bold text-brand-cyan">Streak: {{ $stats['streak_days'] }} Day{{ $stats['streak_days'] !== 1 ? 's' : '' }}</span>
                <span class="text-[10px] text-brand-gray">Daily Goal Active</span>
            </div>
        </div>
        @endif
    </div>

    {{-- ─── NEW USER ONBOARDING PANEL ─────────────────────────────────────── --}}
    @if($stats['enrolled_courses'] === 0)
    <div class="glass-card rounded-3xl p-10 border border-brand-teal/20 bg-gradient-to-br from-brand-dark-secondary to-[#1A1D21] text-center relative overflow-hidden mb-8">
        <div class="absolute top-0 right-0 w-72 h-72 bg-brand-cyan/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-brand-teal/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="relative z-10">
            <div class="h-16 w-16 rounded-2xl bg-brand-cyan/15 border border-brand-cyan/30 flex items-center justify-center text-brand-cyan mx-auto mb-4">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-brand-white mb-2">Welcome to Diwebs Academy!</h2>
            <p class="text-sm sm:text-base text-brand-gray max-w-md mx-auto leading-relaxed mb-8">
                Your learning journey starts here. Browse our courses, enroll in your first bootcamp, and unlock certificates, live classes, and 1-on-1 mentorship sessions.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('academy.courses') }}"
                   class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-3.5 text-sm font-bold text-brand-dark-secondary shadow-lg hover:opacity-90 active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Browse Courses
                </a>
                <a href="{{ route('academy.mentorship') }}"
                   class="rounded-xl border border-brand-teal/30 bg-brand-dark-secondary/60 px-8 py-3.5 text-sm font-bold text-brand-cyan hover:border-brand-cyan hover:bg-brand-teal/10 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Book a Mentor
                </a>
            </div>
        </div>
    </div>
    @else

    {{-- ─── Quick Actions ───────────────────────────────────────────────────── --}}
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 mb-8">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-4">Quick Action Shortcuts</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('academy.courses') }}"
               class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <div class="h-10 w-10 rounded-xl bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-center text-brand-cyan mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-white">Resume Course</span>
            </a>

            @if($hasLivePlan)
            <a href="{{ route('academy.live-classes') }}"
               class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <div class="h-10 w-10 rounded-xl bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-center text-brand-cyan mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-white">Join Live Class</span>
            </a>
            @else
            <div class="rounded-xl bg-brand-dark-secondary/30 border border-brand-teal/5 p-4 text-center opacity-60 relative overflow-hidden cursor-not-allowed">
                <div class="h-10 w-10 rounded-xl bg-brand-dark-secondary border border-brand-teal/10 flex items-center justify-center text-brand-gray mx-auto mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-gray">Live Classes</span>
                <span class="absolute top-1.5 right-1.5 text-[8px] font-bold text-amber-400 bg-amber-950/40 border border-amber-500/20 px-1.5 py-0.5 rounded-full uppercase">Plan Required</span>
            </div>
            @endif

            @if($hasAudioPlan)
            <a href="{{ route('academy.audio-learning') }}"
               class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <div class="h-10 w-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-white">Continue Audio</span>
            </a>
            @else
            <div class="rounded-xl bg-brand-dark-secondary/30 border border-brand-teal/5 p-4 text-center opacity-60 relative overflow-hidden cursor-not-allowed">
                <div class="h-10 w-10 rounded-xl bg-brand-dark-secondary border border-brand-teal/10 flex items-center justify-center text-brand-gray mx-auto mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-gray">Audio Learning</span>
                <span class="absolute top-1.5 right-1.5 text-[8px] font-bold text-purple-400 bg-purple-950/40 border border-purple-500/20 px-1.5 py-0.5 rounded-full uppercase">Plan Required</span>
            </div>
            @endif

            <a href="{{ route('academy.mentorship') }}"
               class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <div class="h-10 w-10 rounded-xl bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-center text-brand-cyan mx-auto mb-2 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="block text-xs font-bold text-brand-white">Book Session</span>
            </a>
        </div>
    </div>

    {{-- ─── Stat Widgets ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-xs font-bold text-brand-gray uppercase tracking-wider">Enrolled Courses</span>
            <strong class="block text-3xl font-extrabold text-brand-white mt-1.5">{{ $stats['enrolled_courses'] }}</strong>
            <span class="block text-xs text-brand-cyan mt-1 font-medium">Bootcamps Active</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-xs font-bold text-brand-gray uppercase tracking-wider">Completed</span>
            <strong class="block text-3xl font-extrabold text-emerald-400 mt-1.5">{{ $stats['completed_courses'] }}</strong>
            <span class="block text-xs text-brand-gray mt-1 font-medium">Syllabus Passed</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-xs font-bold text-brand-gray uppercase tracking-wider">Audio Lessons</span>
            <strong class="block text-3xl font-extrabold text-purple-400 mt-1.5">{{ $stats['audio_completed'] }}</strong>
            <span class="block text-xs text-brand-gray mt-1 font-medium">Lectures &amp; Summaries</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-xs font-bold text-brand-gray uppercase tracking-wider">Certificates</span>
            <strong class="block text-3xl font-extrabold text-amber-400 mt-1.5">{{ $stats['certificates_earned'] }}</strong>
            <span class="block text-xs text-brand-gray mt-1 font-medium">Enterprise Validated</span>
        </div>
    </div>

    {{-- ─── Live Class & Mentorship Widgets ────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

        {{-- Live Class --}}
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-brand-teal/15 border border-brand-teal/30 flex items-center justify-center text-brand-cyan shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Upcoming Live Class</h4>
                    @if($hasLivePlan && $stats['upcoming_live_class_title'])
                        <span class="block text-base font-bold text-brand-white mt-1">{{ $stats['upcoming_live_class_title'] }}</span>
                        <span class="block text-xs text-brand-gray">{{ $stats['upcoming_live_class_time'] }}</span>
                    @elseif($hasLivePlan)
                        <span class="block text-base font-bold text-brand-white mt-1">No Live Classes Scheduled</span>
                        <span class="block text-xs text-brand-gray">Check back soon</span>
                    @else
                        <span class="block text-base font-bold text-amber-400 mt-1">Live Class Access Required</span>
                        <span class="block text-xs text-brand-gray">Contact admin to upgrade your plan</span>
                    @endif
                </div>
            </div>
            @if($hasLivePlan && $stats['upcoming_live_class_url'])
                <a href="{{ $stats['upcoming_live_class_url'] }}" target="_blank"
                   class="rounded-xl bg-brand-cyan text-brand-dark-secondary text-xs px-4 py-2.5 font-extrabold hover:opacity-90 shadow shrink-0">
                    Join Session
                </a>
            @elseif(!$hasLivePlan)
                <a href="{{ route('academy.live-classes') }}"
                   class="rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs px-4 py-2.5 font-bold transition-all shrink-0">
                    Upgrade
                </a>
            @else
                <span class="text-xs text-brand-gray shrink-0">Nothing Scheduled</span>
            @endif
        </div>

        {{-- 1-on-1 Mentorship --}}
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-brand-teal/15 border border-brand-teal/30 flex items-center justify-center text-brand-cyan shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Next 1-on-1 Coaching</h4>
                    @if($stats['upcoming_mentorship_title'])
                        <span class="block text-base font-bold text-brand-white mt-1">{{ $stats['upcoming_mentorship_title'] }}</span>
                        <span class="block text-xs text-brand-gray">{{ $stats['upcoming_mentorship_time'] }}</span>
                    @else
                        <span class="block text-base font-bold text-brand-white mt-1">No Bookings Yet</span>
                        <span class="block text-xs text-brand-gray">Schedule a session with a mentor</span>
                    @endif
                </div>
            </div>
            @if($stats['upcoming_mentorship_url'])
                <a href="{{ $stats['upcoming_mentorship_url'] }}" target="_blank"
                   class="rounded-xl bg-brand-cyan text-brand-dark-secondary text-xs px-4 py-2.5 font-extrabold hover:opacity-90 shadow shrink-0">
                    Launch Meet
                </a>
            @else
                <a href="{{ route('academy.mentorship') }}"
                   class="rounded-xl bg-brand-dark-secondary border border-brand-teal/30 hover:border-brand-teal text-brand-cyan text-xs px-4 py-2.5 font-bold transition-all shrink-0">
                    Book Now
                </a>
            @endif
        </div>
    </div>

    {{-- ─── Audio Learning Quick-Preview Widget ────────────────────────────── --}}
    @if($latestAudio)
        @if($hasAudioPlan)
        <div class="glass-card rounded-2xl p-6 border border-purple-500/20 bg-gradient-to-r from-brand-dark-secondary to-[#1A1D21] mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-purple-500/5 rounded-full blur-[60px] pointer-events-none"></div>
            <div class="flex items-center gap-5 relative z-10">
                <div class="h-14 w-14 rounded-full bg-purple-500/10 border border-purple-500/30 flex items-center justify-center shrink-0">
                    <span class="text-2xl">🎧</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider block mb-0.5">Audio Learning — Latest Lesson</span>
                    <h4 class="text-sm font-bold text-brand-white">{{ $latestAudio->title }}</h4>
                    <p class="text-[10px] text-brand-gray mt-0.5">
                        Instructor: <span class="text-purple-300">{{ $latestAudio->instructor_name }}</span>
                        &nbsp;·&nbsp;
                        {{ floor($latestAudio->duration_seconds / 60) }}:{{ sprintf('%02d', $latestAudio->duration_seconds % 60) }}
                        &nbsp;·&nbsp; {{ strtoupper($latestAudio->format) }}
                    </p>
                </div>
            </div>
            <a href="{{ route('academy.audio-learning') }}"
               class="relative z-10 shrink-0 rounded-xl bg-purple-600/80 hover:bg-purple-600 border border-purple-500/40 px-5 py-2.5 text-xs font-bold text-white transition-all active:scale-95 shadow">
                ▶ Play Now →
            </a>
        </div>
        @else
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 bg-brand-dark-secondary/10 mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 relative overflow-hidden opacity-80">
            <div class="flex items-center gap-5 relative z-10">
                <div class="h-14 w-14 rounded-full bg-brand-dark-secondary border border-brand-teal/10 flex items-center justify-center shrink-0">
                    <span class="text-xl">🔒</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-brand-gray uppercase tracking-wider block mb-0.5">Audio Learning — Subscribed Feature</span>
                    <h4 class="text-sm font-bold text-brand-gray/50 line-through">LMS Audio Briefs & Podcasts</h4>
                    <p class="text-[10px] text-brand-gray/40 mt-0.5">
                        Podcast system is locked. Upgrade to an Audio Plan to stream summaries.
                    </p>
                </div>
            </div>
            <div class="shrink-0 rounded-xl bg-brand-dark-secondary border border-brand-teal/15 px-5 py-2.5 text-xs font-bold text-brand-gray/60 cursor-not-allowed select-none">
                🔒 Locked / Plan Required
            </div>
        </div>
        @endif
    @endif

    {{-- ─── Charts Section ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Weekly Study Time Bar Chart --}}
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-6">Weekly Study Activity</h3>
            <div class="relative h-48 w-full flex items-end justify-between px-2 pt-6">
                {{-- Grid lines --}}
                <div class="absolute inset-0 flex flex-col justify-between text-[9px] text-brand-gray/30 pointer-events-none">
                    <div class="border-b border-brand-teal/5 pb-1 w-full text-right">6h</div>
                    <div class="border-b border-brand-teal/5 pb-1 w-full text-right">4h</div>
                    <div class="border-b border-brand-teal/5 pb-1 w-full text-right">2h</div>
                    <div class="w-full text-right">0h</div>
                </div>
                {{-- Bars --}}
                @foreach($weeklyHours as $item)
                <div class="flex flex-col items-center flex-1 z-10">
                    <span class="text-[10px] text-brand-cyan font-bold mb-1 opacity-0 hover:opacity-100 transition-opacity">{{ $item['val'] }}h</span>
                    <div class="w-8 rounded-t-lg transition-all duration-500 shadow-lg hover:brightness-110 cursor-pointer
                         {{ $item['val'] > 0
                             ? 'bg-gradient-to-t from-brand-teal to-brand-cyan shadow-brand-teal/10'
                             : 'bg-brand-dark-secondary/60 border border-brand-teal/10' }}"
                         style="height: {{ $item['height'] }}">
                    </div>
                    <span class="text-[10px] text-brand-gray mt-2">{{ $item['day'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Completion & Progress Ring Meters --}}
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex flex-col justify-between">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4">LMS Metrics Rate</h3>

            <div class="flex items-center justify-around py-4">
                {{-- Completion Meter --}}
                @php
                    $cr = $stats['completion_rate'];
                    $crDash = $cr; // out of 100
                @endphp
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center h-24 w-24">
                        <svg class="absolute inset-0 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-brand-dark-secondary" stroke="currentColor" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="text-brand-cyan" stroke-dasharray="{{ $crDash }}, 100" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        </svg>
                        <span class="text-sm font-extrabold text-brand-white">{{ $cr }}%</span>
                    </div>
                    <span class="text-[10px] text-brand-gray uppercase tracking-wider font-bold mt-3">Completion</span>
                </div>

                {{-- Audio Progress Meter --}}
                @php
                    $totalAudio = max(1, $stats['audio_completed']);
                    $audioRate  = min(100, (int) round(($stats['audio_completed'] / $totalAudio) * 100));
                @endphp
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center h-24 w-24">
                        <svg class="absolute inset-0 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-brand-dark-secondary" stroke="currentColor" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="text-purple-400" stroke-dasharray="{{ $audioRate }}, 100" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        </svg>
                        <span class="text-sm font-extrabold text-brand-white">{{ $audioRate }}%</span>
                    </div>
                    <span class="text-[10px] text-brand-gray uppercase tracking-wider font-bold mt-3">Audio Access</span>
                </div>
            </div>

            <div class="text-center text-[10px] text-brand-gray/60 border-t border-brand-teal/5 pt-3">
                Updated from active LMS logs.
            </div>
        </div>

    </div>
    @endif {{-- end enrolled check --}}

</div>
@endsection
