@extends('layouts.cbt')

@section('title', 'Revenue & Financial Statements - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Revenue & Financial Statements</h1>
            <p class="text-sm text-brand-gray mt-1">Track accrued examinee commissions, scheduled payout dates, and fiscal statements.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/25 px-4 py-2 rounded-xl">
            <span class="text-xs text-amber-400 font-bold">💰 Next Payout: June 25, 2026</span>
        </div>
    </div>

    <!-- Financial stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Total Revenue Accrued</span>
            <strong class="block text-3xl font-bold text-amber-400 mt-2">${{ number_format($center->revenue, 2) }}</strong>
            <p class="text-[10px] text-brand-gray mt-2">Commission rate: <strong class="text-brand-cyan">{{ $center->commission_rate }}%</strong> per candidate seat validation.</p>
        </div>

        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Pending Release</span>
            <strong class="block text-3xl font-bold text-brand-white mt-2">$750.00</strong>
            <p class="text-[10px] text-brand-gray mt-2">Held for scheduled June ledger batch cycle release.</p>
        </div>

        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="block text-[10px] font-bold text-brand-gray uppercase tracking-wider">Lifetime Payouts</span>
            <strong class="block text-3xl font-bold text-emerald-400 mt-2">$3,750.00</strong>
            <p class="text-[10px] text-brand-gray mt-2">Transferred to partner checking routing account.</p>
        </div>
    </div>

    <!-- Payout logs table -->
    <div class="space-y-4">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Payout & Settlement History</h3>
        
        <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/10">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-brand-dark-secondary/40 text-brand-gray border-b border-brand-teal/10">
                        <th class="p-4 font-bold">Settlement ID</th>
                        <th class="p-4 font-bold">Ledger Period</th>
                        <th class="p-4 font-bold">Commission Model</th>
                        <th class="p-4 font-bold">Gross Amount</th>
                        <th class="p-4 font-bold">Payout Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    <tr class="hover:bg-brand-teal/5 transition-all">
                        <td class="p-4 font-mono font-bold text-brand-cyan">SET-00281</td>
                        <td class="p-4">May 01 - May 31, 2026</td>
                        <td class="p-4">15% Commission (Lagos Hub)</td>
                        <td class="p-4 font-mono font-bold">$1,250.00</td>
                        <td class="p-4">
                            <span class="inline-block rounded bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-bold text-emerald-400">DISBURSED</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-brand-teal/5 transition-all">
                        <td class="p-4 font-mono font-bold text-brand-cyan">SET-00249</td>
                        <td class="p-4">Apr 01 - Apr 30, 2026</td>
                        <td class="p-4">15% Commission (Lagos Hub)</td>
                        <td class="p-4 font-mono font-bold">$1,500.00</td>
                        <td class="p-4">
                            <span class="inline-block rounded bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-bold text-emerald-400">DISBURSED</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-brand-teal/5 transition-all">
                        <td class="p-4 font-mono font-bold text-brand-cyan">SET-00199</td>
                        <td class="p-4">Mar 01 - Mar 31, 2026</td>
                        <td class="p-4">15% Commission (Lagos Hub)</td>
                        <td class="p-4 font-mono font-bold">$1,000.00</td>
                        <td class="p-4">
                            <span class="inline-block rounded bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-bold text-emerald-400">DISBURSED</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
