@extends('layouts.academy')

@section('title', 'Academy Control - Student Overview')

@section('academy_content')
<div>
    <!-- Header banner -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Academy Control Overview</h1>
            <p class="text-sm text-brand-gray mt-1">Accelerate your skills with video, audio, and live mentor sessions.</p>
        </div>
        
        <!-- Learning streak banner -->
        <div class="flex items-center gap-3 bg-brand-teal/10 border border-brand-teal/20 px-4 py-2 rounded-xl">
            <span class="text-lg">🔥</span>
            <div class="text-xs">
                <span class="block font-bold text-brand-cyan">Streak: 6 Days</span>
                <span class="text-[9px] text-brand-gray">Daily Goal Active</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 mb-8">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-4">Quick Action Shortcuts</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('academy.courses') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">🎓</span>
                <span class="block text-xs font-bold text-brand-white">Resume Course</span>
            </a>
            
            <a href="{{ route('academy.live-classes') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">📺</span>
                <span class="block text-xs font-bold text-brand-white">Join Live Class</span>
            </a>

            <a href="{{ route('academy.audio-learning') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">🎧</span>
                <span class="block text-xs font-bold text-brand-white">Continue Audio</span>
            </a>

            <a href="{{ route('academy.mentorship') }}" class="rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/10 hover:border-brand-cyan/40 p-4 text-center group transition-all">
                <span class="block text-xl mb-1.5 transition-transform group-hover:scale-110">📅</span>
                <span class="block text-xs font-bold text-brand-white">Book Session</span>
            </a>
        </div>
    </div>

    <!-- Widgets Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Enrolled Courses</span>
            <strong class="block text-2xl font-bold text-brand-white mt-1.5">{{ $stats['enrolled_courses'] }}</strong>
            <span class="block text-[9px] text-brand-cyan mt-1">Bootcamps Active</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Completed Courses</span>
            <strong class="block text-2xl font-bold text-emerald-400 mt-1.5">{{ $stats['completed_courses'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Syllabus Passed</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Audio Completed</span>
            <strong class="block text-2xl font-bold text-purple-400 mt-1.5">{{ $stats['audio_completed'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Lectures &amp; Summaries</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Certificates Earned</span>
            <strong class="block text-2xl font-bold text-amber-400 mt-1.5">{{ $stats['certificates_earned'] }}</strong>
            <span class="block text-[9px] text-brand-gray mt-1">Enterprise Validated</span>
        </div>
    </div>

    <!-- Live & Mentorship schedule status widgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-3xl">📺</span>
                <div>
                    <h4 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Upcoming Live Class</h4>
                    <span class="block text-sm font-bold text-brand-white mt-1">{{ $stats['upcoming_live_class_title'] }}</span>
                    <span class="block text-[10px] text-brand-gray">{{ $stats['upcoming_live_class_time'] }}</span>
                </div>
            </div>
            @if($stats['upcoming_live_class_url'])
                <a href="{{ $stats['upcoming_live_class_url'] }}" target="_blank" class="rounded bg-brand-cyan text-brand-dark-secondary text-xs px-3.5 py-2 font-extrabold hover:opacity-90 shadow">
                    Join Session
                </a>
            @else
                <span class="text-[10px] text-brand-gray">No Live Scheduled</span>
            @endif
        </div>

        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-3xl">📅</span>
                <div>
                    <h4 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Next 1-on-1 Coaching</h4>
                    <span class="block text-sm font-bold text-brand-white mt-1">{{ $stats['upcoming_mentorship_title'] }}</span>
                    <span class="block text-[10px] text-brand-gray">{{ $stats['upcoming_mentorship_time'] }}</span>
                </div>
            </div>
            @if($stats['upcoming_mentorship_url'])
                <a href="{{ $stats['upcoming_mentorship_url'] }}" target="_blank" class="rounded bg-brand-cyan text-brand-dark-secondary text-xs px-3.5 py-2 font-extrabold hover:opacity-90 shadow">
                    Launch Meet
                </a>
            @else
                <a href="{{ route('academy.mentorship') }}" class="rounded bg-brand-dark-secondary border border-brand-teal/30 hover:border-brand-teal text-brand-cyan text-xs px-3.5 py-2 font-bold transition-all">
                    Book Now
                </a>
            @endif
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Chart 1: Study Time (Bar chart SVG) -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-6">Weekly Study Time Hours</h3>
            
            <div class="relative h-48 w-full flex items-end justify-between px-2 pt-6">
                <!-- Grid background lines -->
                <div class="absolute inset-0 flex flex-col justify-between text-[9px] text-brand-gray/30 pointer-events-none">
                    <div class="border-b border-brand-teal/5 pb-1 w-full text-right">6h</div>
                    <div class="border-b border-brand-teal/5 pb-1 w-full text-right">4h</div>
                    <div class="border-b border-brand-teal/5 pb-1 w-full text-right">2h</div>
                    <div class="w-full text-right">0h</div>
                </div>

                @php
                    $weeklyHours = [
                        ['day' => 'Mon', 'val' => 2.4, 'height' => '40%'],
                        ['day' => 'Tue', 'val' => 4.8, 'height' => '80%'],
                        ['day' => 'Wed', 'val' => 1.5, 'height' => '25%'],
                        ['day' => 'Thu', 'val' => 5.2, 'height' => '87%'],
                        ['day' => 'Fri', 'val' => 3.0, 'height' => '50%'],
                        ['day' => 'Sat', 'val' => 0.8, 'height' => '13%'],
                        ['day' => 'Sun', 'val' => 4.2, 'height' => '70%'],
                    ];
                @endphp

                <!-- Graph Columns -->
                @foreach($weeklyHours as $item)
                    <div class="flex flex-col items-center flex-1 z-10">
                        <span class="text-[10px] text-brand-cyan font-bold mb-1 opacity-0 hover:opacity-100 transition-opacity">{{ $item['val'] }}h</span>
                        <div class="w-8 bg-gradient-to-t from-brand-teal to-brand-cyan rounded-t-lg transition-all duration-500 shadow-lg shadow-brand-teal/10 hover:brightness-110 cursor-pointer" style="height: {{ $item['height'] }}"></div>
                        <span class="text-[10px] text-brand-gray mt-2">{{ $item['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Chart 2 & 3: Completion Rate & Attendance (Circular meters) -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex flex-col justify-between">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4">LMS Metrics Rate</h3>
            
            <div class="flex items-center justify-around py-4">
                <!-- Completion Meter -->
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center h-24 w-24">
                        <svg class="absolute inset-0 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-brand-dark-secondary" stroke="currentColor" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-brand-cyan" stroke-dasharray="75, 100" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="text-sm font-extrabold text-brand-white">75%</span>
                    </div>
                    <span class="text-[10px] text-brand-gray uppercase tracking-wider font-bold mt-3">Completion</span>
                </div>

                <!-- Attendance Meter -->
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center h-24 w-24">
                        <svg class="absolute inset-0 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-brand-dark-secondary" stroke="currentColor" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-brand-teal" stroke-dasharray="92, 100" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="text-sm font-extrabold text-brand-white">92%</span>
                    </div>
                    <span class="text-[10px] text-brand-gray uppercase tracking-wider font-bold mt-3">Live Attendance</span>
                </div>
            </div>
            
            <div class="text-center text-[10px] text-brand-gray/60 border-t border-brand-teal/5 pt-3">
                Updated in real-time from active LMS logs.
            </div>
        </div>

    </div>
</div>
@endsection
