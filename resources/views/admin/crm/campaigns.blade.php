@extends('layouts.admin')

@section('title', 'Marketing Campaigns - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddCampaign: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Marketing Campaigns</h1>
            <p class="text-xs text-brand-gray mt-1">Configure promotions, email campaigns, referral cards, and ad budgets.</p>
        </div>
        <button @click="showAddCampaign = !showAddCampaign" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Setup Campaign
        </button>
    </div>

    <!-- Create Campaign Form -->
    <div x-show="showAddCampaign" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Launch Promo Campaign</h3>
        <form action="{{ route('admin.crm.campaigns.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Campaign Name</label>
                <input type="text" name="name" required placeholder="Summer 2026 Tech Discount" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Campaign Type</label>
                <select name="campaign_type" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="email">Email Newsletter Blast</option>
                    <option value="ad">Pay-Per-Click Ad Campaign</option>
                    <option value="referral">Referral Bonus Program</option>
                    <option value="promotion">Seasonal Discount Promo</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Status</label>
                <select name="status" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Planning" selected>Planning</option>
                    <option value="Active">Active</option>
                    <option value="Paused">Paused</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Budget Allocated (₦)</label>
                <input type="number" name="budget" required placeholder="150000.00" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Budget Spent (₦)</label>
                <input type="number" name="spent" required placeholder="0.00" value="0.00" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Target Audience Demographics</label>
                <input type="text" name="target_audience" placeholder="SaaS Startups / Lagos Tech Founders" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddCampaign = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Initialize Campaign</button>
            </div>
        </form>
    </div>

    <!-- Campaigns list -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Marketing Campaigns Registry</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Campaign Name</th>
                        <th class="pb-3">Type</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Budget</th>
                        <th class="pb-3">Spent</th>
                        <th class="pb-3">Metrics Overview</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors text-brand-white">
                            <td class="py-4">
                                <strong class="text-sm block">{{ $camp->name }}</strong>
                                <span class="text-brand-gray text-[10px] block">Audience: {{ $camp->target_audience ?? 'Broad Reach' }}</span>
                            </td>
                            <td class="py-4 text-brand-cyan font-bold">{{ ucfirst($camp->campaign_type) }}</td>
                            <td class="py-4">
                                <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase
                                    @if($camp->status === 'Active') bg-emerald-950 text-emerald-400
                                    @elseif($camp->status === 'Completed') bg-brand-dark text-brand-gray
                                    @else bg-brand-teal/10 text-brand-white
                                    @endif">
                                    {{ $camp->status }}
                                </span>
                            </td>
                            <td class="py-4 font-mono">₦{{ number_format($camp->budget, 2) }}</td>
                            <td class="py-4 font-mono text-brand-gray">₦{{ number_format($camp->spent, 2) }}</td>
                            <td class="py-4 max-w-xs text-[11px] leading-relaxed">
                                @if(is_array($camp->metrics))
                                    <div class="grid grid-cols-2 gap-2 text-brand-gray font-mono">
                                        <span>Open Rate: <strong class="text-brand-white">{{ $camp->metrics['open_rate'] ?? '0%' }}</strong></span>
                                        <span>CTR: <strong class="text-brand-white">{{ $camp->metrics['ctr'] ?? '0%' }}</strong></span>
                                        <span>Conversions: <strong class="text-brand-white">{{ $camp->metrics['conversions'] ?? 0 }}</strong></span>
                                        <span>ROI: <strong class="text-emerald-400">{{ $camp->metrics['roi'] ?? '0%' }}</strong></span>
                                    </div>
                                @else
                                    <span class="text-brand-gray/50">Metrics processing...</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-brand-gray">No campaign initiatives registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
@endsection
