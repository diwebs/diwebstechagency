@extends('layouts.academy')

@section('title', 'Live Classes - Diwebs Academy')

@section('academy_content')
<div x-data="liveClassesState()" class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Academy Live Learning</h1>
            <p class="text-sm text-brand-gray mt-1">Join interactive classrooms, workshops, and coaching sessions led by core engineers.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-xs text-brand-gray">Feed Listener Live</span>
        </div>
    </div>

    <!-- Active countdown timer when a live class is near -->
    <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 bg-brand-teal/5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-3xl animate-bounce">📺</span>
            <div>
                <span class="rounded bg-brand-cyan/20 border border-brand-teal/30 px-2 py-0.5 text-[8px] font-bold text-brand-cyan uppercase tracking-wider">Next Live Session</span>
                <h4 class="text-sm font-bold text-brand-white mt-1">Advanced AI Engineering Workshop</h4>
                <p class="text-[10px] text-brand-gray">Instructor: David Miller · Starts in 15 mins</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-1.5 text-xs text-brand-white font-mono">
                <span class="bg-[#1A1D21] border border-brand-teal/10 px-2 py-1.5 rounded">00</span>
                <span>:</span>
                <span class="bg-[#1A1D21] border border-brand-teal/10 px-2 py-1.5 rounded">14</span>
                <span>:</span>
                <span class="bg-[#1A1D21] border border-brand-teal/10 px-2 py-1.5 rounded" x-text="countdownSec">59</span>
            </div>
            
            <button @click="openPreJoinModal('Advanced AI Engineering Workshop', 'David Miller', 'https://meet.google.com/abc-defg-hij')" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-black text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                ⚡ Join Meet
            </button>
        </div>
    </div>

    <!-- Live Sessions listing cards grid -->
    <div class="space-y-6">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2 flex items-center justify-between">
            <span>Classroom Schedule</span>
            <div class="flex items-center gap-2 text-[10px] font-bold text-brand-gray">
                <button @click="filter = 'all'" :class="filter === 'all' ? 'text-brand-cyan underline' : ''">All</button>
                <span>·</span>
                <button @click="filter = 'live'" :class="filter === 'live' ? 'text-brand-cyan underline' : ''">Live Now</button>
                <span>·</span>
                <button @click="filter = 'scheduled'" :class="filter === 'scheduled' ? 'text-brand-cyan underline' : ''">Scheduled</button>
            </div>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($liveSessions as $session)
                @php
                    $isLive = $session->status === 'live';
                    $isScheduled = $session->status === 'scheduled';
                @endphp
                <div x-show="filter === 'all' || (filter === 'live' && '{{ $session->status }}' === 'live') || (filter === 'scheduled' && '{{ $session->status }}' === 'scheduled')"
                     class="glass-card rounded-2xl p-6 flex flex-col justify-between border transition-all duration-300
                     {{ $isLive ? 'border-emerald-500/30 bg-emerald-500/5 shadow-md shadow-emerald-500/5' : 'border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/20' }}">
                    
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <span class="rounded-lg px-2.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wide
                                  {{ $isLive ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/20 animate-pulse' : '' }}
                                  {{ $isScheduled ? 'bg-brand-teal/10 text-brand-cyan border border-brand-teal/20' : '' }}
                                  {{ !$isLive && !$isScheduled ? 'bg-rose-950 text-rose-400 border border-rose-500/20' : '' }}">
                                {{ strtoupper($session->status) }}
                            </span>
                            
                            <span class="rounded bg-brand-dark-secondary px-2 py-0.5 text-[9px] font-bold uppercase text-brand-gray border border-brand-teal/5">
                                {{ str_replace('_', ' ', $session->session_type) }}
                            </span>
                        </div>

                        <h4 class="text-base font-bold text-brand-white leading-snug">{{ $session->title }}</h4>
                        <p class="text-[11px] text-brand-gray mt-1">Instructor: <span class="text-brand-cyan">{{ $session->teacher ? $session->teacher->name : 'Staff Mentor' }}</span></p>
                        
                        <p class="text-xs text-brand-gray/80 mt-3 leading-relaxed">{{ $session->description }}</p>
                    </div>

                    <div class="mt-6 border-t border-brand-teal/5 pt-4 flex items-center justify-between text-xs text-brand-gray">
                        <div>
                            <span class="block font-medium text-brand-white">{{ $session->date->format('M d, Y H:i') }}</span>
                            <span class="block text-[10px] text-brand-gray/60 mt-0.5">Duration: {{ $session->duration_minutes }} mins</span>
                        </div>

                        @if($isLive)
                            <button @click="openPreJoinModal('{{ $session->title }}', '{{ $session->teacher ? $session->teacher->name : 'Staff Mentor' }}', '{{ $session->meeting_url }}')" 
                                    class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-black text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                                ⚡ Enter Class
                            </button>
                        @elseif($isScheduled)
                            <button @click="alert('Classroom is scheduled to unlock at the designated date/time.')" 
                                    class="rounded-lg border border-brand-teal/20 hover:border-brand-teal bg-brand-dark-secondary/50 px-4 py-2 text-xs font-bold text-brand-cyan transition-all cursor-pointer">
                                Lock Pending
                            </button>
                        @else
                            <span class="text-[10px] text-brand-gray/50">Session Finished</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                    No active or scheduled live classrooms exist currently.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pre-join Readiness check modal -->
    <div x-show="showPreJoinModal" class="fixed inset-0 z-50 flex items-center justify-center bg-brand-dark-secondary/80 backdrop-blur-md px-4" style="display:none;">
        <div class="glass-card rounded-3xl p-6 border border-brand-cyan/25 max-w-md w-full space-y-6 relative">
            <button @click="showPreJoinModal = false" class="absolute top-5 right-5 text-brand-gray hover:text-brand-white text-xs cursor-pointer select-none">✕ Close</button>
            
            <div class="text-center">
                <span class="text-3xl">🛡️</span>
                <h3 class="text-base font-bold text-brand-white mt-3">LMS Classroom Readiness Checklist</h3>
                <p class="text-[11px] text-brand-gray/80 mt-1">Verification of camera permissions and meeting room security parameters.</p>
            </div>

            <div class="space-y-3 bg-brand-dark-secondary/50 border border-brand-teal/15 p-4 rounded-2xl text-xs">
                <div class="flex items-center justify-between text-brand-white">
                    <span>Target Classroom:</span>
                    <span class="font-bold text-brand-cyan" x-text="targetMeetTitle">--</span>
                </div>
                <div class="flex items-center justify-between text-brand-white">
                    <span>Teacher Assigned:</span>
                    <span class="font-bold text-brand-cyan" x-text="targetMeetTeacher">--</span>
                </div>
                <div class="border-t border-brand-teal/10 pt-3 space-y-2 text-[10px] text-brand-gray/80">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Mic &amp; Video Streams Online
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> TLS Meeting Sandbox Initialized
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> User token authenticated: {{ auth()->user()->name }}
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="showPreJoinModal = false" class="flex-1 rounded-xl border border-brand-teal/20 py-3 text-xs font-bold text-brand-gray hover:text-brand-white transition-all cursor-pointer">
                    Dismiss
                </button>
                <a :href="targetMeetUrl" target="_blank" @click="showPreJoinModal = false" class="flex-1 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-black text-brand-dark-secondary shadow text-center hover:opacity-90 active:scale-95 transition-all cursor-pointer block font-sans">
                    🚀 Launch Meet Room
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function liveClassesState() {
    return {
        filter: 'all',
        countdownSec: 59,
        showPreJoinModal: false,
        targetMeetTitle: '',
        targetMeetTeacher: '',
        targetMeetUrl: '',
        init() {
            setInterval(() => {
                this.countdownSec = this.countdownSec > 0 ? this.countdownSec - 1 : 59;
            }, 1000);
        },
        openPreJoinModal(title, teacher, url) {
            this.targetMeetTitle = title;
            this.targetMeetTeacher = teacher;
            this.targetMeetUrl = url;
            this.showPreJoinModal = true;
        }
    };
}
</script>
@endsection
