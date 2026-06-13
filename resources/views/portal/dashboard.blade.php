@extends('layouts.app')

@section('title', 'Client Portal Dashboard - Project Tracking')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mb-10">
        <h1 class="text-3xl font-bold tracking-tight text-brand-white">Client Dashboard</h1>
        <p class="text-sm text-brand-gray mt-1">Track software development progress, view project budgets, and settle pending milestone invoices.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Client Projects -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">Active Engagements</h3>
            
            @forelse($projects as $project)
                <div class="glass-card rounded-2xl p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-brand-cyan/5 rounded-full blur-xl"></div>
                    
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[10px] text-brand-cyan uppercase font-bold">{{ strtoupper($project->status) }}</span>
                            <h4 class="text-xl font-bold text-brand-white mt-2">{{ $project->title }}</h4>
                            <p class="text-xs text-brand-gray mt-1 max-w-md">{{ $project->description }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] uppercase text-brand-gray tracking-wider">Budget Allocation</span>
                            <strong class="text-lg font-bold text-brand-white">${{ number_format($project->budget, 2) }}</strong>
                        </div>
                    </div>

                    <!-- Milestone Mini-Tracker -->
                    <div class="mt-6 border-t border-brand-teal/10 pt-6">
                        <h5 class="text-xs font-bold text-brand-white mb-3">Milestone Progress</h5>
                        <div class="space-y-2">
                            @foreach($project->milestones as $milestone)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="h-1.5 w-1.5 rounded-full 
                                            @if($milestone->status === 'approved') bg-emerald-400
                                            @elseif($milestone->status === 'working') bg-brand-cyan animate-pulse
                                            @else bg-brand-gray
                                            @endif">
                                        </span>
                                        <span class="text-brand-gray">{{ $milestone->title }}</span>
                                    </div>
                                    <span class="capitalize font-semibold 
                                        @if($milestone->status === 'approved') text-emerald-400
                                        @elseif($milestone->status === 'working') text-brand-cyan
                                        @else text-brand-gray
                                        @endif">
                                        {{ $milestone->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 pt-4 flex justify-between items-center border-t border-brand-teal/5">
                        <span class="text-xs text-brand-gray">Agreement Status: 
                            <strong class="{{ $project->agreement_signed_at ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $project->agreement_signed_at ? 'Signed' : 'Pending Signature' }}
                            </strong>
                        </span>
                        
                        <a href="{{ route('portal.project', $project->id) }}" class="rounded bg-brand-teal/10 border border-brand-teal/30 hover:bg-brand-teal/20 px-4 py-2 text-xs font-bold text-brand-cyan transition-all">
                            Manage Project Details →
                        </a>
                    </div>
                </div>
            @empty
                <div class="glass-card rounded-2xl p-8 text-center text-brand-gray text-sm">
                    No active development contracts found in your account.
                </div>
            @endforelse
        </div>

        <!-- Right: Unpaid Invoices & Quick Pay -->
        <div class="space-y-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">Unpaid Milestone Invoices</h3>
            
            <div class="glass-card rounded-2xl p-6 space-y-4">
                @forelse($unpaidInvoices as $invoice)
                    <div class="border-b border-brand-teal/10 pb-4 last:border-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] text-brand-cyan font-bold uppercase tracking-wider">Invoice #{{ $invoice->invoice_number }}</span>
                                <h4 class="text-xs font-bold text-brand-white mt-1">{{ $invoice->project->title }}</h4>
                                <p class="text-[10px] text-brand-gray">Due Date: {{ $invoice->due_date->format('M d, Y') }}</p>
                            </div>
                            <strong class="text-sm font-mono text-brand-white">${{ number_format($invoice->amount, 2) }}</strong>
                        </div>
                        
                        <form action="{{ route('portal.invoice.pay', $invoice->id) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full rounded bg-gradient-to-r from-brand-teal to-brand-cyan py-2 text-center text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                                settle invoice (mock checkout)
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">All billing invoices settled. Excellent!</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
