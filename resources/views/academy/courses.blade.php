@extends('layouts.academy')

@section('title', 'Academy Bootcamps & Catalog')

@section('academy_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">My Courses &amp; Bootcamps</h1>
        <p class="text-sm text-brand-gray mt-1">Explore enrolled curriculums and browse available training tracks.</p>
    </div>

    <!-- Active Bootcamps -->
    <div class="space-y-6 mb-12">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">Active Learning Bootcamps</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($enrollments as $enrollment)
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border border-brand-teal/10 hover:border-brand-teal/20 transition-all">
                    <div>
                        <h4 class="text-base font-bold text-brand-white">{{ $enrollment->course->title }}</h4>
                        <p class="text-[11px] text-brand-gray mt-1">Instructor: <span class="text-brand-cyan">{{ $enrollment->course->instructor_name }}</span></p>
                        
                        <!-- Progress bar -->
                        <div class="mt-5">
                            <div class="flex justify-between text-xs text-brand-gray mb-1.5">
                                <span>Curriculum Progress</span>
                                <span class="font-bold text-brand-cyan">{{ $enrollment->progress }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-brand-dark-secondary rounded-full overflow-hidden border border-brand-teal/10">
                                <div class="h-full bg-gradient-to-r from-brand-teal to-brand-cyan" style="width: {{ $enrollment->progress }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        @if($enrollment->progress === 100)
                            <span class="rounded bg-emerald-950 border border-emerald-500/20 px-2.5 py-1 text-[10px] text-emerald-400 font-bold">🎓 Certified Complete</span>
                        @else
                            <span class="text-[11px] text-brand-gray font-medium">Status: In Progress</span>
                        @endif

                        @php
                            $firstLesson = $enrollment->course->lessons->first();
                        @endphp
                        
                        @if($firstLesson)
                            <a href="{{ route('academy.lesson', [$enrollment->course->slug, $firstLesson->slug]) }}" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-extrabold text-brand-dark-secondary shadow hover:opacity-90 transition-all">
                                {{ $enrollment->progress > 0 ? 'Resume Lessons' : 'Start Bootcamp' }}
                            </a>
                        @else
                            <span class="text-xs text-brand-gray">Syllabus release pending</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                    You have not enrolled in any training bootcamps yet. Choose a program below to begin.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Catalog Catalog -->
    <div class="space-y-6">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider">LMS Extended Catalog</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($availableCourses as $course)
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border border-brand-teal/10 hover:border-brand-teal/20 transition-all">
                    <div>
                        <div class="flex items-start justify-between">
                            <h4 class="text-base font-bold text-brand-white leading-snug">{{ $course->title }}</h4>
                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-brand-teal/10 text-brand-cyan border border-brand-teal/20">
                                {{ $course->difficulty }}
                            </span>
                        </div>
                        <p class="text-[11px] text-brand-gray mt-1">Lead Instructor: <span class="text-brand-cyan">{{ $course->instructor_name }}</span></p>
                        <p class="text-xs text-brand-gray/80 mt-3 leading-relaxed">{{ $course->description }}</p>
                    </div>

                    <div class="mt-6 flex justify-between items-center border-t border-brand-teal/5 pt-4">
                        <span class="text-sm font-extrabold text-brand-white">${{ number_format($course->price, 2) }}</span>
                        
                        <div class="flex items-center gap-3">
                            <a href="{{ route('academy.course', $course->slug) }}" class="rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/50 hover:bg-brand-teal/10 px-4 py-2 text-xs font-bold text-brand-cyan transition-all">
                                View Syllabus
                            </a>
                            
                            <form action="{{ route('academy.enroll', $course->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-extrabold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                                    Instant Enroll
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                    No other course catalogs are registered in the system.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
