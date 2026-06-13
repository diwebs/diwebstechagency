@extends('layouts.app')

@section('title', 'Contact Diwebs Tech - Establish a Technology Partnership')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow">
            Connect With Us
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            Let us build the future of your technological infrastructure.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-xl font-bold text-brand-cyan mb-4 font-sans">Corporate Contacts</h3>
            <p class="text-sm text-brand-gray">
                <strong>Address:</strong><br>
                102 Herbert Macaulay Way, Yaba, Lagos, Nigeria<br><br>
                <strong>Email:</strong> info@diwebs.com<br>
                <strong>Phone:</strong> +234 801 122 3344
            </p>
        </div>
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-xl font-bold text-brand-white mb-4">Direct Message</h3>
            <p class="text-sm text-brand-gray mb-6">Use the CRM form on our homepage to specify exact project budgets and scopes for a priority consultation.</p>
            <a href="{{ route('home') }}#contact-section" class="inline-block rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90">Open Intake Form</a>
        </div>
    </div>
</div>
@endsection
