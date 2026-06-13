@extends('layouts.app')

@section('title', 'Diwebs Tech - Custom Product Solutions')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">
            Enterprise Solutions
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            Turnkey technology software suites custom built to address operational bottlenecks.
        </p>
    </div>

    <div class="space-y-8">
        <!-- Solution 1 -->
        <div class="glass-card rounded-2xl p-8 border-l-4 border-l-brand-cyan">
            <h3 class="text-xl font-bold text-brand-white">Diwebs Academy (LMS)</h3>
            <p class="mt-2 text-sm text-brand-gray">
                A modern learning management platform complete with video playlists, course tracking progress, automated grading quizzes, and verified PDF certificates.
            </p>
        </div>
        <!-- Solution 2 -->
        <div class="glass-card rounded-2xl p-8 border-l-4 border-l-brand-teal">
            <h3 class="text-xl font-bold text-brand-white">Diwebs CBT Platform</h3>
            <p class="mt-2 text-sm text-brand-gray">
                A robust examination system incorporating candidate verification protocols, webcam surveillance checks, tab locks, and database analytics for high-volume exam environments.
            </p>
        </div>
    </div>
</div>
@endsection
