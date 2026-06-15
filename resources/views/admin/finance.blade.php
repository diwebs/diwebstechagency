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

    <!-- Invoices lists table -->
    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Invoice #</th>
                        <th class="px-6 py-4">Client / Account</th>
                        <th class="px-6 py-4">Project Module</th>
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
                                {{ $invoice->project ? $invoice->project->title : 'LMS / CBT Direct Enrollment' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-brand-white">@money($invoice->amount)</td>
                            <td class="px-6 py-4 text-brand-gray">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2.5 py-1 text-[9px] font-bold uppercase
                                    @if($invoice->status === 'paid') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                    @elseif($invoice->status === 'pending') bg-amber-950 text-amber-400 border border-amber-500/20
                                    @else bg-rose-950 text-rose-400 border border-rose-500/20
                                    @endif">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.finance.invoice.status', $invoice->id) }}" method="POST" class="inline-flex gap-1.5 justify-end">
                                    @csrf
                                    <select name="status" class="bg-brand-dark-secondary border border-brand-teal/15 rounded px-2 py-1 text-[10px] text-brand-gray focus:outline-none focus:border-brand-cyan/40">
                                        <option value="pending" {{ $invoice->status === 'pending' ? 'selected' : '' }}>Pending</option>
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
@endsection
