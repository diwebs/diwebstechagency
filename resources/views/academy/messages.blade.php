@extends('layouts.academy')

@section('title', 'Academy Messages - Diwebs Academy')

@section('academy_content')
<div x-data="messagesState()" class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden flex flex-col md:flex-row h-[550px]">
    
    <!-- Left Sidebar: Active Mentors -->
    <div class="w-full md:w-64 border-b md:border-b-0 md:border-r border-brand-teal/10 flex flex-col">
        <div class="p-4 border-b border-brand-teal/10 bg-brand-dark-secondary/20">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Instructor Chat</h3>
            <span class="text-[9px] text-brand-gray mt-1 block">Click a mentor to message</span>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-brand-teal/5">
            <template x-for="t in teachers">
                <div class="p-3.5 flex items-center gap-3 cursor-pointer hover:bg-brand-teal/5 transition-all"
                     :class="activeTeacherId == t.id ? 'bg-brand-teal/5 border-l-2 border-brand-cyan' : ''"
                     @click="selectTeacher(t)">
                    <div class="relative h-9 w-9 rounded-lg bg-brand-dark-secondary border border-brand-teal/10 flex items-center justify-center text-lg">
                        👨‍💻
                        <span class="absolute -bottom-1 -right-1 h-2.5 w-2.5 rounded-full border-2 border-[#1E2125] bg-emerald-400"></span>
                    </div>
                    <div class="leading-tight">
                        <span class="block text-xs font-bold text-brand-white" x-text="t.name"></span>
                        <span class="block text-[9px] text-brand-gray/80 line-clamp-1" x-text="t.expertise"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Right Panel: Conversation Feed -->
    <div class="flex-1 flex flex-col justify-between bg-brand-dark-secondary/10">
        <!-- Chat Header -->
        <div class="p-4 border-b border-brand-teal/10 bg-brand-dark-secondary/20 flex items-center justify-between">
            <div>
                <h4 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Active Connection</h4>
                <strong class="text-sm text-brand-white block mt-0.5" x-text="activeTeacherName">Select a teacher</strong>
            </div>
            
            <!-- Call Shortcuts -->
            <div x-show="activeTeacherId" class="flex gap-2">
                <a href="{{ route('academy.mentorship') }}" class="rounded bg-brand-teal/10 border border-brand-teal/20 text-brand-cyan text-[10px] px-2.5 py-1 font-bold hover:bg-brand-teal/20 transition-all">
                    📅 Schedule Call
                </a>
            </div>
        </div>

        <!-- Chat Logs -->
        <div class="flex-1 p-4 overflow-y-auto space-y-4 font-sans text-xs" x-ref="chatBox">
            <template x-for="m in filteredMessages">
                <div class="flex flex-col max-w-[70%]"
                     :class="m.sender_role === 'student' ? 'ml-auto items-end' : 'mr-auto items-start'">
                    
                    <div class="rounded-2xl p-3 border leading-relaxed"
                         :class="m.sender_role === 'student' 
                             ? 'bg-brand-teal/15 text-brand-white border-brand-teal/30 rounded-tr-none' 
                             : 'bg-[#25282D] text-brand-gray border-white/5 rounded-tl-none'">
                        
                        <p x-text="m.text"></p>
                    </div>
                    
                    <span class="text-[9px] text-brand-gray/50 mt-1 font-mono" x-text="m.time"></span>
                </div>
            </template>
        </div>

        <!-- Input Form Bar -->
        <div class="p-3 bg-brand-dark-secondary/35 border-t border-brand-teal/10 flex items-center gap-2">
            <button @click="alert('Upload channel is online. Attach files, images or mock zips here.')" class="h-9 w-9 rounded-lg bg-brand-dark-secondary border border-brand-teal/10 flex items-center justify-center hover:bg-brand-teal/5 transition-colors text-xs text-brand-gray hover:text-brand-cyan cursor-pointer select-none">
                📎
            </button>
            
            <input type="text" 
                   x-model="inputText" 
                   @keydown.enter="sendMsg()"
                   placeholder="Type your question or message..." 
                   class="flex-1 rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
            
            <button @click="sendMsg()" class="h-9 rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 text-xs font-black text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                Send
            </button>
        </div>
    </div>
</div>

<script>
function messagesState() {
    return {
        teachers: @json($teachers),
        activeTeacherId: null,
        activeTeacherName: 'Choose an instructor',
        inputText: '',
        messages: [
            { id: 1, teacher_id: 1, sender_role: 'teacher', text: 'Hi! Let me know if you have any questions about Agile database normalization models.', time: 'Today 10:15' },
            { id: 2, teacher_id: 1, sender_role: 'student', text: 'Thanks, does SQLite support the JSON column queries natively?', time: 'Today 10:17' },
            { id: 3, teacher_id: 1, sender_role: 'teacher', text: 'Yes, absolutely! Since SQLite 3.38.0, JSON functions are built-in by default.', time: 'Today 10:20' }
        ],
        filteredMessages: [],

        init() {
            if (this.teachers.length > 0) {
                this.selectTeacher(this.teachers[0]);
            }
        },

        selectTeacher(teacher) {
            this.activeTeacherId = teacher.id;
            this.activeTeacherName = teacher.name;
            this.filterMessages();
        },

        filterMessages() {
            this.filteredMessages = this.messages.filter(m => m.teacher_id == this.activeTeacherId);
            this.$nextTick(() => {
                const c = this.$refs.chatBox;
                if (c) c.scrollTop = c.scrollHeight;
            });
        },

        sendMsg() {
            if (!this.inputText.trim() || !this.activeTeacherId) return;
            
            // Push student message
            this.messages.push({
                id: this.messages.length + 1,
                teacher_id: this.activeTeacherId,
                sender_role: 'student',
                text: this.inputText,
                time: 'Just Now'
            });

            const sentText = this.inputText;
            this.inputText = '';
            this.filterMessages();

            // Simulate teacher response
            setTimeout(() => {
                let reply = "I've received your query! I will review the codebase details and get back to you shortly.";
                if (sentText.toLowerCase().includes('help') || sentText.toLowerCase().includes('error')) {
                    reply = "Can you share the exact error stack or exception log? That will help pinpoint the issue.";
                }
                this.messages.push({
                    id: this.messages.length + 1,
                    teacher_id: this.activeTeacherId,
                    sender_role: 'teacher',
                    text: reply,
                    time: 'Just Now'
                });
                this.filterMessages();
            }, 1000);
        }
    };
}
</script>
@endsection
