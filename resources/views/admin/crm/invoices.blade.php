@extends('layouts.admin')

@section('title', 'CRM Invoices - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddInvoice: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Invoice &amp; Billing Management</h1>
            <p class="text-xs text-brand-gray mt-1">Issue customer billing, configure tax rates, track payments, and verify balances.</p>
        </div>
        <button @click="showAddInvoice = !showAddInvoice" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Issue Invoice
        </button>
    </div>

    <!-- Create Invoice Form -->
    <div x-show="showAddInvoice" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Generate Billing Invoice</h3>
        <form action="{{ route('admin.crm.invoices.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs" x-data="{ amount: 0, taxRate: 0.075 }">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Invoice number</label>
                <input type="text" name="invoice_number" required placeholder="DWS-INV-2026-001" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Client Target</label>
                <select name="crm_client_id" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">Select Client</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->contact_person }} ({{ $cl->company_name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">B2B Company Link</label>
                <select name="crm_company_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">No Corporate Link</option>
                    @foreach($companies as $cp)
                        <option value="{{ $cp->id }}">{{ $cp->organization_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Billing Title</label>
                <input type="text" name="title" required placeholder="Milestone 1 Deliverables" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Amount (₦)</label>
                <input type="number" name="amount" step="0.01" required placeholder="450000.00" x-model.number="amount" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">VAT / Tax Rate (%)</label>
                <select x-model.number="taxRate" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="0">No Tax (0%)</option>
                    <option value="0.05">VAT (5%)</option>
                    <option value="0.075" selected>VAT (7.5%)</option>
                    <option value="0.1">Tax (10%)</option>
                </select>
                <input type="hidden" name="tax" :value="(amount * taxRate).toFixed(2)">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Due Date</label>
                <input type="date" name="due_date" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Recurring Invoice</label>
                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" name="is_recurring" value="1" class="rounded bg-brand-dark border-brand-teal/20 text-brand-cyan focus:ring-0">
                    <span class="text-brand-gray">Enable Recurring Subscription</span>
                </div>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Billing Interval</label>
                <select name="billing_interval" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">One-Off Bill</option>
                    <option value="monthly">Monthly Cycle</option>
                    <option value="quarterly">Quarterly Cycle</option>
                    <option value="yearly">Yearly Cycle</option>
                </select>
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Invoice Description</label>
                <textarea name="description" rows="2" placeholder="Briefly specify items and services..." class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none"></textarea>
            </div>
            
            <div class="col-span-1 md:col-span-3 bg-brand-dark-secondary/30 rounded-xl p-3 border border-brand-teal/10 flex justify-between items-center">
                <span class="text-brand-gray">Total Valuation (Calculated):</span>
                <strong class="text-lg text-emerald-400 font-mono" x-text="'₦' + (amount + (amount * taxRate)).toLocaleString(undefined, {minimumFractionDigits: 2})"></strong>
            </div>

            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddInvoice = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Generate Invoice</button>
            </div>
        </form>
    </div>

    <!-- Invoices List Table -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Billing Registry</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Invoice Details</th>
                        <th class="pb-3">Client</th>
                        <th class="pb-3">Base Amount</th>
                        <th class="pb-3">Tax (VAT)</th>
                        <th class="pb-3">Total Amount</th>
                        <th class="pb-3">Due Date</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors text-brand-white">
                            <td class="py-4">
                                <strong class="block text-sm">{{ $inv->title }}</strong>
                                <span class="text-brand-gray/60 font-mono text-[9px]">{{ $inv->invoice_number }}</span>
                                @if($inv->is_recurring)
                                    <span class="rounded bg-brand-cyan/10 text-brand-cyan text-[8px] px-1 py-0.5 font-bold uppercase">{{ $inv->billing_interval }}</span>
                                @endif
                            </td>
                            <td class="py-4">
                                <span class="block">{{ $inv->client->contact_person }}</span>
                                <span class="text-brand-gray text-[10px]">{{ $inv->client->company_name }}</span>
                            </td>
                            <td class="py-4 font-mono font-bold">₦{{ number_format($inv->amount, 2) }}</td>
                            <td class="py-4 font-mono text-brand-gray">₦{{ number_format($inv->tax, 2) }}</td>
                            <td class="py-4 font-mono font-bold text-emerald-400">₦{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="py-4 font-mono text-brand-gray">{{ $inv->due_date->format('Y-m-d') }}</td>
                            <td class="py-4">
                                <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase
                                    @if($inv->status === 'Paid') bg-emerald-950 text-emerald-400
                                    @elseif($inv->status === 'Overdue') bg-rose-950 text-rose-400
                                    @elseif($inv->status === 'Sent') bg-brand-teal/20 text-brand-cyan
                                    @else bg-brand-dark text-brand-gray
                                    @endif">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                @if($inv->status !== 'Paid')
                                    <form action="{{ route('admin.crm.invoices.status', $inv->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="Paid">
                                        <button type="submit" class="rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-2 py-1 text-[10px] font-bold cursor-pointer">
                                            Mark Paid
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-brand-gray">No billing transactions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
