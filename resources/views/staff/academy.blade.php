@extends('layouts.staff')

@section('title', 'Academy Training - Staff Portal')

@section('staff_content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-brand-white">Staff Academy &amp; Professional Development</h1>
        <p class="text-xs text-brand-gray mt-1">Enroll in corporate training programs, bootcamps, and view your course catalog progress.</p>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($courses as $course)
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4 flex flex-col justify-between">
                <div>
                    <span class="inline-flex rounded bg-brand-cyan/10 px-2 py-0.5 text-[10px] font-bold text-brand-cyan tracking-wider font-mono">LMS Training</span>
                    <h3 class="text-base font-bold text-brand-white mt-2">{{ $course->title }}</h3>
                    <p class="text-xs text-brand-gray mt-2 leading-relaxed">{{ $course->description }}</p>
                    <span class="text-[10px] text-brand-gray mt-3 block font-medium">Instructor: <strong>{{ $course->instructor_name }}</strong></span>
                </div>
                
                <div class="pt-4 border-t border-brand-teal/10 flex justify-between items-center gap-4">
                    <span class="text-xs text-brand-cyan font-bold font-mono">Professional Tier</span>
                    <a href="{{ route('academy.course', $course->slug) }}" target="_blank" class="rounded-lg bg-brand-teal/20 border border-brand-teal/30 hover:bg-brand-teal/30 px-4 py-2 text-xs font-bold text-brand-cyan transition-all">
                        Launch Course Portal →
                    </a>
                </div>
            </div>
        @empty
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 text-center text-brand-gray lg:col-span-2">
                No professional development courses registered in the academy catalog yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
