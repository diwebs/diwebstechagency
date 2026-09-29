@extends('layouts.admin')

@section('title', 'CBT Centers - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">CBT Center Network</h1>
            <p class="text-sm text-brand-gray mt-1">Overview of all registered physical examination centers and their operational status.</p>
        </div>
    </div>

    <!-- Create CBT Center Form Card -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 mb-8">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-4">Register New CBT Center</h3>
        <form action="{{ route('admin.centers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Center Name</label>
                    <input type="text" name="name" required placeholder="e.g. Diwebs Tech Hub Lagos" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Center Code (Unique)</label>
                    <input type="text" name="code" required placeholder="e.g. DTH-LOS-01" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">City</label>
                    <input type="text" name="city" required placeholder="e.g. Lagos" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Full Physical Address</label>
                    <input type="text" name="address" required placeholder="e.g. 12 Joel Ogunnaike St, Ikeja GRA" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Capacity (Seats)</label>
                    <input type="number" name="capacity" required min="1" placeholder="e.g. 150" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Contact Phone</label>
                    <input type="text" name="contact_phone" required placeholder="e.g. +234 812 345 6789" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Contact Email</label>
                    <input type="email" name="contact_email" required placeholder="e.g. lagoshub@diwebstech.com" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Center Type</label>
                    <select name="center_type" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                        <option value="jamb">JAMB Accredited Center</option>
                        <option value="waec">WAEC/NECO Examination Hub</option>
                        <option value="school">Private/Institutional School Lab</option>
                        <option value="corporate">Corporate Assessment Center</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-1.5">Power Backup Model</label>
                    <select name="power_backup" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                        <option value="generator">Dual Backup Diesel Generator</option>
                        <option value="inverter">Solar Smart Inverter Array</option>
                        <option value="full_redundancy">Generator + Inverter Hybrid System</option>
                        <option value="no">No Backup Power (Grid Only)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary hover:opacity-90 active:scale-95 transition-all shadow">
                    ➕ Register CBT Center
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($centers as $center)
            <div class="glass-card glass-card-hover rounded-2xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-brand-teal/5 rounded-full blur-xl"></div>
                
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-brand-gray tracking-wider">{{ $center->code }}</span>
                        <h3 class="text-lg font-bold text-brand-white mt-1">{{ $center->name }}</h3>
                        <p class="text-xs text-brand-gray mt-1">{{ $center->address }}, {{ $center->city }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase
                        {{ $center->status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/20' : 'bg-rose-950 text-rose-400 border border-rose-500/20' }}">
                        {{ $center->status }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-brand-teal/10">
                    <div class="text-center">
                        <span class="block text-[10px] text-brand-gray uppercase tracking-wider">Capacity</span>
                        <strong class="text-brand-white">{{ $center->capacity }}</strong>
                    </div>
                    <div class="text-center border-x border-brand-teal/10">
                        <span class="block text-[10px] text-brand-gray uppercase tracking-wider">Seats</span>
                        <strong class="text-brand-white">{{ $center->seats_count }}</strong>
                    </div>
                    <div class="text-center">
                        <span class="block text-[10px] text-brand-gray uppercase tracking-wider">Contact</span>
                        <strong class="text-brand-white text-[10px]">{{ $center->contact_phone }}</strong>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 glass-card rounded-2xl p-12 text-center text-brand-gray">
                No CBT centers registered in the system yet.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $centers->links() }}
    </div>
</div>
@endsection
