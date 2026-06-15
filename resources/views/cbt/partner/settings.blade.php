@extends('layouts.cbt')

@section('title', 'Center Configuration Settings - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    
    <div>
        <h1 class="text-2xl font-bold text-brand-white">Center Configuration Settings</h1>
        <p class="text-sm text-brand-gray mt-1">Configure physical infrastructure details, local staff contacts, and internet parameters.</p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 max-w-3xl">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2 mb-6">Infrastructure Profile</h3>
        
        <form action="#" method="POST" class="space-y-6 text-xs">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Center Name -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Center Name</label>
                    <input type="text" name="name" value="{{ $center->name }}" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" />
                </div>

                <!-- Center Code -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Center Code (Read Only)</label>
                    <input type="text" value="{{ $center->code }}" readonly class="w-full rounded-xl bg-brand-dark-secondary/50 border border-brand-teal/5 p-3 text-brand-gray font-mono cursor-not-allowed" />
                </div>

                <!-- Contact Email -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $center->contact_email }}" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" />
                </div>

                <!-- Contact Phone -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ $center->contact_phone }}" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" />
                </div>

                <!-- Address -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Physical Address</label>
                    <input type="text" name="address" value="{{ $center->address }}" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" />
                </div>

                <!-- City -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">City</label>
                    <input type="text" name="city" value="{{ $center->city }}" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" />
                </div>

                <!-- Capacity -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Seat Capacity</label>
                    <input type="number" name="capacity" value="{{ $center->capacity }}" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" />
                </div>

                <!-- Internet Quality -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Primary ISP Link</label>
                    <select name="internet_quality" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan">
                        <option value="enterprise" {{ $center->internet_quality === 'enterprise' ? 'selected' : '' }}>Fiber Optic (Enterprise Tier)</option>
                        <option value="standard" {{ $center->internet_quality === 'standard' ? 'selected' : '' }}>Standard Broadband DSL</option>
                        <option value="cellular" {{ $center->internet_quality === 'cellular' ? 'selected' : '' }}>Cellular Backup Grid Only</option>
                    </select>
                </div>

                <!-- Power Backup -->
                <div class="space-y-2">
                    <label class="block text-brand-gray font-bold uppercase tracking-wider text-[10px]">Backup Power Source</label>
                    <select name="power_backup" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan">
                        <option value="full_redundancy" {{ $center->power_backup === 'full_redundancy' ? 'selected' : '' }}>Full Automatic UPS + Diesel Generator</option>
                        <option value="partial" {{ $center->power_backup === 'partial' ? 'selected' : '' }}>UPS Backup (1 Hour Retention)</option>
                        <option value="none" {{ $center->power_backup === 'none' ? 'selected' : '' }}>Grid Connection Only (No backup)</option>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <div class="border-t border-brand-teal/10 pt-6 flex justify-end">
                <button type="submit" @click.prevent="alert('Center configuration changes saved successfully.')" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-3 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                    Save Configuration
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
