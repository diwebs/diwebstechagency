@extends('layouts.app')

@section('title', 'CBT Engine - Timed Examination')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" 
     x-data="examEngine({
        durationSeconds: {{ $exam->duration_minutes * 60 }},
        questionsCount: {{ $exam->questions->count() }},
        logUrl: '{{ route('cbt.exam.log-event', $session->id) }}',
        submitUrl: '{{ route('cbt.exam.submit', $session->id) }}'
     })"
     x-init="initEngine()">

    <!-- Top Status Header -->
    <div class="glass-card rounded-2xl p-6 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-brand-white">{{ $exam->title }}</h1>
            <p class="text-xs text-brand-gray mt-1">Status: <strong class="text-brand-cyan uppercase">Active Secure Session</strong></p>
        </div>
        <!-- Timer display -->
        <div class="flex items-center gap-4">
            <div class="rounded-xl border border-brand-teal/20 bg-brand-dark-secondary px-6 py-3 text-center min-w-[120px]">
                <span class="block text-[10px] text-brand-cyan font-bold uppercase tracking-wider">Remaining Time</span>
                <span class="text-2xl font-mono font-bold text-brand-white" x-text="formatTime(timeRemaining)">00:00</span>
            </div>
            <!-- Alert Violation Badge -->
            <div class="rounded-xl border border-rose-500/20 bg-brand-dark-secondary px-6 py-3 text-center min-w-[120px]">
                <span class="block text-[10px] text-rose-400 font-bold uppercase tracking-wider">Security Flags</span>
                <span class="text-2xl font-mono font-bold text-rose-400" x-text="violations">0</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left: Question Area -->
        <div class="lg:col-span-3 space-y-6">
            <form id="examForm" action="{{ route('cbt.exam.submit', $session->id) }}" method="POST">
                @csrf
                
                @foreach($exam->questions as $index => $question)
                    <div x-show="currentQ === {{ $index }}" class="glass-card rounded-3xl p-8 space-y-6" x-transition>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold text-brand-cyan uppercase">Question {{ $index + 1 }} of {{ $exam->questions->count() }}</span>
                            <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[10px] text-brand-cyan capitalize">{{ $question->difficulty }}</span>
                        </div>

                        <h3 class="text-lg font-bold text-brand-white font-sans">
                            {{ $question->question_text }}
                        </h3>

                        <!-- Options selection -->
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center gap-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/40 p-4 cursor-pointer hover:border-brand-cyan/40 hover:bg-brand-dark-secondary/80 transition-all">
                                    @if($question->question_type === 'single_choice')
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $option['id'] }}" 
                                               class="h-4 w-4 border-brand-teal/30 text-brand-cyan focus:ring-brand-cyan bg-brand-dark-secondary"
                                               x-model="answers['{{ $question->id }}']">
                                    @else
                                        <input type="checkbox" 
                                               name="answers[{{ $question->id }}][]" 
                                               value="{{ $option['id'] }}" 
                                               class="h-4 w-4 rounded border-brand-teal/30 text-brand-cyan focus:ring-brand-cyan bg-brand-dark-secondary"
                                               @change="toggleCheckbox('{{ $question->id }}', '{{ $option['id'] }}')">
                                    @endif
                                    <span class="text-sm text-brand-gray">{{ $option['id'] }}. {{ $option['text'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Question Navigation Buttons -->
                <div class="mt-6 flex justify-between">
                    <button type="button" 
                            @click="prevQuestion()" 
                            class="rounded-md bg-brand-dark-secondary/60 hover:bg-brand-dark-secondary border border-brand-teal/20 px-6 py-2.5 text-sm font-semibold text-brand-white transition-all"
                            :disabled="currentQ === 0"
                            :class="{'opacity-50 pointer-events-none': currentQ === 0}">
                        Previous
                    </button>
                    
                    <button type="button" 
                            x-show="currentQ < questionsCount - 1"
                            @click="nextQuestion()" 
                            class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-sm font-semibold text-brand-dark-secondary transition-all">
                        Next Question
                    </button>

                    <button type="button" 
                            x-show="currentQ === questionsCount - 1"
                            @click="submitExamPrompt()"
                            class="rounded-md bg-emerald-500 hover:bg-emerald-600 px-6 py-2.5 text-sm font-bold text-brand-dark-secondary transition-all cursor-pointer">
                        Submit Examination
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webcam Box and Navigator -->
        <div class="space-y-6">
            <!-- Simulated Webcam Monitoring Box -->
            <div class="glass-card rounded-2xl p-6 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-dot-matrix opacity-20"></div>
                <h4 class="text-xs font-semibold text-brand-cyan uppercase tracking-wider mb-4">Webcam Feed</h4>
                <!-- Cam stream view box -->
                <div class="mx-auto w-full aspect-video rounded-xl border border-brand-teal/20 bg-brand-dark-secondary/80 flex flex-col items-center justify-center relative">
                    <span class="absolute top-2 left-2 flex items-center gap-1.5 px-2 py-0.5 rounded bg-brand-dark-secondary/80 text-[10px] text-rose-400 font-semibold border border-rose-500/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500 animate-ping"></span> Live Rec
                    </span>
                    <span class="text-3xl">👤</span>
                    <p class="text-[10px] text-brand-gray mt-2">Candidate: {{ auth()->user()->name }}</p>
                </div>
            </div>

            <!-- Question Numbers Navigator Grid -->
            <div class="glass-card rounded-2xl p-6">
                <h4 class="text-xs font-semibold text-brand-cyan uppercase tracking-wider mb-4">Assessment Navigator</h4>
                <div class="grid grid-cols-5 gap-2">
                    <template x-for="i in Array.from({ length: questionsCount }, (_, i) => i)">
                        <button type="button"
                                @click="currentQ = i"
                                class="h-8 rounded text-xs font-bold transition-all"
                                :class="{
                                    'bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary': currentQ === i,
                                    'border border-brand-teal/20 bg-brand-dark-secondary/60 text-brand-gray': currentQ !== i
                                }"
                                x-text="i + 1">
                        </button>
                    </template>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Alpine Exam Logic -->
<script>
    function examEngine(config) {
        return {
            currentQ: 0,
            questionsCount: config.questionsCount,
            timeRemaining: config.durationSeconds,
            violations: 0,
            answers: {},
            
            initEngine() {
                // Timer Loop
                setInterval(() => {
                    if (this.timeRemaining > 0) {
                        this.timeRemaining--;
                    } else {
                        this.submitExamAuto();
                    }
                }, 1000);

                // Anti-Cheat: Focus Visiblity Check
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        this.logViolation('tab_switch', 'Candidate lost browser focus.');
                    }
                });

                // Anti-Cheat: Fullscreen monitoring
                window.addEventListener('resize', () => {
                    const isFull = window.innerHeight === screen.height;
                    if (!isFull) {
                        // Log event but don't void instantly
                        this.logViolation('fullscreen_exit', 'Candidate left fullscreen mode.');
                    }
                });
            },

            formatTime(seconds) {
                const min = Math.floor(seconds / 60);
                const sec = seconds % 60;
                return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
            },

            nextQuestion() {
                if (this.currentQ < this.questionsCount - 1) {
                    this.currentQ++;
                }
            },

            prevQuestion() {
                if (this.currentQ > 0) {
                    this.currentQ--;
                }
            },

            toggleCheckbox(qId, val) {
                if (!this.answers[qId]) {
                    this.answers[qId] = [];
                }
                const index = this.answers[qId].indexOf(val);
                if (index > -1) {
                    this.answers[qId].splice(index, 1);
                } else {
                    this.answers[qId].push(val);
                }
            },

            logViolation(type, msg) {
                fetch(config.logUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        event_type: type,
                        details: { message: msg },
                        answers: this.answers
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'terminated') {
                        alert('CRITICAL SECURITY ALERT: Exam session terminated immediately due to multiple visual violations.');
                        window.location.href = '{{ route("cbt.dashboard") }}';
                    } else {
                        this.violations = data.flags;
                        alert('WARNING: Anti-cheat trigger: "' + msg + '" (Violation count: ' + data.flags + '/5). Focus loss or window swapping will void the examination.');
                    }
                });
            },

            submitExamPrompt() {
                if (confirm('Are you sure you want to finish and submit the assessment?')) {
                    document.getElementById('examForm').submit();
                }
            },

            submitExamAuto() {
                alert('Time expired. Submitting exam...');
                document.getElementById('examForm').submit();
            }
        };
    }
</script>
@endsection
