@extends('layouts.app')

@section('title', 'Register - Diwebs Digital Ecosystem')

@section('content')
<div class="mx-auto max-w-md px-4">
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10 text-center mb-6">
            <h2 class="text-2xl font-bold text-brand-white">Ecosystem Registration</h2>
            <p class="mt-2 text-xs text-brand-gray">Create an account to join the network</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="relative z-10 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-brand-cyan uppercase">Full Name</label>
                <input type="text" name="name" id="name" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-brand-cyan uppercase">Email Address</label>
                <input type="email" name="email" id="email" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold text-brand-cyan uppercase">System Role</label>
                <select name="role" id="role" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    <option value="student">Academy Student (LMS)</option>
                    <option value="client">Agency Client (Projects Tracking)</option>
                    <option value="candidate">Exam Candidate (CBT Testing)</option>
                </select>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-brand-cyan uppercase">Password</label>
                <input type="password" name="password" id="password" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-brand-cyan uppercase">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
            </div>

            <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Register Account</button>
        </form>
    </div>
</div>
@endsection
