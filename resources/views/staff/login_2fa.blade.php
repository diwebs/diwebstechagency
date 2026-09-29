@extends('layouts.app')

@section('title', 'Two-Factor Authentication - Staff Gateway')

@section('content')
<div class="mx-auto max-w-md px-4">
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden border border-brand-teal/15 shadow-2xl">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10 text-center mb-6">
            <span class="text-3xl">🛡️</span>
            <h2 class="text-2xl font-bold text-brand-white mt-3">Google Authenticator</h2>
            <p class="mt-2 text-xs text-brand-gray">Please enter the 6-digit TOTP verification code from your Google Authenticator app.</p>
        </div>

        @if(session('error'))
            <div class="relative z-10 mb-4 p-3 rounded bg-rose-500/10 border border-rose-500/30 text-[11px] text-rose-400">
                {{ session('error') }}
            </div>
        @endif

        <div class="relative z-10 space-y-4">
            <form action="{{ route('staff.login.2fa.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <input type="text" 
                           name="code" 
                           maxlength="6" 
                           required 
                           placeholder="000000" 
                           autofocus
                           class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-2xl font-bold tracking-[8px] text-center text-brand-cyan focus:border-brand-cyan focus:outline-none transition-all">
                </div>
                
                <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Verify Code</button>
                <a href="{{ route('staff.login') }}" class="block text-xs text-brand-gray hover:text-brand-white transition-all text-center">Back to Login</a>
            </form>
        </div>
    </div>
</div>
@endsection
