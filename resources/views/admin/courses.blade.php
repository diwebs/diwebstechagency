@extends('layouts.admin')

@section('title', 'LMS Courses - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Academy Course Administration</h1>
            <p class="text-sm text-brand-gray mt-1">Manage learning tracks, syllabuses, video lesson uploads, and student rosters.</p>
        </div>
        <a href="{{ route('admin.courses.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-5 py-2.5 text-sm font-bold text-brand-dark-secondary shadow-lg shadow-brand-teal/20 hover:opacity-90 transition-all">
            <span>➕</span> New Course
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-3.5 text-sm font-medium text-emerald-400">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-5 py-3.5 text-sm font-medium text-red-400">
            ⚠️ {{ $errors->first() }}
        </div>
    @endif

    <!-- Course Grid list -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($courses as $course)
            <div class="glass-card glass-card-hover rounded-2xl overflow-hidden border border-brand-teal/15 flex flex-col justify-between group">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-3 text-[10px] uppercase font-bold text-brand-gray mb-3">
                        <span class="text-brand-cyan">{{ $course->category ?? 'General' }}</span>
                        <span>⏱️ {{ $course->lessons_count }} Lessons</span>
                    </div>

                    <h3 class="text-base font-bold text-brand-white group-hover:text-brand-cyan transition-colors">
                        {{ $course->title }}
                    </h3>

                    <p class="text-xs text-brand-gray mt-2.5 leading-relaxed line-clamp-2">
                        {{ $course->description }}
                    </p>

                    <div class="mt-3 text-[11px] text-brand-gray">
                        👤 <span class="text-brand-white">{{ $course->instructor_name }}</span>
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[10px] font-bold text-brand-cyan">
                            {{ $course->difficulty ?? 'All Levels' }}
                        </span>
                        <span class="text-xs font-mono font-bold text-emerald-400">
                            {{ $course->price > 0 ? \App\Helpers\PaymentHelper::format($course->price) : 'Free Track' }}
                        </span>
                    </div>
                </div>

                <div class="px-6 py-4 bg-brand-dark-secondary/50 border-t border-brand-teal/10 flex items-center justify-between">
                    <span class="text-[10px] text-brand-gray">Added {{ $course->created_at->diffForHumans() }}</span>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.courses.edit', $course->id) }}"
                           class="rounded bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 px-3 py-1.5 text-[10px] font-bold text-brand-cyan transition-all">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="{{ route('admin.courses.delete', $course->id) }}"
                              onsubmit="return confirm('Delete this course and all its lessons?')">
                            @csrf
                            <button type="submit"
                                    class="rounded bg-red-500/15 border border-red-500/30 hover:bg-red-500/25 px-3 py-1.5 text-[10px] font-bold text-red-400 transition-all">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card rounded-2xl p-12 text-center text-brand-gray">
                <div class="text-4xl mb-3">🎓</div>
                <p class="text-base font-semibold text-brand-white mb-1">No Courses Yet</p>
                <p class="text-sm mb-5">Start building your LMS catalog by adding the first course.</p>
                <a href="{{ route('admin.courses.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-5 py-2.5 text-sm font-bold text-brand-dark-secondary">
                    ➕ Create First Course
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</div>
@endsection
