@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    
    <!-- Top breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('academy.course', $course->slug) }}" class="text-xs text-brand-cyan hover:underline">← Back to Course Overview</a>
        <h1 class="text-2xl font-bold text-brand-white mt-2">{{ $lesson->title }}</h1>
        <p class="text-xs text-brand-gray mt-1">Bootcamp: {{ $course->title }}</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Center/Left: Video Player and Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Video Player -->
            <div class="glass-card rounded-3xl overflow-hidden border border-brand-teal/20 relative">
                <video class="w-full aspect-video bg-black/90 object-cover" controls>
                    <source src="{{ $lesson->video_url }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <!-- Written Lesson Content -->
            <div class="glass-card rounded-3xl p-8 space-y-4">
                <h3 class="text-lg font-bold text-brand-white">Lecture Documentation</h3>
                <div class="text-sm text-brand-gray leading-relaxed space-y-4 border-t border-brand-teal/10 pt-4">
                    <p>{{ $lesson->content }}</p>
                    <p>
                        In this module we explore the core parameters that enable scaling. Be sure to complete the quizzes and consult the AI assistant on the right if you experience any conceptual issues.
                    </p>
                </div>
            </div>

            <!-- Navigation buttons -->
            <div class="flex justify-between items-center">
                <span class="text-xs text-brand-cyan">Course progress saved: {{ $enrollment->progress }}%</span>
                
                @if($nextLesson)
                    <a href="{{ route('academy.lesson', [$course->slug, $nextLesson->slug]) }}" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all">Next Lesson →</a>
                @else
                    <span class="rounded bg-emerald-950 border border-emerald-500/20 px-3 py-1.5 text-xs font-bold text-emerald-400">🎉 Course Completed!</span>
                @endif
            </div>
        </div>

        <!-- Right: AI Tutor Assistant Pane -->
        <div class="space-y-6">
            <!-- AI Tutor Box -->
            <div class="glass-card rounded-3xl p-6 border border-brand-teal/20 flex flex-col h-[500px]" 
                 x-data="aiTutorEngine({ askUrl: '{{ route('academy.ask-ai') }}', lessonId: {{ $lesson->id }} })">
                
                <!-- Chat Header -->
                <div class="border-b border-brand-teal/10 pb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-brand-cyan animate-pulse"></span>
                        <h4 class="text-xs font-bold text-brand-white uppercase tracking-wider">AI Course Tutor</h4>
                    </div>
                    <span class="text-[10px] text-brand-gray">Topic Guide</span>
                </div>

                <!-- Message stream box -->
                <div class="flex-1 overflow-y-auto py-4 space-y-4 scrollbar-thin scrollbar-thumb-brand-teal" id="chatStream">
                    <!-- Initial Tutor msg -->
                    <div class="flex items-start gap-2 max-w-[85%]">
                        <div class="h-6 w-6 rounded bg-brand-teal/20 text-[10px] flex items-center justify-center font-bold text-brand-cyan border border-brand-teal/30">AI</div>
                        <div class="rounded-xl bg-brand-dark-secondary/60 px-3 py-2 text-xs text-brand-gray border border-brand-teal/10">
                            Hello! I am your AI learning assistant for <strong>{{ $lesson->title }}</strong>. Ask me anything about programming design patterns, databases, or cloud deployments!
                        </div>
                    </div>

                    <!-- User and tutor messages templates -->
                    <template x-for="msg in messages">
                        <div class="flex items-start gap-2 max-w-[85%] mt-4" :class="{'ml-auto flex-row-reverse': msg.sender === 'user'}">
                            <div class="h-6 w-6 rounded text-[10px] flex items-center justify-center font-bold border"
                                 :class="{
                                     'bg-brand-teal/20 text-brand-cyan border-brand-teal/30': msg.sender === 'ai',
                                     'bg-brand-cyan/20 text-brand-cyan border-brand-cyan/30': msg.sender === 'user'
                                 }"
                                 x-text="msg.sender === 'user' ? 'Me' : 'AI'">
                            </div>
                            <div class="rounded-xl px-3 py-2 text-xs text-brand-gray border"
                                 :class="{
                                     'bg-brand-dark-secondary/60 border-brand-teal/10': msg.sender === 'ai',
                                     'bg-brand-dark-secondary/90 border-brand-cyan/20': msg.sender === 'user'
                                 }"
                                 x-html="msg.text">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input group -->
                <div class="border-t border-brand-teal/10 pt-4">
                    <form @submit.prevent="sendMessage()" class="flex gap-2">
                        <input type="text" 
                               x-model="questionInput" 
                               placeholder="Ask a clarifying question..." 
                               class="flex-1 rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-3 py-2 text-xs text-brand-white focus:border-brand-cyan focus:outline-none"
                               :disabled="loading">
                        <button type="submit" 
                                class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90"
                                :disabled="loading">
                            Send
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lessons Playlist -->
            <div class="glass-card rounded-2xl p-6">
                <h4 class="text-xs font-semibold text-brand-cyan uppercase tracking-wider mb-4">Course Playlist</h4>
                <div class="space-y-3">
                    @foreach($allLessons as $index => $playLesson)
                        <a href="{{ route('academy.lesson', [$course->slug, $playLesson->slug]) }}" 
                           class="flex items-center justify-between rounded-lg p-2.5 text-xs transition-all border"
                           class="border-brand-teal/10"
                           :class="{
                               'bg-brand-teal/10 border-brand-teal/30 text-brand-cyan': '{{ $playLesson->slug }}' === '{{ $lesson->slug }}',
                               'hover:bg-brand-dark-secondary/60 border-transparent text-brand-gray': '{{ $playLesson->slug }}' !== '{{ $lesson->slug }}'
                           }">
                            <span>{{ $index + 1 }}. {{ $playLesson->title }}</span>
                            <span class="text-[10px] text-brand-gray">⏱ {{ round($playLesson->duration_seconds / 60) }} M</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Alpine AI Tutor Logic -->
<script>
    function aiTutorEngine(config) {
        return {
            questionInput: '',
            messages: [],
            loading: false,

            sendMessage() {
                if (!this.questionInput.trim()) return;

                const userQuestion = this.questionInput;
                this.messages.push({ sender: 'user', text: userQuestion });
                this.questionInput = '';
                this.loading = true;

                this.scrollChat();

                fetch(config.askUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        question: userQuestion,
                        lesson_id: config.lessonId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.messages.push({ sender: 'ai', text: data.answer });
                    this.loading = false;
                    this.scrollChat();
                })
                .catch(() => {
                    this.messages.push({ sender: 'ai', text: 'Sorry, I encountered an issue querying the model database. Please try again.' });
                    this.loading = false;
                    this.scrollChat();
                });
            },

            scrollChat() {
                setTimeout(() => {
                    const stream = document.getElementById('chatStream');
                    if (stream) {
                        stream.scrollTop = stream.scrollHeight;
                    }
                }, 50);
            }
        };
    }
</script>
@endsection
