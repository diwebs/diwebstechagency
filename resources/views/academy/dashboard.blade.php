@extends('layouts.app')

@section('title', 'Diwebs Academy - LMS Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mb-12">
        <h1 class="text-3xl font-bold tracking-tight text-brand-white">Diwebs Academy</h1>
        <p class="text-sm text-brand-gray mt-1">Accelerate your tech skills with curriculum built for enterprise scale.</p>
    </div>

    <!-- Enrolled Courses -->
    <div class="space-y-6 mb-16">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">My Active Bootcamps</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($enrollments as $enrollment)
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-brand-white">{{ $enrollment->course->title }}</h4>
                        <p class="text-xs text-brand-gray mt-1">Instructor: {{ $enrollment->course->instructor_name }}</p>
                        
                        <!-- Progress bar -->
                        <div class="mt-6">
                            <div class="flex justify-between text-xs text-brand-gray mb-1">
                                <span>Progress</span>
                                <span class="font-bold text-brand-cyan">{{ $enrollment->progress }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-brand-dark-secondary rounded-full overflow-hidden border border-brand-teal/10">
                                <div class="h-full bg-gradient-to-r from-brand-teal to-brand-cyan" style="width: {{ $enrollment->progress }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        @if($enrollment->progress === 100)
                            <span class="rounded bg-emerald-950 border border-emerald-500/20 px-2 py-1 text-[10px] text-emerald-400 font-semibold">🎓 Certified</span>
                        @else
                            <span class="text-xs text-brand-gray">Status: In Progress</span>
                        @endif

                        @php
                            $firstLesson = $enrollment->course->lessons->first();
                        @endphp
                        
                        @if($firstLesson)
                            <a href="{{ route('academy.lesson', [$enrollment->course->slug, $firstLesson->slug]) }}" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all">
                                {{ $enrollment->progress > 0 ? 'Resume Course' : 'Start Lesson' }}
                            </a>
                        @else
                            <span class="text-xs text-brand-gray">Syllabus release pending</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-sm">
                    You have not enrolled in any bootcamps yet. Explore the catalog below to begin.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Course Catalog -->
    <div class="space-y-6">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">Explore Course Catalog</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($availableCourses as $course)
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-brand-white">{{ $course->title }}</h4>
                        <p class="text-xs text-brand-gray mt-1">Instructor: {{ $course->instructor_name }}</p>
                        <p class="text-sm text-brand-gray mt-4">{{ $course->description }}</p>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <span class="text-md font-bold text-brand-white">${{ $course->price }}</span>
                        <a href="{{ route('academy.course', $course->slug) }}" class="rounded border border-brand-teal/30 bg-brand-teal/10 hover:bg-brand-teal/20 px-4 py-2 text-xs font-bold text-brand-cyan transition-all">
                            View Syllabus
                        </a>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-sm">
                    No other catalog items available currently.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
