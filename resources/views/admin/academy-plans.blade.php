@extends('layouts.admin')

@section('title', 'Academy Plans - Admin Control Center')

@section('admin_content')
<div>
    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Academy Access Plans</h1>
            <p class="text-sm text-brand-gray mt-1">Assign and manage live class, audio, and mentorship access per student.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-brand-teal/10 px-3 py-1.5 text-[10px] font-bold text-brand-cyan border border-brand-teal/20">
                {{ $plans->total() }} Total Plans
            </span>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-6 rounded-xl bg-emerald-950/40 border border-emerald-500/30 p-3 text-xs text-emerald-400 flex items-center gap-2">
        <span>✓</span> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 rounded-xl bg-rose-950/40 border border-rose-500/30 p-3 text-xs text-rose-400 flex items-center gap-2">
        <span>✗</span> {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ── Left: Create Plan Form ───────────────────────────────────────── --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3 flex items-center gap-2">
                    <span>🔑</span> Create New Plan
                </h3>

                <form action="{{ route('admin.academy-plans.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Student --}}
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Student / User</label>
                        <select name="user_id" required
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="">— Select a student —</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }}) · {{ ucfirst($u->role) }}
                            </option>
                            @endforeach
                        </select>
                        @error('user_id')<p class="text-rose-400 text-[10px] mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Course (optional) --}}
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">
                            Course <span class="text-brand-gray font-normal normal-case">(optional — leave blank for all courses)</span>
                        </label>
                        <select name="course_id"
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="">All Courses (Global Plan)</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Plan Name --}}
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Plan Name / Label</label>
                        <input type="text" name="plan_name" value="{{ old('plan_name') }}" required
                               placeholder="e.g. React Bootcamp — Full Access"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        @error('plan_name')<p class="text-rose-400 text-[10px] mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Access Toggles --}}
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-3">Access Features</label>
                        <div class="space-y-3">
                            @foreach([
                                ['includes_live_class',  '📺', 'Live Class Access',    'Allow student to join scheduled live sessions'],
                                ['includes_audio',       '🎧', 'Audio Learning Access','Allow student to stream audio lessons'],
                                ['includes_mentorship',  '📅', 'Mentorship Booking',   'Allow student to book 1-on-1 coaching sessions'],
                            ] as [$field, $icon, $label, $desc])
                            <label class="flex items-start gap-3 glass-card p-3 rounded-xl border border-brand-teal/10 hover:border-brand-teal/25 cursor-pointer transition-all">
                                <input type="checkbox" name="{{ $field }}" value="1"
                                       {{ old($field) ? 'checked' : ($field === 'includes_audio' ? 'checked' : '') }}
                                       class="mt-0.5 accent-brand-cyan w-4 h-4">
                                <div>
                                    <span class="text-xs font-bold text-brand-white flex items-center gap-1.5">{{ $icon }} {{ $label }}</span>
                                    <span class="text-[10px] text-brand-gray mt-0.5 block">{{ $desc }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Expiry --}}
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">
                            Expiry Date <span class="text-brand-gray font-normal normal-case">(optional — blank = no expiry)</span>
                        </label>
                        <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Admin Notes (optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Paid via Paystack — ref #XYZ"
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-3 border-t border-brand-teal/10">
                        <button type="submit"
                                class="w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                            🔑 Assign Plan to Student
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info card --}}
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/10">
                <h4 class="text-xs font-bold text-brand-cyan uppercase tracking-wider mb-2">How Plans Work</h4>
                <ul class="text-xs text-brand-gray/80 space-y-1.5 leading-relaxed">
                    <li>• Plans are assigned per student. They appear immediately on the student's dashboard.</li>
                    <li>• A student without a <strong class="text-brand-white">Live Class</strong> plan sees a locked upgrade gate on the live classes page.</li>
                    <li>• Plans can be linked to a specific course or set globally for all courses.</li>
                    <li>• Expired or cancelled plans are automatically ignored.</li>
                </ul>
            </div>
        </div>

        {{-- ── Right: Active Plans Table ────────────────────────────────────── --}}
        <div class="lg:col-span-7">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <div class="flex items-center justify-between mb-5 border-b border-brand-teal/10 pb-3">
                    <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider flex items-center gap-2">
                        <span>📋</span> All Assigned Plans
                    </h3>
                </div>

                <div class="space-y-4">
                    @forelse($plans as $plan)
                    <div class="rounded-xl border p-5 transition-all
                        {{ $plan->status === 'active' ? 'border-brand-teal/20 bg-brand-dark-secondary/20' : 'border-rose-500/15 bg-rose-950/10 opacity-70' }}">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                {{-- Plan name + status badge --}}
                                <div class="flex items-center flex-wrap gap-2 mb-2">
                                    <h4 class="text-xs font-bold text-brand-white">{{ $plan->plan_name }}</h4>
                                    <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase
                                        {{ $plan->status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/20' : 'bg-rose-950 text-rose-400 border border-rose-500/20' }}">
                                        {{ $plan->status }}
                                    </span>
                                </div>

                                {{-- Student & course info --}}
                                <p class="text-[11px] text-brand-gray">
                                    <span class="text-brand-cyan font-semibold">Student:</span>
                                    {{ $plan->user ? $plan->user->name : '—' }}
                                    <span class="text-brand-gray/40"> · {{ $plan->user ? $plan->user->email : '' }}</span>
                                </p>
                                <p class="text-[11px] text-brand-gray mt-0.5">
                                    <span class="text-brand-cyan font-semibold">Course:</span>
                                    {{ $plan->course ? $plan->course->title : 'All Courses (Global)' }}
                                </p>

                                {{-- Access flags --}}
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @if($plan->includes_live_class)
                                    <span class="rounded-full bg-emerald-950/40 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold px-2 py-0.5 uppercase">📺 Live</span>
                                    @else
                                    <span class="rounded-full bg-brand-dark-secondary text-brand-gray/40 border border-brand-teal/5 text-[9px] px-2 py-0.5 uppercase line-through">📺 Live</span>
                                    @endif

                                    @if($plan->includes_audio)
                                    <span class="rounded-full bg-purple-950/40 text-purple-400 border border-purple-500/20 text-[9px] font-bold px-2 py-0.5 uppercase">🎧 Audio</span>
                                    @else
                                    <span class="rounded-full bg-brand-dark-secondary text-brand-gray/40 border border-brand-teal/5 text-[9px] px-2 py-0.5 uppercase line-through">🎧 Audio</span>
                                    @endif

                                    @if($plan->includes_mentorship)
                                    <span class="rounded-full bg-blue-950/40 text-blue-400 border border-blue-500/20 text-[9px] font-bold px-2 py-0.5 uppercase">📅 Mentorship</span>
                                    @else
                                    <span class="rounded-full bg-brand-dark-secondary text-brand-gray/40 border border-brand-teal/5 text-[9px] px-2 py-0.5 uppercase line-through">📅 Mentorship</span>
                                    @endif
                                </div>

                                {{-- Expiry / timestamps --}}
                                <div class="flex items-center gap-4 mt-2 text-[9px] text-brand-gray/50 font-mono">
                                    <span>Created: {{ $plan->created_at->format('M d, Y') }}</span>
                                    @if($plan->expires_at)
                                    <span class="{{ $plan->expires_at->isPast() ? 'text-rose-400' : 'text-amber-400/80' }}">
                                        Expires: {{ $plan->expires_at->format('M d, Y') }}
                                        {{ $plan->expires_at->isPast() ? '(Expired)' : '(' . $plan->expires_at->diffForHumans() . ')' }}
                                    </span>
                                    @else
                                    <span class="text-emerald-400/60">No expiry</span>
                                    @endif
                                </div>

                                @if($plan->notes)
                                <p class="text-[10px] text-brand-gray/60 mt-1.5 italic">Note: {{ $plan->notes }}</p>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col gap-2 shrink-0">
                                @if($plan->status === 'active')
                                <form action="{{ route('admin.academy-plans.cancel', $plan->id) }}" method="POST"
                                      onsubmit="return confirm('Cancel this plan for {{ addslashes($plan->user ? $plan->user->name : 'this student') }}?')">
                                    @csrf
                                    <button type="submit"
                                            class="w-full rounded-lg border border-rose-500/30 bg-rose-950/20 hover:bg-rose-950/40 px-3 py-1.5 text-[10px] font-bold text-rose-400 transition-all cursor-pointer">
                                        ✕ Cancel Plan
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.academy-plans.delete', $plan->id) }}" method="POST"
                                      onsubmit="return confirm('Permanently delete this plan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full rounded-lg border border-brand-teal/10 hover:border-rose-500/30 px-3 py-1.5 text-[10px] font-bold text-brand-gray hover:text-rose-400 transition-all cursor-pointer">
                                        🗑 Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="rounded-xl border border-dashed border-brand-teal/20 p-10 text-center">
                        <span class="text-4xl block mb-2">🔑</span>
                        <p class="text-xs text-brand-gray">No plans assigned yet. Use the form on the left to create your first plan.</p>
                    </div>
                    @endforelse
                </div>

                @if($plans->hasPages())
                <div class="mt-6 border-t border-brand-teal/10 pt-4">
                    {{ $plans->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
