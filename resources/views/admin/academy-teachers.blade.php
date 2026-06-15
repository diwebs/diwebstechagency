@extends('layouts.admin')

@section('title', 'Academy Teachers Database - Admin Control Center')

@section('admin_content')
<div class="space-y-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Academy Instructor Database</h1>
        <p class="text-sm text-brand-gray mt-1">Profile course instructors, guest lecturers, and 1-on-1 coaching mentors.</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-950/50 border border-emerald-500/30 p-4 text-xs text-emerald-400">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Profiles Creator Form -->
        <div class="lg:col-span-5">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Register Instructor</h3>
                
                <form action="{{ route('admin.academy.teachers.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Link User (Optional) -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Link User Account (Optional)</label>
                        <select name="user_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="">-- Standalone Profile (No system login user) --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. Dr. Ada Lovelace"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Instructor Email</label>
                        <input type="email" name="email" placeholder="instructor@diwebstechagency.website"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Role -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Role Title</label>
                            <select name="role" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                                <option value="instructor">Bootcamp Instructor</option>
                                <option value="mentor">1-on-1 Mentor</option>
                                <option value="guest_speaker">Guest Speaker</option>
                            </select>
                        </div>

                        <!-- Hourly Rate -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Hourly Rate ($)</label>
                            <input type="number" name="hourly_rate" value="50" min="0" step="0.01"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- Expertise -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Expertise Tag Keywords (comma separated)</label>
                        <input type="text" name="expertise" required placeholder="Laravel, MySQL, Cloud Architecture, Vue.js"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Bio -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Biography &amp; Certifications</label>
                        <textarea name="bio" required rows="3" placeholder="Profile info summary..."
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-3 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all"></textarea>
                    </div>

                    <!-- Media channels toggles -->
                    <div class="flex items-center gap-6 pt-2">
                        <div class="flex items-center gap-2 text-xs text-brand-white">
                            <input type="checkbox" name="voice_only_enabled" value="1" checked class="rounded bg-brand-dark-secondary border border-brand-teal/15 text-brand-cyan focus:ring-0">
                            <span>Voice Call Enabled</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-brand-white">
                            <input type="checkbox" name="video_enabled" value="1" checked class="rounded bg-brand-dark-secondary border border-brand-teal/15 text-brand-cyan focus:ring-0">
                            <span>Video Call Enabled</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-brand-teal/10">
                        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-extrabold text-brand-dark-secondary shadow hover:opacity-90 active:scale-95 transition-all cursor-pointer font-sans">
                            ✓ Onboard Instructor Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Profiles List -->
        <div class="lg:col-span-7">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 overflow-hidden">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Active Instructor Profiles</h3>
                
                <div class="space-y-4 max-h-[580px] overflow-y-auto pr-1">
                    @forelse($teachers as $teacher)
                        <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-4 text-xs space-y-2.5">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">👨‍💻</span>
                                    <div>
                                        <strong class="text-brand-white" style="font-size: 13px;">{{ $teacher->name }}</strong>
                                        <span class="block text-[10px] text-brand-cyan font-bold uppercase tracking-wider mt-0.5">{{ $teacher->role }}</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-emerald-400">${{ number_format($teacher->hourly_rate, 2) }}/hr</span>
                            </div>

                            <div class="text-[11px] text-brand-gray/80 leading-relaxed">{{ $teacher->bio }}</div>

                            <div class="flex flex-wrap gap-1">
                                @foreach(explode(',', $teacher->expertise) as $tag)
                                    <span class="rounded bg-brand-dark-secondary px-2 py-0.5 text-[9px] text-brand-gray border border-brand-teal/5">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>

                            <div class="flex gap-4 border-t border-brand-teal/5 pt-2 text-[10px] text-brand-gray/60">
                                <div>Call channels: 
                                    <span class="text-brand-white font-bold">
                                        {{ $teacher->voice_only_enabled ? 'Voice' : '' }}
                                        {{ $teacher->voice_only_enabled && $teacher->video_enabled ? ' & ' : '' }}
                                        {{ $teacher->video_enabled ? 'Video' : '' }}
                                    </span>
                                </div>
                                @if($teacher->email)
                                    <div>Email: <span class="text-brand-cyan">{{ $teacher->email }}</span></div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-brand-gray">No instructors have been added to the database yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
