@extends('layouts.app')

@section('title', 'Diwebs Tech - Tech Blog & Insights')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow">
            Tech Insights
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            Guides and updates from our engineering team.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="glass-card rounded-2xl p-8">
            <h4 class="text-xs font-semibold text-brand-cyan uppercase">Laravel</h4>
            <h3 class="mt-2 text-lg font-bold text-brand-white">Why Laravel 12 is Ready for Shared Hosting Deployments</h3>
            <p class="mt-2 text-sm text-brand-gray">An overview of package discovery optimizations, sqlite speed improvements, and task scheduling structures.</p>
        </div>
    </div>
</div>
@endsection
