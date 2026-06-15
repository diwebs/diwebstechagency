@extends('layouts.admin')

@section('title', 'CBT Centers - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">CBT Center Network</h1>
        <p class="text-sm text-brand-gray mt-1">Overview of all registered physical examination centers and their operational status.</p>
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
