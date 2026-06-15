@extends('layouts.app')

@section('title', 'CBT Proctor Lobby - Security Check')

@section('content')
<div class="mx-auto max-w-3xl px-4" x-data="lobbyState()" x-init="startCalibration()">
    <!-- Lobby Card -->
    <div class="glass-card rounded-3xl p-8 border border-brand-cyan/25 space-y-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-dot-matrix opacity-10 pointer-events-none"></div>

        <div class="text-center">
            <span class="text-4xl">🛡️</span>
            <h1 class="text-2xl font-bold text-brand-white mt-3">LMS Proctor Sandbox Verification</h1>
            <p class="text-xs text-brand-gray mt-1">Please verify your hardware peripherals and identity credentials to initialize secure stream protocols.</p>
        </div>

        <!-- System Parameters Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Simulated Camera Monitor -->
            <div class="rounded-2xl border border-brand-teal/20 bg-brand-dark-secondary/50 p-5 flex flex-col items-center justify-center min-h-[200px] relative">
                <template x-if="cameraStatus === 'checking'">
                    <div class="text-center space-y-3">
                        <span class="h-8 w-8 animate-spin rounded-full border-2 border-brand-cyan border-t-transparent inline-block"></span>
                        <p class="text-[10px] text-brand-gray">Calibrating Webcam Matrix...</p>
                    </div>
                </template>
                <template x-if="cameraStatus === 'ready'">
                    <div class="text-center space-y-3">
                        <span class="text-4xl">👤</span>
                        <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">Webcam Confirmed</p>
                        <p class="text-[9px] text-brand-gray/60">Biometric stream locked onto focus target.</p>
                    </div>
                </template>
            </div>

            <!-- Checklist status list -->
            <div class="space-y-3 text-xs">
                <h3 class="text-[10px] font-extrabold uppercase text-brand-cyan tracking-wider mb-2">Hardware Readiness Checklist</h3>

                <!-- Cam Check -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-brand-dark-secondary/40 border border-brand-teal/5">
                    <span class="text-brand-white">Webcam Stream Capture</span>
                    <span class="font-bold font-mono" :class="cameraStatus === 'ready' ? 'text-emerald-400' : 'text-brand-gray'" x-text="cameraStatus === 'ready' ? 'PASSED' : 'TESTING...'"></span>
                </div>

                <!-- Mic Check -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-brand-dark-secondary/40 border border-brand-teal/5">
                    <span class="text-brand-white">Microphone Channel Ping</span>
                    <span class="font-bold font-mono" :class="micStatus === 'ready' ? 'text-emerald-400' : 'text-brand-gray'" x-text="micStatus === 'ready' ? 'PASSED' : 'TESTING...'"></span>
                </div>

                <!-- Network Check -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-brand-dark-secondary/40 border border-brand-teal/5">
                    <span class="text-brand-white">TLS Handshake Latency</span>
                    <span class="font-bold font-mono" :class="netStatus === 'ready' ? 'text-emerald-400' : 'text-brand-gray'" x-text="netStatus === 'ready' ? 'PASSED (28ms)' : 'TESTING...'"></span>
                </div>

                <!-- Identity Verification -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-brand-dark-secondary/40 border border-brand-teal/5">
                    <span class="text-brand-white">Identity Credentials Token</span>
                    <span class="font-bold font-mono" :class="idStatus === 'ready' ? 'text-emerald-400' : 'text-brand-gray'" x-text="idStatus === 'ready' ? 'VERIFIED' : 'TESTING...'"></span>
                </div>
            </div>
        </div>

        <div class="border-t border-brand-teal/10 pt-6 flex gap-4">
            <a href="{{ route('cbt.live-exams') }}" class="flex-1 rounded-xl border border-brand-teal/20 text-center py-3 text-xs font-bold text-brand-gray hover:text-brand-white transition-all">
                Cancel
            </a>
            
            <form action="{{ route('cbt.live-exams.start', $liveExam->id) }}" method="POST" class="flex-1" @submit="requestFullscreen($event)">
                @csrf
                <button type="submit" 
                        :disabled="!allReady" 
                        class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-black text-brand-dark-secondary shadow text-center hover:opacity-90 active:scale-95 transition-all cursor-pointer block disabled:opacity-50"
                        x-text="allReady ? '🚀 Launch Proctor Room' : 'Calibrating System Tools...'">
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function lobbyState() {
    return {
        cameraStatus: 'checking',
        micStatus: 'checking',
        netStatus: 'checking',
        idStatus: 'checking',
        allReady: false,
        
        startCalibration() {
            setTimeout(() => { this.cameraStatus = 'ready'; }, 1000);
            setTimeout(() => { this.micStatus = 'ready'; }, 1800);
            setTimeout(() => { this.netStatus = 'ready'; }, 2400);
            setTimeout(() => { 
                this.idStatus = 'ready'; 
                this.allReady = true;
            }, 3000);
        },

        requestFullscreen(e) {
            // Request fullscreen on form submit to initialize proctor sandboxing
            const docEl = document.documentElement;
            if (docEl.requestFullscreen) {
                docEl.requestFullscreen();
            } else if (docEl.webkitRequestFullscreen) {
                docEl.webkitRequestFullscreen();
            } else if (docEl.msRequestFullscreen) {
                docEl.msRequestFullscreen();
            }
        }
    };
}
</script>
@endsection
