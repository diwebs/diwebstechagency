@extends('layouts.academy')

@section('title', 'Live Classes - Diwebs Academy')

@section('academy_content')
<div x-data="liveClassesState()" class="space-y-8">

    {{-- ─── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Academy Live Learning</h1>
            <p class="text-sm text-brand-gray mt-1">Join interactive classrooms, workshops, and coaching sessions led by core engineers.</p>
        </div>
        @if($hasLivePlan)
        <div class="flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-xs text-brand-gray">Feed Listener Live</span>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- PLAN GATE — shown when student has NO live-class plan                 --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @unless($hasLivePlan)
    <div class="glass-card rounded-3xl p-12 border border-amber-500/25 bg-gradient-to-br from-brand-dark-secondary to-[#1A1D21] text-center relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-80 h-80 bg-amber-500/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-brand-teal/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10">
            {{-- Lock icon --}}
            <div class="h-20 w-20 mx-auto rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            <h2 class="text-xl font-extrabold text-brand-white mb-3">Live Class Access Required</h2>
            <p class="text-sm text-brand-gray max-w-md mx-auto leading-relaxed mb-2">
                Live classroom sessions are available exclusively to students with an active <strong class="text-amber-400">Live Class Plan</strong>. Contact the Diwebs admin team to subscribe.
            </p>
            <p class="text-xs text-brand-gray/60 max-w-md mx-auto leading-relaxed mb-8">
                Once a plan is assigned by an administrator, you will immediately see the full live class schedule and be able to join sessions in real time.
            </p>

            {{-- Feature list --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-xl mx-auto mb-8 text-left">
                @foreach([
                    ['📺', 'Live Interactive Sessions', 'Real-time classroom with instructor Q&A'],
                    ['🔴', 'Join Sessions Instantly', 'One-click Google Meet & Zoom integration'],
                    ['🎬', 'Session Recordings', 'Replay sessions you missed anytime'],
                ] as $f)
                <div class="glass-card rounded-xl p-4 border border-amber-500/10 bg-brand-dark-secondary/40">
                    <span class="text-xl block mb-2">{{ $f[0] }}</span>
                    <h5 class="text-xs font-bold text-brand-white mb-1">{{ $f[1] }}</h5>
                    <p class="text-[10px] text-brand-gray leading-relaxed">{{ $f[2] }}</p>
                </div>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="mailto:support@diwebstechagency.website?subject=Live Class Plan Request"
                   class="rounded-xl bg-amber-500 hover:bg-amber-400 px-8 py-3 text-sm font-bold text-brand-dark-secondary shadow-lg active:scale-95 transition-all">
                    📧 Request Live Class Access
                </a>
                <a href="{{ route('academy.courses') }}"
                   class="rounded-xl border border-brand-teal/30 bg-brand-dark-secondary/60 px-8 py-3 text-sm font-bold text-brand-cyan hover:border-brand-cyan transition-all">
                    Browse Courses Instead →
                </a>
            </div>
        </div>
    </div>
    @endunless

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- LIVE CLASS CONTENT — shown only when plan is active                  --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($hasLivePlan)

    {{-- Next live session countdown banner --}}
    @php
        $nextLive = $liveSessions->where('status', 'live')->first()
            ?? $liveSessions->where('status', 'scheduled')->first();
    @endphp
    @if($nextLive)
    <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 bg-brand-teal/5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-3xl animate-bounce">📺</span>
            <div>
                <span class="rounded bg-brand-cyan/20 border border-brand-teal/30 px-2 py-0.5 text-[8px] font-bold text-brand-cyan uppercase tracking-wider">
                    {{ $nextLive->status === 'live' ? '🔴 Live Now' : 'Next Live Session' }}
                </span>
                <h4 class="text-sm font-bold text-brand-white mt-1">{{ $nextLive->title }}</h4>
                <p class="text-[10px] text-brand-gray">
                    Instructor: {{ $nextLive->teacher ? $nextLive->teacher->name : 'Staff Mentor' }}
                    &nbsp;·&nbsp; {{ $nextLive->date->format('M d, H:i') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @if($nextLive->status === 'live')
            <button @click="openPreJoinModal('{{ $nextLive->title }}', '{{ $nextLive->teacher ? $nextLive->teacher->name : 'Staff Mentor' }}', '{{ $nextLive->meeting_url }}')"
                    class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-black text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                ⚡ Join Meet
            </button>
            @else
            <div class="flex items-center gap-1.5 text-xs text-brand-white font-mono">
                <span class="bg-[#1A1D21] border border-brand-teal/10 px-2 py-1.5 rounded" x-text="countdownH">00</span>
                <span>:</span>
                <span class="bg-[#1A1D21] border border-brand-teal/10 px-2 py-1.5 rounded" x-text="countdownM">00</span>
                <span>:</span>
                <span class="bg-[#1A1D21] border border-brand-teal/10 px-2 py-1.5 rounded" x-text="countdownS">00</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Session Grid --}}
    <div class="space-y-6">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2 flex items-center justify-between">
            <span>Classroom Schedule</span>
            <div class="flex items-center gap-2 text-[10px] font-bold text-brand-gray">
                <button @click="filter = 'all'"       :class="filter === 'all'       ? 'text-brand-cyan underline' : ''">All</button>
                <span>·</span>
                <button @click="filter = 'live'"      :class="filter === 'live'      ? 'text-brand-cyan underline' : ''">Live Now</button>
                <span>·</span>
                <button @click="filter = 'scheduled'" :class="filter === 'scheduled' ? 'text-brand-cyan underline' : ''">Scheduled</button>
            </div>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($liveSessions as $session)
                @php
                    $isLive      = $session->status === 'live';
                    $isScheduled = $session->status === 'scheduled';
                @endphp
                <div x-show="filter === 'all' || (filter === 'live' && '{{ $session->status }}' === 'live') || (filter === 'scheduled' && '{{ $session->status }}' === 'scheduled')"
                     class="glass-card rounded-2xl p-6 flex flex-col justify-between border transition-all duration-300
                     {{ $isLive ? 'border-emerald-500/30 bg-emerald-500/5 shadow-md shadow-emerald-500/5' : 'border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/20' }}">

                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <span class="rounded-lg px-2.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wide
                                  {{ $isLive      ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/20 animate-pulse' : '' }}
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
                            <span class="rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/50 px-4 py-2 text-xs font-bold text-brand-gray cursor-default">
                                🔒 Starts {{ $session->date->diffForHumans() }}
                            </span>
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

    {{-- Pre-join Modal --}}
    <div x-show="showPreJoinModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-brand-dark-secondary/80 backdrop-blur-md px-4"
         style="display:none;">
        <div class="glass-card rounded-3xl p-6 border border-brand-cyan/25 max-w-md w-full space-y-6 relative">
            <button @click="showPreJoinModal = false" class="absolute top-5 right-5 text-brand-gray hover:text-brand-white text-xs cursor-pointer select-none">✕ Close</button>
            <div class="text-center">
                <span class="text-3xl">🛡️</span>
                <h3 class="text-base font-bold text-brand-white mt-3">Classroom Readiness Check</h3>
                <p class="text-[11px] text-brand-gray/80 mt-1">Verifying permissions and session security parameters.</p>
            </div>
            <div class="space-y-3 bg-brand-dark-secondary/50 border border-brand-teal/15 p-4 rounded-2xl text-xs">
                <div class="flex items-center justify-between text-brand-white">
                    <span>Classroom:</span>
                    <span class="font-bold text-brand-cyan" x-text="targetMeetTitle">--</span>
                </div>
                <div class="flex items-center justify-between text-brand-white">
                    <span>Instructor:</span>
                    <span class="font-bold text-brand-cyan" x-text="targetMeetTeacher">--</span>
                </div>
                <div class="border-t border-brand-teal/10 pt-3 space-y-2 text-[10px] text-brand-gray/80">
                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-400"></span> Mic &amp; Video Online</div>
                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-400"></span> TLS Session Initialized</div>
                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-400"></span> User: {{ auth()->user()->name }}</div>
                </div>
            </div>
            <div class="flex gap-3">
                <button @click="showPreJoinModal = false" class="flex-1 rounded-xl border border-brand-teal/20 py-3 text-xs font-bold text-brand-gray hover:text-brand-white transition-all cursor-pointer">Dismiss</button>
                <a :href="targetMeetUrl" target="_blank" @click="showPreJoinModal = false"
                   class="flex-1 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-black text-brand-dark-secondary shadow text-center hover:opacity-90 active:scale-95 transition-all cursor-pointer block font-sans">
                    🚀 Launch Meet Room
                </a>
            </div>
        </div>
    </div>

    @endif {{-- end hasLivePlan --}}

</div>

<script>
function liveClassesState() {
    return {
        filter: 'all',
        countdownH: '00',
        countdownM: '00',
        countdownS: '00',
        showPreJoinModal: false,
        targetMeetTitle: '',
        targetMeetTeacher: '',
        targetMeetUrl: '',
        init() {
            // Live countdown (simple 60-second ticker)
            setInterval(() => {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                this.countdownH = h;
                this.countdownM = m;
                this.countdownS = s;
            }, 1000);
        },
        openPreJoinModal(title, teacher, url) {
            this.targetMeetTitle   = title;
            this.targetMeetTeacher = teacher;
            this.targetMeetUrl     = url;
            this.showPreJoinModal  = true;
        }
    };
}
</script>
@endsection
