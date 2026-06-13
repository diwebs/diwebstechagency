@extends('layouts.app')

@section('title', 'Diwebs Tech - Custom Engineering Services')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">
            Our Technical Specializations
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            We provide deep expertise across critical technological sectors.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
        <!-- Service 1 -->
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-lg font-bold text-brand-cyan mb-2">1. Software Engineering</h3>
            <p class="text-sm text-brand-gray">
                Custom web application development, microservices setups, database normalizations, and high-performance backend programming using Laravel 12 and PHP 8.3.
            </p>
        </div>
        <!-- Service 2 -->
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-lg font-bold text-brand-cyan mb-2">2. Cloud Solutions & DevOps</h3>
            <p class="text-sm text-brand-gray">
                Continuous integration pipelines, docker configurations, AWS and Azure hostings, load balancings, and automatic scaling backups.
            </p>
        </div>
        <!-- Service 3 -->
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-lg font-bold text-brand-cyan mb-2">3. CBT Exam Infrastructure</h3>
            <p class="text-sm text-brand-gray">
                Provision of offline-capable test environments, local local-area synchronization engines, biometric checks, and candidate browser lock systems.
            </p>
        </div>
        <!-- Service 4 -->
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-lg font-bold text-brand-cyan mb-2">4. AI Engineering & Neural Integration</h3>
            <p class="text-sm text-brand-gray">
                Large language models finetuning, semantic vector searching, intelligent support pipelines, and OCR document verification automations.
            </p>
        </div>
    </div>
</div>
@endsection
