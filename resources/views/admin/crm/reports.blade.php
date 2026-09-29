@extends('layouts.admin')

@section('title', 'Forecasting & Reports - Diwebs CRM')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Forecasting &amp; Analytics</h1>
            <p class="text-xs text-brand-gray mt-1">Review pipeline summaries and extract ledger sheets for external audit.</p>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Total Sales Valuation</span>
            <strong class="block text-2xl font-bold text-emerald-400 mt-2 font-mono">₦{{ number_format($dealSum, 2) }}</strong>
            <span class="text-[9px] text-brand-cyan block mt-1">All pipeline deals volume</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Total Invoiced Billing</span>
            <strong class="block text-2xl font-bold text-brand-white mt-2 font-mono">₦{{ number_format($invoiceSum, 2) }}</strong>
            <span class="text-[9px] text-brand-cyan block mt-1">All paid invoices volume</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Leads Density</span>
            <strong class="block text-2xl font-bold text-brand-white mt-2 font-mono">{{ $leadCount }} Leads</strong>
            <span class="text-[9px] text-brand-cyan block mt-1">Total leads captured</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="block text-[9px] uppercase font-bold text-brand-gray tracking-wider">Clients Base</span>
            <strong class="block text-2xl font-bold text-brand-cyan mt-2 font-mono">{{ $clientCount }} Accounts</strong>
            <span class="text-[9px] text-brand-gray block mt-1">Active client records</span>
        </div>
    </div>

    <!-- Export Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Available Export Categories -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 space-y-4">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Export Data Ledgers</h3>
            <p class="text-xs text-brand-gray">Select a category to stream a live CSV spreadsheet directly from the database:</p>
            
            <div class="grid grid-cols-2 gap-3 text-xs">
                <a href="{{ route('admin.crm.reports.export', ['type' => 'leads']) }}" class="flex items-center justify-between rounded-lg bg-brand-dark-secondary/60 border border-brand-teal/15 p-3 hover:border-brand-cyan transition-all text-brand-white font-bold select-none cursor-pointer">
                    <span>🎯 Leads Sheet</span>
                    <span class="text-[10px] text-brand-cyan">CSV ↙</span>
                </a>
                <a href="{{ route('admin.crm.reports.export', ['type' => 'clients']) }}" class="flex items-center justify-between rounded-lg bg-brand-dark-secondary/60 border border-brand-teal/15 p-3 hover:border-brand-cyan transition-all text-brand-white font-bold select-none cursor-pointer">
                    <span>👥 Clients Sheet</span>
                    <span class="text-[10px] text-brand-cyan">CSV ↙</span>
                </a>
                <a href="{{ route('admin.crm.reports.export', ['type' => 'deals']) }}" class="flex items-center justify-between rounded-lg bg-brand-dark-secondary/60 border border-brand-teal/15 p-3 hover:border-brand-cyan transition-all text-brand-white font-bold select-none cursor-pointer">
                    <span>⚡ Deals Sheet</span>
                    <span class="text-[10px] text-brand-cyan">CSV ↙</span>
                </a>
                <a href="{{ route('admin.crm.reports.export', ['type' => 'general']) }}" class="flex items-center justify-between rounded-lg bg-brand-dark-secondary/60 border border-brand-teal/15 p-3 hover:border-brand-cyan transition-all text-brand-white font-bold select-none cursor-pointer">
                    <span>📊 Summaries</span>
                    <span class="text-[10px] text-brand-cyan">CSV ↙</span>
                </a>
            </div>
        </div>

        <!-- Export Log History -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Export Audit Log</h3>
            <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                @forelse($reports as $rep)
                    <div class="flex justify-between items-center text-xs border-b border-brand-teal/5 pb-2 last:border-0 last:pb-0">
                        <div>
                            <strong class="block text-brand-white">{{ $rep->name }}</strong>
                            <span class="text-brand-gray text-[10px]">Type: {{ ucfirst($rep->report_type) }}</span>
                        </div>
                        <span class="text-brand-gray/60 text-[10px] font-mono">{{ $rep->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No report generation events logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
