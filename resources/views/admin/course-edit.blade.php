@extends('layouts.admin')

@section('title', isset($course) ? 'Edit Course - Admin' : 'New Course - Admin')

@section('admin_content')
<div>
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.courses') }}" class="text-brand-gray hover:text-brand-cyan text-sm transition-colors">
                    ← LMS Courses
                </a>
                <span class="text-brand-gray/40 text-sm">/</span>
                <span class="text-sm text-brand-white">{{ isset($course) ? 'Edit Course' : 'New Course' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-brand-white">
                {{ isset($course) ? 'Edit: ' . $course->title : 'Create New Course' }}
            </h1>
            <p class="text-sm text-brand-gray mt-1">
                {{ isset($course) ? 'Update course details, syllabus, and manage lessons below.' : 'Fill in the details to publish a new course to the LMS catalog.' }}
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-3.5 text-sm font-medium text-emerald-400">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-5 py-3.5 text-sm font-medium text-red-400 space-y-1">
            @foreach ($errors->all() as $error)
                <div>⚠️ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Course Form --}}
    <form method="POST"
          action="{{ isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Main Details --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Course Title --}}
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-5">Course Details</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5" for="title">Course Title *</label>
                            <input id="title" name="title" type="text" required
                                   value="{{ old('title', $course->title ?? '') }}"
                                   class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                                   placeholder="e.g. Advanced Full-Stack Engineering" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5" for="description">Description *</label>
                            <textarea id="description" name="description" rows="4" required
                                      class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors resize-none"
                                      placeholder="Briefly describe what students will learn...">{{ old('description', $course->description ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5" for="instructor_name">Instructor Name *</label>
                            <input id="instructor_name" name="instructor_name" type="text" required
                                   value="{{ old('instructor_name', $course->instructor_name ?? '') }}"
                                   class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                                   placeholder="e.g. Dr. Amina Yusuf" />
                        </div>
                    </div>
                </div>

                {{-- Syllabus --}}
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-5">Syllabus Topics</h2>
                    <p class="text-xs text-brand-gray mb-3">Enter one topic per line. These will appear as the course outline.</p>
                    <textarea id="syllabus" name="syllabus" rows="6"
                              class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors resize-none font-mono"
                              placeholder="Introduction to the Course&#10;Module 1: Foundations&#10;Module 2: Advanced Concepts&#10;Final Project">{{ old('syllabus', isset($course) && $course->syllabus ? implode("\n", $course->syllabus) : '') }}</textarea>
                </div>

            </div>

            {{-- Right: Metadata --}}
            <div class="space-y-5">

                {{-- Pricing & Settings --}}
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-5">Pricing & Settings</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5" for="price">Price (USD) *</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-brand-gray text-sm font-bold">$</span>
                                <input id="price" name="price" type="number" step="0.01" min="0" required
                                       value="{{ old('price', $course->price ?? '0.00') }}"
                                       class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 pl-8 pr-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors" />
                            </div>
                            <p class="text-[10px] text-brand-gray mt-1">Set to 0 for a free course.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5" for="category">Category</label>
                            <select id="category" name="category"
                                    class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors">
                                <option value="">— Select Category —</option>
                                @foreach(['Software Engineering', 'AI & Machine Learning', 'Cybersecurity', 'Cloud & DevOps', 'Data Science', 'Web Development', 'Mobile Development', 'CBT Preparation', 'Business & Management'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $course->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5" for="difficulty">Difficulty Level</label>
                            <select id="difficulty" name="difficulty"
                                    class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors">
                                @foreach(['All Levels', 'Beginner', 'Intermediate', 'Advanced'] as $level)
                                    <option value="{{ $level }}" {{ old('difficulty', $course->difficulty ?? 'All Levels') === $level ? 'selected' : '' }}>{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5" for="cover_image">Cover Image URL</label>
                            <input id="cover_image" name="cover_image" type="url"
                                   value="{{ old('cover_image', $course->cover_image ?? '') }}"
                                   class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                                   placeholder="https://..." />
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-lg shadow-brand-teal/25 hover:opacity-90 transition-all">
                    {{ isset($course) ? '💾 Save Changes' : '🚀 Publish Course' }}
                </button>

                <a href="{{ route('admin.courses') }}"
                   class="block w-full rounded-xl border border-brand-teal/20 py-3 text-center text-sm font-bold text-brand-gray hover:text-brand-white hover:border-brand-teal/50 transition-all">
                    Cancel
                </a>
            </div>
        </div>
    </form>

    {{-- Lessons Panel (only on Edit mode) --}}
    @isset($course)
    <div class="mt-8">
        <div class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden">
            <div class="px-6 py-4 border-b border-brand-teal/10 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-brand-white">Course Lessons</h2>
                    <p class="text-xs text-brand-gray mt-0.5">{{ $course->lessons->count() }} lessons in this course</p>
                </div>
            </div>

            {{-- Existing Lessons List --}}
            @if($course->lessons->count())
            <div class="divide-y divide-brand-teal/10">
                @foreach($course->lessons->sortBy('sort_order') as $lesson)
                <div class="flex items-center gap-4 px-6 py-4">
                    <div class="w-6 h-6 rounded-full bg-brand-teal/10 border border-brand-teal/25 flex items-center justify-center text-[10px] font-bold text-brand-cyan flex-shrink-0">
                        {{ $lesson->sort_order ?: $loop->iteration }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-brand-white truncate">{{ $lesson->title }}</p>
                        <p class="text-[10px] text-brand-gray mt-0.5">
                            {{ $lesson->video_url ? '🎬 Has Video' : '📄 Text Only' }} &nbsp;•&nbsp;
                            {{ $lesson->duration_seconds > 0 ? round($lesson->duration_seconds / 60) . ' min' : 'No duration set' }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.courses.lessons.delete', [$course->id, $lesson->id]) }}"
                          onsubmit="return confirm('Delete this lesson?')">
                        @csrf
                        <button type="submit" class="text-[10px] font-bold text-red-400 hover:text-red-300 transition-colors px-2 py-1 rounded hover:bg-red-500/10">
                            🗑️ Remove
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Add New Lesson Form --}}
            <div class="px-6 py-5 bg-brand-dark-secondary/30 border-t border-brand-teal/10">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-4">➕ Add New Lesson</h3>
                <form method="POST" action="{{ route('admin.courses.lessons.store', $course->id) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-brand-gray mb-1.5">Lesson Title *</label>
                            <input name="title" type="text" required
                                   class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                                   placeholder="e.g. Introduction to REST APIs" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-brand-gray mb-1.5">Lesson Content</label>
                            <textarea name="content" rows="3"
                                      class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors resize-none"
                                      placeholder="Write lesson notes or overview..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-gray mb-1.5">Video URL</label>
                            <input name="video_url" type="url"
                                   class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                                   placeholder="https://..." />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-gray mb-1.5">Duration (seconds)</label>
                                <input name="duration_seconds" type="number" min="0"
                                       class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                                       placeholder="600" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-gray mb-1.5">Sort Order</label>
                                <input name="sort_order" type="number" min="0"
                                       value="{{ $course->lessons->count() + 1 }}"
                                       class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-5 py-2.5 text-sm font-bold text-brand-dark-secondary shadow-lg shadow-brand-teal/20 hover:opacity-90 transition-all">
                            ➕ Add Lesson
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endisset
</div>
@endsection
