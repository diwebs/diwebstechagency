@extends('layouts.app')

@section('title', 'Staff Portal Sign In - Diwebs Gateway')

@section('content')
<div class="mx-auto max-w-md px-4">
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden border border-brand-teal/15 shadow-2xl">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10 text-center mb-6">
            <span class="text-4xl">👔</span>
            <h2 class="text-2xl font-bold text-brand-white mt-3">Staff Portal Gateway</h2>
            <p class="mt-2 text-xs text-brand-gray">Authenticate to access your workspace and attendance tracker.</p>
        </div>

        @if(session('success'))
            <div class="relative z-10 mb-4 p-3 rounded bg-emerald-500/10 border border-emerald-500/30 text-[11px] text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="relative z-10 mb-4 p-3 rounded bg-rose-500/10 border border-rose-500/30 text-[11px] text-rose-400">
                {{ session('error') }}
            </div>
        @endif

        <div class="relative z-10 space-y-4">
            <form action="{{ route('staff.login.post') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Login Identifier -->
                <div>
                    <label for="login_field" class="block text-xs font-semibold text-brand-cyan uppercase">Staff ID, Email or Username</label>
                    <input type="text" 
                           name="login_field" 
                           id="login_field" 
                           required 
                           value="{{ old('login_field') }}"
                           placeholder="e.g. DWS-1001 or username"
                           class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-brand-cyan uppercase">Password</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <!-- Math Captcha -->
                <div>
                    <label for="captcha" class="block text-xs font-semibold text-brand-cyan uppercase">Security Verification Challenge</label>
                    <span class="block text-[10px] text-brand-gray mt-1">{{ $captchaText }}</span>
                    <input type="number" 
                           name="captcha" 
                           id="captcha" 
                           required 
                           placeholder="Solve math challenge..."
                           class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <!-- Remember Device -->
                <div class="flex items-center gap-2 select-none py-1">
                    <input type="checkbox" name="remember" id="remember" class="rounded bg-brand-dark-secondary border-brand-teal/20 text-brand-cyan focus:ring-brand-cyan/40">
                    <label for="remember" class="text-xs text-brand-gray cursor-pointer hover:text-brand-white transition-colors">Trust and remember this device</label>
                </div>

                <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Verify &amp; Sign In</button>
            </form>
        </div>
    </div>
</div>
@endsection
