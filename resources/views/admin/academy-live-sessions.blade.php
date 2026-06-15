@extends('layouts.admin')

@section('title', 'Academy Live Classes Management - Admin Control Center')

@section('admin_content')
<div class="space-y-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Academy Live Sessions Management</h1>
        <p class="text-sm text-brand-gray mt-1">Schedule classrooms, dispatch Google Meet URLs, and manage live state operations.</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-950/50 border border-emerald-500/30 p-4 text-xs text-emerald-400">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Scheduler Form -->
        <div class="lg:col-span-5">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Schedule Live Session</h3>
                
                <form action="{{ route('admin.academy.live-sessions.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Title -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Session Title</label>
                        <input type="text" name="title" required placeholder="e.g. Advanced AI Prompt Tuning"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Teacher Assign -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Assign Instructor / Mentor</label>
                        <select name="teacher_id" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date & Time -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Schedule Date &amp; Start Time</label>
                        <input type="datetime-local" name="date" required
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Duration -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Duration (mins)</label>
                            <input type="number" name="duration_minutes" required value="60" min="15"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Provider -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Meeting Provider</label>
                            <select name="meeting_provider" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                                <option value="google_meet">Google Meet</option>
                                <option value="zoom">Zoom SDK</option>
                            </select>
                        </div>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Session Category Type</label>
                        <select name="session_type" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="public_class">Public Live Class</option>
                            <option value="group_session">Group Study Session</option>
                            <option value="private_1_on_1">Private 1-on-1 Coaching</option>
                            <option value="corporate_training">Corporate Trainee Brief</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Brief Context description</label>
                        <textarea name="description" rows="3" placeholder="Topics to cover, homework requirements, and key sprints dependencies."
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-3 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all"></textarea>
                    </div>

                    <div class="pt-4 border-t border-brand-teal/10">
                        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-extrabold text-brand-dark-secondary shadow hover:opacity-90 active:scale-95 transition-all cursor-pointer font-sans">
                            ⚡ Dispatch Google Meet Session
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Sessions List -->
        <div class="lg:col-span-7">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 overflow-hidden">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Active Live Schedule Logs</h3>
                
                <div class="space-y-4 max-h-[580px] overflow-y-auto pr-1">
                    @forelse($sessions as $sess)
                        <div class="rounded-xl border p-4 text-xs space-y-3 
                             {{ $sess->status === 'live' ? 'border-emerald-500/30 bg-emerald-500/5 shadow' : 'border-brand-teal/10 bg-brand-dark-secondary/20' }}">
                            
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-brand-white leading-tight" style="font-size: 13px;">{{ $sess->title }}</span>
                                <span class="rounded px-2 py-0.5 text-[8px] font-bold uppercase
                                      {{ $sess->status === 'live' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/20 animate-pulse' : '' }}
                                      {{ $sess->status === 'scheduled' ? 'bg-brand-teal/10 text-brand-cyan border border-brand-teal/20' : '' }}
                                      {{ $sess->status === 'ended' ? 'bg-gray-950 text-gray-400 border border-white/5' : '' }}
                                      {{ $sess->status === 'cancelled' ? 'bg-rose-950 text-rose-400 border border-rose-500/20' : '' }}">
                                    {{ $sess->status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-y-1.5 text-brand-gray text-[11px]">
                                <div>Instructor: <strong class="text-brand-white">{{ $sess->teacher ? $sess->teacher->name : 'Staff Mentor' }}</strong></div>
                                <div>Duration: <strong class="text-brand-white">{{ $sess->duration_minutes }} mins</strong></div>
                                <div class="col-span-2">Date/Time: <strong class="text-brand-white">{{ $sess->date->format('Y-m-d H:i:s') }}</strong></div>
                                <div class="col-span-2">Meeting Link: <a href="{{ $sess->meeting_url }}" target="_blank" class="text-brand-cyan hover:underline font-mono">{{ $sess->meeting_url }}</a></div>
                            </div>

                            <!-- State modifiers -->
                            <div class="flex justify-end gap-2 border-t border-brand-teal/5 pt-3">
                                @if($sess->status === 'scheduled')
                                    <form action="{{ route('admin.academy.live-sessions.status', $sess->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="live">
                                        <button type="submit" class="rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/35 hover:bg-emerald-400 hover:text-brand-dark-secondary px-2.5 py-1 font-bold transition-all cursor-pointer">
                                            Go Live Now
                                        </button>
                                    </form>
                                @endif

                                @if($sess->status === 'live' || $sess->status === 'scheduled')
                                    <form action="{{ route('admin.academy.live-sessions.status', $sess->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="ended">
                                        <button type="submit" class="rounded bg-brand-dark-secondary border border-brand-teal/25 text-brand-gray hover:text-brand-white px-2.5 py-1 font-bold transition-all cursor-pointer">
                                            End Session
                                        </button>
                                    </form>
                                @endif

                                @if($sess->status !== 'cancelled' && $sess->status !== 'ended')
                                    <form action="{{ route('admin.academy.live-sessions.status', $sess->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="rounded bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500 hover:text-white px-2.5 py-1 font-bold transition-all cursor-pointer">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-brand-gray">No live session logs in the system.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
