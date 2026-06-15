@extends('layouts.academy')

@section('title', 'Audio Learning Portal - Learn on the Go')

@section('academy_content')
<div x-data="audioPlayerState()" class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Audio Learning System</h1>
            <p class="text-sm text-brand-gray mt-1">Listen to lecture summaries, tech podcasts, and course briefs on the go.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-purple-400 animate-pulse"></span>
            <span class="text-xs text-brand-gray">Offline Mode Syncable</span>
        </div>
    </div>

    <!-- Active Premium Audio Player Interface -->
    <div class="glass-card rounded-3xl p-6 border border-brand-teal/20 bg-gradient-to-b from-brand-dark-secondary to-[#1A1D21] relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-cyan/5 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-teal/5 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
            <!-- Disk wrapper / thumbnail visual -->
            <div class="lg:col-span-4 flex flex-col items-center">
                <div class="relative h-44 w-44 rounded-full border border-brand-teal/30 bg-[#25282D] flex items-center justify-center p-3 shadow-2xl shadow-brand-teal/5 overflow-hidden animate-pulse-slow" :class="isPlaying ? 'animate-spin-slow' : ''">
                    <div class="h-full w-full rounded-full bg-brand-dark-secondary border border-brand-teal/10 flex items-center justify-center relative">
                        <span class="text-4xl text-brand-cyan">🎧</span>
                        <div class="absolute h-10 w-10 bg-brand-dark-secondary border border-brand-teal/20 rounded-full shadow-inner flex items-center justify-center">
                            <span class="h-3.5 w-3.5 rounded-full bg-brand-cyan"></span>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-5">
                    <span class="rounded-full bg-brand-teal/10 px-3 py-0.5 text-[9px] font-bold text-brand-cyan border border-brand-teal/20 uppercase">
                        Active Stream
                    </span>
                </div>
            </div>

            <!-- Player Controls -->
            <div class="lg:col-span-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-brand-white" x-text="currentTrack.title">Select a track below</h3>
                    <p class="text-xs text-brand-gray mt-1">Instructor: <span class="text-brand-cyan" x-text="currentTrack.instructor">--</span></p>
                </div>

                <!-- Hidden audio tag -->
                <audio x-ref="audioEl" 
                       :src="currentTrack.url" 
                       @timeupdate="updateProgress" 
                       @loadedmetadata="onMetadataLoaded" 
                       @ended="onEnded"></audio>

                <!-- Scrubber Progress Bar -->
                <div>
                    <div class="relative w-full h-1.5 bg-brand-dark-secondary rounded-full border border-brand-teal/15 cursor-pointer overflow-hidden" @click="scrub">
                        <div class="absolute top-0 bottom-0 left-0 bg-gradient-to-r from-brand-teal to-brand-cyan rounded-full" :style="{ width: progressPercent + '%' }"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-brand-gray/60 mt-1.5 font-mono">
                        <span x-text="formatTime(currentTime)">0:00</span>
                        <span x-text="formatTime(duration)">0:00</span>
                    </div>
                </div>

                <!-- Action Button Matrix -->
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <!-- Speed Toggle -->
                        <div class="relative">
                            <button @click="showSpeedDropdown = !showSpeedDropdown" class="text-xs font-bold text-brand-cyan px-2.5 py-1.5 rounded-lg border border-brand-teal/15 bg-brand-dark-secondary/50 hover:bg-brand-teal/10 transition-all flex items-center gap-1.5 cursor-pointer">
                                ⚡ Speed: <span x-text="playbackRate + 'x'">1x</span>
                            </button>
                            <div x-show="showSpeedDropdown" @click.away="showSpeedDropdown = false" class="absolute bottom-10 left-0 z-20 w-24 bg-brand-dark-secondary border border-brand-teal/20 rounded-xl p-1 shadow-xl text-xs space-y-0.5">
                                <template x-for="rate in [0.75, 1.0, 1.25, 1.5, 2.0]">
                                    <button @click="setSpeed(rate)" class="w-full text-left px-2 py-1.5 rounded hover:bg-brand-teal/10 text-brand-gray hover:text-brand-white transition-colors" x-text="rate + 'x'"></button>
                                </template>
                            </div>
                        </div>

                        <!-- Chapters Panel toggle -->
                        <button @click="tab = tab === 'chapters' ? 'transcript' : 'chapters'" class="text-xs font-bold text-brand-gray hover:text-brand-white transition-colors">
                            📚 Chapters
                        </button>
                    </div>

                    <!-- Audio Media Controllers -->
                    <div class="flex items-center gap-6">
                        <!-- Skip back 10s -->
                        <button @click="skip(-10)" class="text-brand-cyan hover:text-brand-white text-lg transition-colors cursor-pointer select-none">
                            ⏪ 10s
                        </button>

                        <!-- Big Play/Pause Button -->
                        <button @click="togglePlay" class="h-14 w-14 rounded-full bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-black shadow flex items-center justify-center hover:opacity-90 active:scale-95 transition-all cursor-pointer select-none">
                            <span x-text="isPlaying ? '⏸' : '▶'" class="text-xl">▶</span>
                        </button>

                        <!-- Skip forward 10s -->
                        <button @click="skip(10)" class="text-brand-cyan hover:text-brand-white text-lg transition-colors cursor-pointer select-none">
                            10s ⏩
                        </button>
                    </div>

                    <div>
                        <!-- Download Offline Toggle -->
                        <button @click="toggleDownload" class="p-2 rounded-lg border text-xs cursor-pointer transition-all"
                                :class="currentTrack.isDownloaded ? 'border-emerald-500/30 text-emerald-400 bg-emerald-500/5' : 'border-brand-teal/20 text-brand-gray hover:text-brand-cyan'">
                            <span x-text="currentTrack.isDownloaded ? '✓ Offline Sync' : '📥 Get Offline'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Tab section: Summary vs Chapters vs Transcript -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Panel: Playlists Selector -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-4 border-b border-brand-teal/10 pb-2">Course Lectures Library</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                @forelse($audioLessons as $lesson)
                    <div class="rounded-xl border p-3 flex items-center justify-between text-xs cursor-pointer transition-all duration-200"
                         :class="currentTrack.id === {{ $lesson->id }} ? 'border-brand-cyan bg-brand-teal/5 text-brand-white font-semibold' : 'border-brand-teal/5 bg-brand-dark-secondary/20 hover:border-brand-teal/20 hover:bg-brand-dark-secondary/30 text-brand-gray'"
                         @click="selectTrack({{ json_encode($lesson) }})">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg">🎵</span>
                            <div>
                                <h4 class="font-bold text-brand-white line-clamp-1">{{ $lesson->title }}</h4>
                                <span class="text-[10px] text-brand-gray/60">{{ $lesson->instructor_name }} · {{ $lesson->format }}</span>
                            </div>
                        </div>
                        <span class="font-mono text-[10px] text-brand-gray/60">{{ floor($lesson->duration_seconds / 60) }}:{{ sprintf('%02d', $lesson->duration_seconds % 60) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No audio lessons uploaded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Panels: Interactive Tab Workspace -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15">
            <div class="flex items-center gap-4 border-b border-brand-teal/10 pb-3 mb-4 text-xs font-bold text-brand-gray">
                <button @click="tab = 'summary'" :class="tab === 'summary' ? 'text-brand-cyan border-b-2 border-brand-cyan pb-3 -mb-3.5' : 'hover:text-brand-white'">AI Summary</button>
                <button @click="tab = 'transcript'" :class="tab === 'transcript' ? 'text-brand-cyan border-b-2 border-brand-cyan pb-3 -mb-3.5' : 'hover:text-brand-white'">Transcript</button>
                <button @click="tab = 'chapters'" :class="tab === 'chapters' ? 'text-brand-cyan border-b-2 border-brand-cyan pb-3 -mb-3.5' : 'hover:text-brand-white'">Chapters</button>
            </div>

            <!-- Tab Content summary -->
            <div x-show="tab === 'summary'" class="space-y-4 text-xs leading-relaxed text-brand-gray">
                <div class="bg-brand-dark-secondary/40 p-4 rounded-xl border border-brand-teal/5">
                    <h4 class="font-bold text-brand-white mb-2 uppercase tracking-wide text-[10px] text-brand-cyan">🧠 AI Summary Brief</h4>
                    <p x-text="currentTrack.summary || 'Select a lecture to view its summary summary.'"></p>
                </div>
                <div class="flex justify-end">
                    <button @click="generateNewAiSummary" :disabled="loadingSummary" class="rounded border border-brand-teal/20 px-3.5 py-1.5 font-bold hover:bg-brand-teal/10 hover:border-brand-teal text-brand-cyan flex items-center gap-1.5 transition-all cursor-pointer disabled:opacity-50 font-sans">
                        <span x-show="!loadingSummary">🤖 Summarize Audio</span>
                        <span x-show="loadingSummary" style="display:none;">AI Computing...</span>
                    </button>
                </div>
            </div>

            <!-- Tab Content transcript -->
            <div x-show="tab === 'transcript'" class="max-h-56 overflow-y-auto text-xs leading-relaxed text-brand-gray whitespace-pre-wrap pr-2" x-text="currentTrack.transcript || 'No transcript text available for this track.'"></div>

            <!-- Tab Content chapters -->
            <div x-show="tab === 'chapters'" class="space-y-2 max-h-56 overflow-y-auto pr-2">
                <template x-if="!currentTrack.chapters || currentTrack.chapters.length === 0">
                    <p class="text-xs text-brand-gray">No chapter indices mapped.</p>
                </template>
                <template x-for="ch in currentTrack.chapters">
                    <div class="flex justify-between items-center bg-brand-dark-secondary/30 border border-brand-teal/5 p-2.5 rounded-lg hover:border-brand-teal/20 transition-all cursor-pointer text-xs" @click="seekTo(ch.time)">
                        <span class="font-semibold text-brand-white" x-text="ch.title"></span>
                        <span class="font-mono text-brand-cyan" x-text="formatTime(ch.time)"></span>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>

<script>
function audioPlayerState() {
    return {
        isPlaying: false,
        currentTime: 0,
        duration: 0,
        progressPercent: 0,
        playbackRate: 1.0,
        showSpeedDropdown: false,
        tab: 'summary',
        loadingSummary: false,
        currentTrack: {
            id: null,
            title: 'Welcome to Diwebs Audio',
            instructor: 'Select track',
            url: '',
            summary: 'Click one of the audios on the left playlist library panel to stream its lessons and start AI synthesis.',
            transcript: '',
            chapters: [],
            isDownloaded: false
        },
        
        selectTrack(track) {
            this.currentTrack = {
                id: track.id,
                title: track.title,
                instructor: track.instructor_name,
                url: track.audio_url,
                summary: track.summary,
                transcript: track.transcript,
                chapters: Array.isArray(track.chapters) ? track.chapters : JSON.parse(track.chapters || '[]'),
                isDownloaded: localStorage.getItem('downloaded_track_' + track.id) === 'true'
            };
            this.$nextTick(() => {
                const el = this.$refs.audioEl;
                el.load();
                this.isPlaying = false;
                this.togglePlay();
            });
        },

        togglePlay() {
            const el = this.$refs.audioEl;
            if (!el.src) return;
            if (this.isPlaying) {
                el.pause();
                this.isPlaying = false;
            } else {
                el.play().then(() => {
                    this.isPlaying = true;
                }).catch(e => {
                    console.log('Playback blocked by browser action.');
                });
            }
        },

        skip(secs) {
            const el = this.$refs.audioEl;
            el.currentTime = Math.max(0, Math.min(el.duration, el.currentTime + secs));
        },

        setSpeed(rate) {
            this.playbackRate = rate;
            this.$refs.audioEl.playbackRate = rate;
            this.showSpeedDropdown = false;
        },

        seekTo(secs) {
            this.$refs.audioEl.currentTime = secs;
        },

        toggleDownload() {
            if (!this.currentTrack.id) return;
            const key = 'downloaded_track_' + this.currentTrack.id;
            const current = localStorage.getItem(key) === 'true';
            localStorage.setItem(key, !current ? 'true' : 'false');
            this.currentTrack.isDownloaded = !current;
        },

        updateProgress() {
            const el = this.$refs.audioEl;
            this.currentTime = el.currentTime;
            this.progressPercent = el.duration > 0 ? (el.currentTime / el.duration) * 100 : 0;
        },

        onMetadataLoaded() {
            this.duration = this.$refs.audioEl.duration;
        },

        onEnded() {
            this.isPlaying = false;
            this.progressPercent = 0;
            this.currentTime = 0;
        },

        scrub(e) {
            const el = this.$refs.audioEl;
            if (!el.duration) return;
            const rect = e.currentTarget.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percent = clickX / rect.width;
            el.currentTime = el.duration * percent;
        },

        formatTime(secs) {
            if (isNaN(secs)) return '0:00';
            const m = Math.floor(secs / 60);
            const s = Math.floor(secs % 60);
            return m + ':' + (s < 10 ? '0' : '') + s;
        },

        generateNewAiSummary() {
            if (!this.currentTrack.id) return;
            this.loadingSummary = true;
            setTimeout(() => {
                this.currentTrack.summary = "### AI Live Lecture Core Takeaways\n\n" +
                    "- **Key Topic:** Scaling hybrid Laravel database nodes.\n" +
                    "- **Best Practice:** Keep connections pool-isolated and use database transactions for critical ledger entries.\n" +
                    "- **Summary:** Designed for high efficiency in offline mobile modes.";
                this.loadingSummary = false;
            }, 1200);
        }
    };
}
</script>
