@extends('layouts.app')

@section('title', 'About Diwebs Tech - Elite Technological Innovations')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">
            Our Purpose & Vision
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            Diwebs Tech Agency was founded with a singular mission: to conceptualize, engineer, and deploy high-performance digital systems that empower organizations globally.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-xl font-bold text-brand-cyan mb-4">Core Principles</h3>
            <ul class="space-y-4 text-sm text-brand-gray">
                <li class="flex items-start gap-2">
                    <span class="text-brand-cyan">✔</span>
                    <span><strong>Uncompromising Performance:</strong> Every line of code, queries, and layouts is optimized for peak load responsiveness.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-brand-cyan">✔</span>
                    <span><strong>Secure by Design:</strong> Hardened security implementations, threat monitoring, and data encryption are standard.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-brand-cyan">✔</span>
                    <span><strong>Dual Deployment Scalability:</strong> Seamless migrations from hosting environments to container clouds without refactoring.</span>
                </li>
            </ul>
        </div>
        <div class="glass-card rounded-2xl p-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-dot-matrix opacity-20"></div>
            <h3 class="text-xl font-bold text-brand-white mb-4">Enterprise Target</h3>
            <p class="text-sm text-brand-gray leading-relaxed">
                By fusing advanced software architecture with machine learning capabilities, we provide digital infrastructure solutions that enable corporations and government departments to orchestrate large-scale automated operations.
            </p>
        </div>
    </div>
</div>
@endsection
