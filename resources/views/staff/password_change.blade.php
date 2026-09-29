@extends('layouts.app')

@section('title', 'Initial Password Renewal - Staff Portal')

@section('content')
<div class="mx-auto max-w-md px-4">
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden border border-brand-teal/15 shadow-2xl">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10 text-center mb-6">
            <span class="text-3xl">🔑</span>
            <h2 class="text-2xl font-bold text-brand-white mt-3">Reset Initial Password</h2>
            <p class="mt-2 text-xs text-brand-gray">To guarantee access security, you are required to change your temporary password on your first authentication.</p>
        </div>

        @if($errors->any())
            <div class="relative z-10 mb-4 p-3 rounded bg-rose-500/10 border border-rose-500/30 text-xs text-rose-400">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="relative z-10 space-y-4">
            <form action="{{ route('staff.password.change.post') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Password input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-brand-cyan uppercase">New Secure Password</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           placeholder="At least 8 characters..."
                           class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <!-- Confirm password input -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-brand-cyan uppercase">Confirm New Password</label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           required 
                           placeholder="Re-enter password..."
                           class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Activate Account</button>
            </form>
        </div>
    </div>
</div>
@endsection
