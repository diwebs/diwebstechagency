@extends('layouts.academy')

@section('title', 'Teacher Mentorship Portal - Diwebs Academy')

@section('academy_content')
<div x-data="mentorshipState()" class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Teacher &amp; Mentor Directory</h1>
            <p class="text-sm text-brand-gray mt-1">Connect with expert instructors for instant voice/video calls or scheduled coaching.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs text-brand-gray">Mentors Active Now</span>
        </div>
    </div>

    <!-- Instructor Directory Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($teachers as $teacher)
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 hover:border-brand-teal/20 transition-all flex flex-col justify-between">
                <div>
                    <!-- Header with Role and Avatar -->
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-xl bg-brand-dark-secondary border border-brand-teal/20 flex items-center justify-center text-xl shadow-inner">
                                {{ $teacher->avatar ? $teacher->avatar : '👨‍💻' }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-brand-white leading-tight">{{ $teacher->name }}</h4>
                                <span class="text-[9px] uppercase tracking-wider text-brand-cyan font-bold block mt-0.5">{{ str_replace('_', ' ', $teacher->role) }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="block text-xs font-bold text-emerald-400">${{ number_format($teacher->hourly_rate, 2) }}</span>
                            <span class="block text-[8px] uppercase tracking-wider text-brand-gray mt-0.5">per hour</span>
                        </div>
                    </div>

                    <!-- Expertise Tags -->
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach(explode(',', $teacher->expertise) as $tag)
                            <span class="rounded bg-brand-dark-secondary px-2.5 py-0.5 text-[9px] font-bold text-brand-gray border border-brand-teal/5">
                                {{ trim($tag) }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Bio -->
                    <p class="text-xs text-brand-gray/80 leading-relaxed line-clamp-3">{{ $teacher->bio }}</p>
                    
                    <!-- Availability -->
                    <div class="mt-4 bg-brand-dark-secondary/40 border border-brand-teal/5 rounded-xl p-3 text-[10px] text-brand-gray/80">
                        <span class="font-bold text-brand-white block mb-1">Availability Schedule</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse($teacher->availabilities as $av)
                                @php
                                    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                @endphp
                                <span class="rounded bg-brand-teal/5 border border-brand-teal/15 px-2 py-0.5 text-brand-cyan font-semibold">
                                    {{ $days[$av->day_of_week] ?? 'Day' }}: {{ $av->start_time }} - {{ $av->end_time }}
                                </span>
                            @empty
                                <span class="text-brand-gray/50">Contact instructor for availability</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Call Matrix Operations -->
                <div class="mt-6 border-t border-brand-teal/5 pt-4 flex gap-3">
                    <!-- Instant Connect Voice -->
                    @if($teacher->voice_only_enabled)
                        <button @click="triggerInstantCall('voice', '{{ $teacher->name }}', '{{ $teacher->id }}')" 
                                class="flex-1 rounded-xl border border-brand-teal/20 hover:border-brand-teal bg-brand-dark-secondary/50 py-2.5 text-xs font-bold text-brand-cyan transition-all cursor-pointer">
                            📞 Voice Call
                        </button>
                    @endif

                    <!-- Instant Connect Video -->
                    @if($teacher->video_enabled)
                        <button @click="triggerInstantCall('video', '{{ $teacher->name }}', '{{ $teacher->id }}')" 
                                class="flex-1 rounded-xl border border-brand-teal/20 hover:border-brand-teal bg-brand-dark-secondary/50 py-2.5 text-xs font-bold text-brand-cyan transition-all cursor-pointer">
                            🎥 Video Call
                        </button>
                    @endif

                    <!-- Booking Schedule Drawer -->
                    <button @click="openBookingModal('{{ $teacher->name }}', '{{ $teacher->id }}')" 
                            class="flex-1 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-2.5 text-xs font-black text-brand-dark-secondary shadow hover:opacity-90 active:scale-95 transition-all cursor-pointer font-sans">
                        📅 Schedule
                    </button>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                No teacher profiles found in the database. Instructors are seeded from the administrative panel.
            </div>
        @endforelse
    </div>

    <!-- Booking scheduling modal -->
    <div x-show="showBookingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-brand-dark-secondary/80 backdrop-blur-md px-4" style="display:none;">
        <div class="glass-card rounded-3xl p-6 border border-brand-cyan/25 max-w-md w-full space-y-6 relative">
            <button @click="showBookingModal = false" class="absolute top-5 right-5 text-brand-gray hover:text-brand-white text-xs cursor-pointer select-none">✕ Close</button>
            
            <div>
                <span class="text-3xl">📅</span>
                <h3 class="text-base font-bold text-brand-white mt-3">Book Coaching Session</h3>
                <p class="text-[11px] text-brand-gray/80 mt-1">Schedule a dedicated 1-on-1 private class with mentor.</p>
            </div>

            <form action="{{ route('academy.bookings.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="teacher_id" :value="bookingTeacherId">

                <div class="space-y-1.5 text-xs text-brand-white">
                    <span class="block">Mentor Selected:</span>
                    <strong class="text-brand-cyan" x-text="bookingTeacherName">--</strong>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Pick Date</label>
                    <input type="date" required name="booking_date" 
                           class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                </div>

                <!-- Times -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Start Time</label>
                        <input type="time" required name="start_time" value="09:00"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">End Time</label>
                        <input type="time" required name="end_time" value="10:00"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Call type selection -->
                <div>
                    <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Session call format</label>
                    <select name="call_type" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <option value="video">🎥 1-on-1 Google Meet Video Call</option>
                        <option value="voice">📞 Voice Call (Only)</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-3 border-t border-brand-teal/10">
                    <button type="button" @click="showBookingModal = false" class="flex-1 rounded-xl border border-brand-teal/20 py-3 text-xs font-bold text-brand-gray hover:text-brand-white transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-black text-brand-dark-secondary shadow hover:opacity-90 active:scale-95 transition-all cursor-pointer font-sans">
                        ✓ Secure Session
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Simulated Calling Modal -->
    <div x-show="showCallingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-brand-dark-secondary/90 backdrop-blur-md px-4" style="display:none;">
        <div class="glass-card rounded-3xl p-8 border border-brand-cyan/25 max-w-sm w-full space-y-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-brand-cyan/5 rounded-full blur-[60px] animate-pulse-slow"></div>
            
            <div class="relative z-10 space-y-6">
                <!-- Dialing Avatar pulse -->
                <div class="relative inline-flex h-20 w-20 items-center justify-center rounded-full bg-brand-dark-secondary border border-brand-cyan/20 text-3xl">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-cyan opacity-40"></span>
                    👨‍💻
                </div>

                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-brand-cyan" x-text="callType === 'video' ? 'Initializing Video Stream' : 'Dialing Audio Line'">Dialing</h3>
                    <h4 class="text-lg font-bold text-brand-white mt-1.5" x-text="callTeacherName">--</h4>
                    <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">Securing P2P handshake protocol...</p>
                </div>

                <!-- Call status indicator -->
                <div class="text-xs text-brand-cyan font-bold font-mono animate-pulse" x-text="callStatus">
                    Connecting...
                </div>

                <!-- Cancel call button -->
                <div>
                    <button @click="showCallingModal = false" class="h-12 w-12 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold flex items-center justify-center mx-auto shadow shadow-rose-900 transition-colors cursor-pointer select-none">
                        ❌
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mentorshipState() {
    return {
        showBookingModal: false,
        bookingTeacherName: '',
        bookingTeacherId: '',
        
        showCallingModal: false,
        callTeacherName: '',
        callTeacherId: '',
        callType: 'video',
        callStatus: 'Dialing...',

        openBookingModal(name, id) {
            this.bookingTeacherName = name;
            this.bookingTeacherId = id;
            this.showBookingModal = true;
        },

        triggerInstantCall(type, name, id) {
            this.callType = type;
            this.callTeacherName = name;
            this.callTeacherId = id;
            this.callStatus = 'Connecting...';
            this.showCallingModal = true;

            setTimeout(() => {
                this.callStatus = 'Generating Google Meet URL...';
            }, 1000);

            setTimeout(() => {
                this.callStatus = 'Classroom Ready!';
                window.open('https://meet.google.com/abc-defg-hij', '_blank');
                this.showCallingModal = false;
            }, 2500);
        }
    };
}
</script>
@endsection
