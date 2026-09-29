@extends('layouts.admin')

@section('title', 'Corporate Accounts - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddCompany: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Corporate Accounts (B2B)</h1>
            <p class="text-xs text-brand-gray mt-1">Manage B2B agency relationships, partnership layers, and company valuation data.</p>
        </div>
        <button @click="showAddCompany = !showAddCompany" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Register Corporate Account
        </button>
    </div>

    <!-- Company Add Form -->
    <div x-show="showAddCompany" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">New Company Account</h3>
        <form action="{{ route('admin.crm.companies.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Organization Name</label>
                <input type="text" name="organization_name" required placeholder="Vanguard Logistics" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Industry Sector</label>
                <input type="text" name="industry" placeholder="Logistics &amp; Shipping" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Annual Contract Value (₦)</label>
                <input type="number" name="annual_value" step="0.01" required placeholder="5000000.00" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Relationship Stage</label>
                <select name="relationship_stage" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Lead">Lead Intake</option>
                    <option value="Prospect">Qualified Prospect</option>
                    <option value="Active Client" selected>Active Client</option>
                    <option value="Partner">Corporate Partner</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Primary Corporate Contact</label>
                <input type="text" name="corporate_contact" placeholder="Marcus Vance (CFO)" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Partnership Level</label>
                <select name="partnership_level" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Standard">Standard Client</option>
                    <option value="Silver">Silver Tier Partner</option>
                    <option value="Gold" selected>Gold Tier Partner</option>
                    <option value="Platinum">Platinum Enterprise Tier</option>
                </select>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddCompany = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Save Corporate Profile</button>
            </div>
        </form>
    </div>

    <!-- Companies list -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Corporate Accounts Registry</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Organization Name</th>
                        <th class="pb-3">Industry</th>
                        <th class="pb-3">Primary Contact</th>
                        <th class="pb-3">Annual Contract Value</th>
                        <th class="pb-3">Relationship Stage</th>
                        <th class="pb-3">Partnership Level</th>
                        <th class="pb-3">Active Clients Linked</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($companies as $company)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors text-brand-white">
                            <td class="py-3.5">
                                <strong class="text-sm">{{ $company->organization_name }}</strong>
                            </td>
                            <td class="py-3.5 text-brand-gray">{{ $company->industry ?? 'N/A' }}</td>
                            <td class="py-3.5">{{ $company->corporate_contact ?? 'N/A' }}</td>
                            <td class="py-3.5 font-mono text-brand-cyan font-bold">₦{{ number_format($company->annual_value, 2) }}</td>
                            <td class="py-3.5">
                                <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-brand-teal/15 text-brand-cyan">
                                    {{ $company->relationship_stage }}
                                </span>
                            </td>
                            <td class="py-3.5 font-bold">
                                <span class="text-amber-400">★</span> {{ $company->partnership_level ?? 'Standard' }}
                            </td>
                            <td class="py-3.5 text-center font-mono font-bold text-brand-cyan">{{ $company->clients_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-brand-gray">No corporate B2B registry records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $companies->links() }}
        </div>
    </div>
</div>
@endsection
