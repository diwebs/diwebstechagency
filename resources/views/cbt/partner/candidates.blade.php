@extends('layouts.cbt')

@section('title', 'Proctor Supervisor Console - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8" x-data="proctorConsole()">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Proctor Monitoring Terminal</h1>
            <p class="text-sm text-brand-gray mt-1">Supervise candidate webcam flags, secure browser status, and security compliance.</p>
        </div>
        
        <!-- Center details -->
        <div class="flex items-center gap-2 bg-rose-500/10 border border-rose-500/25 px-4 py-2 rounded-xl">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
            </span>
            <span class="text-xs text-rose-400 font-bold">Proctor Feed Active: {{ $center->name }}</span>
        </div>
    </div>

    <!-- Active Candidates Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Live Candidates List -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Active Candidates Monitor</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($sessions->where('status', 'active') as $session)
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/15 space-y-4 hover:border-brand-cyan/35 transition-all">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-bold text-brand-white">{{ $session->user->name }}</h4>
                                <span class="text-[10px] text-brand-gray block">{{ $session->user->email }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-bold text-emerald-400">
                                ACTIVE
                            </span>
                        </div>

                        <div class="text-xs space-y-1.5 border-t border-brand-teal/5 pt-3">
                            <div class="flex justify-between">
                                <span class="text-brand-gray">Exam:</span>
                                <strong class="text-brand-white truncate max-w-[150px]">{{ $session->exam->title }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-brand-gray">Duration:</span>
                                <strong class="text-brand-white font-mono">{{ $session->exam->duration_minutes }} Min</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-brand-gray">Security Flags:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold font-mono {{ $session->anti_cheat_flags > 0 ? 'bg-rose-500/15 text-rose-400 border border-rose-500/20' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' }}">
                                    ⚠️ {{ $session->anti_cheat_flags }} Flags
                                </span>
                            </div>
                        </div>

                        <!-- Flags timeline list -->
                        @if($session->flags->count() > 0)
                            <div class="bg-[#1A1D21] border border-brand-teal/10 rounded-xl p-3 space-y-2 text-[10px]">
                                <span class="text-rose-400 font-bold block">Violation Log:</span>
                                <ul class="space-y-1 divide-y divide-white/5">
                                    @foreach($session->flags as $flag)
                                        <li class="pt-1.5 text-brand-gray">
                                            <span class="text-rose-400 font-bold">[{{ strtoupper(str_replace('_', ' ', $flag->violation_type)) }}]</span>
                                            {{ $flag->details }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="border-t border-brand-teal/5 pt-4 flex gap-3">
                            <button @click="warnCandidate('{{ $session->id }}', '{{ $session->user->name }}')" 
                                    class="flex-1 rounded bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 py-2 text-xs font-bold text-amber-400 transition-all select-none">
                                ⚠️ Warn Candidate
                            </button>
                            <button @click="terminateCandidate('{{ $session->id }}', '{{ $session->user->name }}')"
                                    class="flex-1 rounded bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 py-2 text-xs font-bold text-rose-400 transition-all select-none">
                                🔌 Terminate
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                        No candidate sessions are currently active in this center.
                    </div>
                @endforelse
            </div>
            
            <!-- Historic Sessions -->
            <div class="pt-6">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2 mb-4">Completed / Terminated Sessions Today</h3>
                <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/10">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-brand-dark-secondary/40 text-brand-gray border-b border-brand-teal/10">
                                <th class="p-4 font-bold">Candidate</th>
                                <th class="p-4 font-bold">Exam</th>
                                <th class="p-4 font-bold">Status</th>
                                <th class="p-4 font-bold">Score</th>
                                <th class="p-4 font-bold">Ended At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5">
                            @forelse($sessions->where('status', '!=', 'active') as $histSession)
                                <tr class="hover:bg-brand-teal/5 transition-all text-brand-white">
                                    <td class="p-4 font-bold">{{ $histSession->user->name }}</td>
                                    <td class="p-4 text-brand-gray">{{ $histSession->exam->title }}</td>
                                    <td class="p-4">
                                        @if($histSession->status === 'completed')
                                            <span class="rounded bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-bold text-emerald-400 uppercase">Passed</span>
                                        @elseif($histSession->status === 'void')
                                            <span class="rounded bg-rose-500/10 border border-rose-500/30 px-2 py-0.5 text-[9px] font-bold text-rose-400 uppercase font-mono">Terminated</span>
                                        @else
                                            <span class="rounded bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 text-[9px] font-bold text-amber-400 uppercase font-mono">{{ $histSession->status }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono font-bold">{{ $histSession->score !== null ? $histSession->score . '%' : 'N/A' }}</td>
                                    <td class="p-4 text-brand-gray">{{ $histSession->ended_at ? $histSession->ended_at->format('H:i:s') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-brand-gray">No completed assessments recorded today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Proctoring Live Video Diagnostics Mock -->
        <div class="space-y-6">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Proctor Cam Diagnostics</h3>
            
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <div class="relative rounded-xl overflow-hidden bg-brand-dark-secondary/60 aspect-video flex items-center justify-center border border-brand-teal/10">
                    <!-- CCTV grid mock -->
                    <div class="absolute inset-0 bg-[radial-gradient(#14b8a6_1px,transparent_1px)] [background-size:16px_16px] opacity-10"></div>
                    <div class="absolute top-2 left-2 bg-black/60 text-[9px] font-mono text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">
                        📹 MULTI-STREAM FEED
                    </div>
                    <span class="text-xs text-brand-gray text-center px-4">Webcam multi-stream active. Proctor view consolidates diagnostic telemetry checks.</span>
                </div>
                
                <div class="text-xs space-y-2.5 text-brand-gray">
                    <div class="flex justify-between">
                        <span>Active Stream Channels:</span>
                        <strong class="text-brand-white">3 Channels</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>WebRTC Peer Ping:</span>
                        <strong class="text-emerald-400 font-mono">14ms</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Face Detection Engine:</span>
                        <strong class="text-brand-cyan">ON (TensorFlow)</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Alert Modal / Warning Mock -->
    <div x-show="showWarningModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-[#0A0D10]/80 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom glass-card border border-brand-teal/20 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="p-6 space-y-4">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-amber-400">⚠️ Issue Proctor Warning</h3>
                    <p class="text-xs text-brand-gray">Send an urgent warning prompt to candidate <strong class="text-brand-white" x-text="targetName"></strong>. They must acknowledge the notification to resume typing.</p>
                    
                    <textarea x-model="warningMsg" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/20 text-xs p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" rows="3" placeholder="Webcam verification failed: Please face the terminal directly."></textarea>
                    
                    <div class="flex justify-end gap-3 pt-2">
                        <button @click="showWarningModal = false" class="rounded bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-xs text-brand-gray hover:text-brand-white">Cancel</button>
                        <button @click="submitWarning()" class="rounded bg-amber-500 text-brand-dark-secondary px-4 py-2 text-xs font-bold hover:opacity-90">Send Warning</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function proctorConsole() {
    return {
        showWarningModal: false,
        targetSessionId: null,
        targetName: '',
        warningMsg: 'Webcam verification warning: Please face your terminal directly.',
        warnCandidate(sessionId, name) {
            this.targetSessionId = sessionId;
            this.targetName = name;
            this.showWarningModal = true;
        },
        submitWarning() {
            fetch(`/cbt/partner/candidates/${this.targetSessionId}/warn`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: this.warningMsg })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Proctor warning dispatched successfully!');
                    this.showWarningModal = false;
                }
            });
        },
        terminateCandidate(sessionId, name) {
            if (confirm(`CRITICAL ACTION: Are you sure you want to lock the shell and terminate ${name}'s examination session? This action is immediate and permanent.`)) {
                fetch(`/cbt/partner/candidates/${sessionId}/terminate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert('Session terminated! Candidate shell has been locked.');
                        window.location.reload();
                    }
                });
            }
        }
    }
}
</script>
@endsection
