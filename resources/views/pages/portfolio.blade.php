@extends('layouts.app')

@section('title', 'Diwebs Tech - Project Portfolio')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow">
            Our Work
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            A selection of complex systems we have deployed.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-lg font-bold text-brand-white">Government CBT Infrastructure</h3>
            <p class="mt-2 text-sm text-brand-gray">A national testing portal synchronization framework managing 400+ physical centers and 2M candidate exams.</p>
        </div>
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-lg font-bold text-brand-white">FinTech Payment System</h3>
            <p class="mt-2 text-sm text-brand-gray">SaaS payroll and payment routing application processing $50M+ annual volume with strict PCI compliance.</p>
        </div>
    </div>
</div>
@endsection
