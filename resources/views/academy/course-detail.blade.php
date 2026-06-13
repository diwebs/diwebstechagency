@extends('layouts.app')

@section('title', $course->title . ' - Diwebs Academy')

@section('content')
<div class="mx-auto max-w-4xl px-6 lg:px-8">
    <div class="glass-card rounded-3xl p-8 md:p-12 relative overflow-hidden mb-8">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10">
            <h1 class="text-3xl font-bold tracking-tight text-brand-white">{{ $course->title }}</h1>
            <p class="text-sm text-brand-gray mt-2">Led by instructor <strong class="text-brand-white">{{ $course->instructor_name }}</strong></p>
            <p class="text-md text-brand-gray mt-6 max-w-2xl leading-relaxed">{{ $course->description }}</p>

            <div class="mt-8 flex justify-between items-center border-t border-brand-teal/10 pt-6">
                <div>
                    <span class="block text-[10px] uppercase text-brand-cyan tracking-wider">Tuition Fee</span>
                    <span class="text-2xl font-bold text-brand-white">${{ $course->price }}</span>
                </div>
                
                @if($isEnrolled)
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-brand-cyan font-bold">Enrolled (Progress: {{ $progress }}%)</span>
                        @if($course->lessons->first())
                            <a href="{{ route('academy.lesson', [$course->slug, $course->lessons->first()->slug]) }}" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90">Resume Syllabus</a>
                        @endif
                    </div>
                @else
                    <form action="{{ route('academy.enroll', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-3 text-sm font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">Register & Enroll</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Syllabus Modules -->
    <div class="space-y-6">
        <h3 class="text-lg font-bold text-brand-white">Course Syllabus & Lessons</h3>
        
        <div class="glass-card rounded-2xl p-6 divide-y divide-brand-teal/10">
            @forelse($course->lessons as $index => $lesson)
                <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-brand-white">Lesson {{ $index + 1 }}: {{ $lesson->title }}</h4>
                        <span class="text-[10px] text-brand-gray">Duration: {{ round($lesson->duration_seconds / 60) }} Mins</span>
                    </div>
                    
                    @if($isEnrolled)
                        <a href="{{ route('academy.lesson', [$course->slug, $lesson->slug]) }}" class="text-xs text-brand-cyan hover:underline">View Lesson</a>
                    @else
                        <span class="text-xs text-brand-gray">🔒 Locked</span>
                    @endif
                </div>
            @empty
                <p class="text-xs text-brand-gray py-4">No lessons released for this course yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
