@extends('layouts.app')

@section('title', 'Diwebs Tech - Case Studies')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow">
            Case Studies
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            Proven engineering outcomes.
        </p>
    </div>

    <div class="glass-card rounded-2xl p-8 mb-8">
        <h3 class="text-xl font-bold text-brand-cyan">Scaling CBT Verification to 50k Concurrent Users</h3>
        <p class="mt-2 text-sm text-brand-gray">By migrating the central CBT scheduling portal to a load-balanced Redis session store, we achieved <200ms page load times under stress.</p>
    </div>
</div>
@endsection
