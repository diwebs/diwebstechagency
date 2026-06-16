@extends('layouts.admin')

@section('title', 'Referral Transactions - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Referral Tracker &amp; Programs</h1>
            <p class="text-sm text-brand-gray mt-1 font-medium">Verify client referral actions, settle payout bonuses, and configure default reward criteria.</p>
        </div>
    </div>

    <!-- Referral Analytics Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-brand-gray tracking-wider">Settled &amp; Paid Bonuses</span>
                <div class="text-3xl font-extrabold text-emerald-400 mt-1">@money($totalPaid)</div>
            </div>
            <span class="text-2xl">💰</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-brand-gray tracking-wider">Approved Bonuses (Unpaid)</span>
                <div class="text-3xl font-extrabold text-brand-cyan mt-1">@money($totalApproved)</div>
            </div>
            <span class="text-2xl">✓</span>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-brand-gray tracking-wider">Pending Verification</span>
                <div class="text-3xl font-extrabold text-amber-400 mt-1">@money($totalPending)</div>
            </div>
            <span class="text-2xl animate-pulse">⏳</span>
        </div>
    </div>

    <!-- Referrals Data Table -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-5 pb-3 border-b border-brand-teal/10">All Client Referrals</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="text-[10px] uppercase font-bold text-brand-gray border-b border-brand-teal/10">
                    <tr>
                        <th class="py-3.5 px-4">Referrer (Client)</th>
                        <th class="py-3.5 px-4">Referee (New Client)</th>
                        <th class="py-3.5 px-4">Signup Date</th>
                        <th class="py-3.5 px-4">Bonus Amount</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($referrals as $ref)
                        <tr>
                            <td class="py-4 px-4">
                                <div class="font-semibold">{{ $ref->referrer->name }}</div>
                                <div class="text-[10px] text-brand-gray font-mono mt-0.5">{{ $ref->referrer->email }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold">{{ $ref->referee->name }}</div>
                                <div class="text-[10px] text-brand-gray font-mono mt-0.5">{{ $ref->referee->email }}</div>
                            </td>
                            <td class="py-4 px-4">{{ $ref->created_at->format('M d, Y H:i') }}</td>
                            <td class="py-4 px-4 font-mono font-bold text-brand-cyan">@money($ref->bonus_amount)</td>
                            <td class="py-4 px-4">
                                <span class="rounded px-2.5 py-0.5 text-[9px] uppercase font-bold 
                                    @if($ref->status === 'paid') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @elseif($ref->status === 'approved') bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                                    @elseif($ref->status === 'void') bg-rose-500/10 text-rose-400 border border-rose-500/20
                                    @else bg-amber-500/10 text-amber-400 border border-amber-500/20
                                    @endif">
                                    {{ $ref->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <!-- Quick Pay action -->
                                    @if($ref->status !== 'paid' && $ref->status !== 'void')
                                        <form action="{{ route('admin.referrals.pay', $ref->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded bg-emerald-500/10 border border-emerald-500/35 hover:bg-emerald-500/25 text-emerald-400 font-bold px-2 py-1 text-[10px] uppercase cursor-pointer transition-all">
                                                Paid
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Status dropdown update -->
                                    <form action="{{ route('admin.referrals.status', $ref->id) }}" method="POST" class="inline-flex">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="rounded bg-brand-dark-primary border border-brand-teal/15 text-[10px] text-brand-white py-1 px-2 focus:outline-none cursor-pointer">
                                            <option value="pending" {{ $ref->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $ref->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="paid" {{ $ref->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="void" {{ $ref->status === 'void' ? 'selected' : '' }}>Void</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-brand-gray text-xs">No client referral transactions have been tracked.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
            <div class="mt-6 border-t border-brand-teal/10 pt-4">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
