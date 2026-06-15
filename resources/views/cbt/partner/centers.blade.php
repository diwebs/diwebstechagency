@extends('layouts.cbt')

@section('title', 'My Certified CBT Centers - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">My Certified CBT Centers</h1>
            <p class="text-sm text-brand-gray mt-1">Review operational specifications, status audits, and seat routing for your locations.</p>
        </div>
        
        <a href="{{ route('cbt.center-enrollment') }}" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all select-none">
            ➕ Register Additional Center
        </a>
    </div>

    <!-- Centers List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($centers as $center)
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4 hover:border-brand-cyan/40 transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="inline-block text-[9px] font-extrabold uppercase tracking-wider text-brand-cyan bg-[#1A1D21] border border-brand-teal/10 px-2.5 py-1 rounded-full mb-2">
                            {{ strtoupper($center->center_type ?? 'standard') }} CENTER
                        </span>
                        <h3 class="text-lg font-bold text-brand-white">{{ $center->name }}</h3>
                        <p class="text-xs text-brand-cyan font-mono mt-0.5">Code: {{ $center->code }}</p>
                    </div>
                    
                    <span class="inline-flex items-center gap-1 rounded bg-emerald-500/10 border border-emerald-500/30 px-2.5 py-1 text-xs font-bold text-emerald-400">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 border-y border-brand-teal/5 py-4 my-2 text-xs">
                    <div class="space-y-1">
                        <span class="text-brand-gray block text-[10px] uppercase font-bold tracking-wider">Address</span>
                        <span class="text-brand-white block">{{ $center->address }}, {{ $center->city }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-brand-gray block text-[10px] uppercase font-bold tracking-wider">Contact</span>
                        <span class="text-brand-white block">{{ $center->contact_phone }}</span>
                        <span class="text-brand-gray block text-[10px]">{{ $center->contact_email }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5">
                        <span class="text-brand-gray block text-[9px] uppercase tracking-wider">Capacity</span>
                        <strong class="text-brand-white">{{ $center->capacity }} Seats</strong>
                    </div>
                    <div class="p-2 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5">
                        <span class="text-brand-gray block text-[9px] uppercase tracking-wider">Commission</span>
                        <strong class="text-brand-white">{{ $center->commission_rate }}%</strong>
                    </div>
                    <div class="p-2 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5">
                        <span class="text-brand-gray block text-[9px] uppercase tracking-wider">Accrued Rev</span>
                        <strong class="text-amber-400">${{ number_format($center->revenue, 2) }}</strong>
                    </div>
                </div>

                <div class="border-t border-brand-teal/5 pt-4 flex gap-3">
                    <a href="{{ route('cbt.partner.centers.seats', $center->id) }}" class="flex-1 text-center rounded bg-brand-teal/15 hover:bg-brand-teal/25 border border-brand-teal/30 py-2 text-xs font-bold text-brand-cyan transition-all">
                        💺 View Seats Layout
                    </a>
                    <a href="{{ route('cbt.partner.revenue') }}" class="flex-1 text-center rounded bg-brand-dark-secondary hover:bg-brand-dark-secondary/80 border border-brand-teal/10 py-2 text-xs font-bold text-brand-white transition-all">
                        💳 Financial Statements
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 glass-card rounded-2xl p-12 text-center border border-dashed border-brand-teal/20 text-brand-gray text-sm">
                <span class="text-4xl block mb-3">🏢</span>
                No certified physical centers registered under your partner account.
                <div class="mt-4">
                    <a href="{{ route('cbt.center-enrollment') }}" class="inline-block rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all">
                        Submit Center Application &rarr;
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
