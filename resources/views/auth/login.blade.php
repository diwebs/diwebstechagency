@extends('layouts.app')

@section('title', 'Sign In - Diwebs Digital Ecosystem')

@section('content')
<div class="mx-auto max-w-md px-4">
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10 text-center mb-6">
            <h2 class="text-2xl font-bold text-brand-white">Ecosystem Authentication</h2>
            <p class="mt-2 text-xs text-brand-gray">Sign in to access your dashboard portal</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="relative z-10 space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-brand-cyan uppercase">Corporate Email</label>
                <input type="email" name="email" id="email" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-brand-cyan uppercase">Password</label>
                <input type="password" name="password" id="password" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
            </div>

            <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Verify Credentials</button>
        </form>

        <div class="relative z-10 mt-8 border-t border-brand-teal/10 pt-6">
            <h4 class="text-xs font-semibold text-brand-white uppercase text-center mb-4">Dev Testing Sandbox</h4>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('auth.dev-login', 'super_admin') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">Admin Panel</a>
                <a href="{{ route('auth.dev-login', 'client') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">Client Portal</a>
                <a href="{{ route('auth.dev-login', 'student') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">Academy LMS</a>
                <a href="{{ route('auth.dev-login', 'candidate') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">CBT Engine</a>
            </div>
            <p class="mt-4 text-[10px] text-brand-gray text-center">Developer quick quick-logins automatically register tester entities in the DB.</p>
        </div>
    </div>
</div>
@endsection
