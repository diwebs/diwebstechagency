@extends('layouts.admin')

@section('title', 'Financial Operations - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Financial Operations &amp; Billing</h1>
        <p class="text-sm text-brand-gray mt-1">Audit billing history, project invoices, payment statuses, and system revenue.</p>
    </div>

    <!-- Financial stats overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 bg-gradient-to-br from-brand-dark-secondary to-emerald-950/20">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Total Certified Revenue</span>
            <strong class="block text-3xl font-extrabold text-emerald-400 mt-2">@money($totalRevenue)</strong>
            <p class="text-[10px] text-brand-gray mt-1.5">Cleared invoices through Stripe and digital escrow payment channels.</p>
        </div>

        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 bg-gradient-to-br from-brand-dark-secondary to-amber-950/20">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Outstanding Invoices</span>
            <strong class="block text-3xl font-extrabold text-amber-400 mt-2">@money($pendingRevenue)</strong>
            <p class="text-[10px] text-brand-gray mt-1.5">Awaiting bank wire clearance or cryptocurrency escrow release logs.</p>
        </div>

        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 bg-gradient-to-br from-brand-dark-secondary to-brand-teal/10">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Estimated Gross Forecast</span>
            <strong class="block text-3xl font-extrabold text-brand-white mt-2">@money($totalRevenue + $pendingRevenue)</strong>
            <p class="text-[10px] text-brand-gray mt-1.5">Estimated gross project valuation sum across the ecosystem.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Invoices lists table -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                                <th class="px-6 py-4">Invoice #</th>
                                <th class="px-6 py-4">Client / Account</th>
                                <th class="px-6 py-4">Billing Item / Project</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Due Date</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Admin Controls</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                                    <td class="px-6 py-4 font-mono font-bold text-brand-white">{{ $invoice->invoice_number }}</td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-brand-white">{{ $invoice->client->name }}</p>
                                        <p class="text-[10px] text-brand-gray">{{ $invoice->client->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-brand-gray">
                                        @if($invoice->title)
                                            <div class="font-semibold text-brand-white">{{ $invoice->title }}</div>
                                            @if($invoice->project)
                                                <div class="text-[9px] text-brand-cyan mt-0.5">Project: {{ $invoice->project->title }}</div>
                                            @endif
                                        @else
                                            {{ $invoice->project ? $invoice->project->title : 'LMS / CBT Direct Enrollment' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-brand-white">@money($invoice->amount)</td>
                                    <td class="px-6 py-4 text-brand-gray">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded px-2.5 py-1 text-[9px] font-bold uppercase
                                            @if($invoice->status === 'paid') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                            @elseif($invoice->status === 'pending' || $invoice->status === 'unpaid') bg-amber-950 text-amber-400 border border-amber-500/20
                                            @else bg-rose-950 text-rose-400 border border-rose-500/20
                                            @endif">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.finance.invoice.status', $invoice->id) }}" method="POST" class="inline-flex gap-1.5 justify-end">
                                            @csrf
                                            <select name="status" class="bg-brand-dark-secondary border border-brand-teal/15 rounded px-2 py-1 text-[10px] text-brand-gray focus:outline-none focus:border-brand-cyan/40">
                                                <option value="unpaid" {{ $invoice->status === 'unpaid' ? 'selected' : '' }}>Unpaid / Pending</option>
                                                <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                            </select>
                                            <button type="submit" class="rounded bg-brand-cyan text-brand-dark-secondary px-3 py-1 text-[10px] font-bold hover:opacity-90">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-brand-gray">No billing invoices found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-brand-teal/10">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>

        <!-- Right: Create Custom Invoice Form -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 h-fit space-y-4">
            <div>
                <h3 class="text-sm font-bold text-brand-cyan uppercase tracking-wider">Generate Custom Invoice</h3>
                <p class="text-[10px] text-brand-gray mt-1">Create a standalone invoice for client requests or custom contract agreement items.</p>
            </div>
            
            <form action="{{ route('admin.finance.invoice.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Select Target Client</label>
                    <select name="client_id" required class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none transition-all">
                        <option value="" disabled selected>-- Select Client Account --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Associated Project (Optional)</label>
                    <select name="project_id" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none transition-all">
                        <option value="">-- No Project (Standalone Billing) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">[{{ $p->client->name }}] {{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Invoice Reference Number</label>
                    <input type="text" name="invoice_number" required value="INV-{{ date('Y') }}-{{ strtoupper(Str::random(5)) }}" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>

                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Invoice Billing Title</label>
                    <input type="text" name="title" required placeholder="e.g. Dashboard Redesign Sprint" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>

                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Detailed Description / Agreement Terms</label>
                    <textarea name="description" rows="3" placeholder="Define exact scope (e.g. Additional frontend sprint terms approved by client...)" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none transition-all"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Amount ($)</label>
                        <input type="number" step="0.01" min="0" name="amount" required placeholder="1250.00" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Due Date</label>
                        <input type="date" name="due_date" required value="{{ now()->addDays(7)->format('Y-m-d') }}" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    💳 Generate &amp; Sync Invoice
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
