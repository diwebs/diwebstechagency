@extends('layouts.cbt')

@section('title', 'CBT Candidate Profile - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Candidate Identity Profile</h1>
            <p class="text-sm text-brand-gray mt-1">Manage test credential parameters, biometric status locks, and secure browser settings.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Details box -->
        <div class="md:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/10 space-y-6">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Biographic Details</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-brand-gray">
                <div>
                    <span class="block mb-1">Full Legal Name:</span>
                    <strong class="text-brand-white text-sm">{{ auth()->user()->name }}</strong>
                </div>
                <div>
                    <span class="block mb-1">Email Coordinates:</span>
                    <strong class="text-brand-white text-sm">{{ auth()->user()->email }}</strong>
                </div>
                <div>
                    <span class="block mb-1">Role Designation:</span>
                    <strong class="text-brand-cyan text-sm uppercase">{{ auth()->user()->role }}</strong>
                </div>
                <div>
                    <span class="block mb-1">Security Clearance:</span>
                    <strong class="text-emerald-400 text-sm uppercase">Active verified</strong>
                </div>
            </div>
        </div>

        <!-- Biometrics diagnostics status box -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Biometric Status</h3>
            <div class="space-y-3 text-xs text-brand-gray">
                <div class="flex items-center justify-between">
                    <span>Face Verification Matrix:</span>
                    <span class="font-bold text-emerald-400">REGISTERED</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Webcam Permission Status:</span>
                    <span class="font-bold text-emerald-400">GRANTED</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Screen Share Capture:</span>
                    <span class="font-bold text-emerald-400">READY</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
