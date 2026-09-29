@extends('layouts.admin')

@section('title', 'Leads Management - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddLead: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Leads Pipeline</h1>
            <p class="text-xs text-brand-gray mt-1">Capture, score, and qualify prospective clients.</p>
        </div>
        <button @click="showAddLead = !showAddLead" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Capture Manual Lead
        </button>
    </div>

    <!-- Manual Lead Form -->
    <div x-show="showAddLead" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Record Lead Intake</h3>
        <form action="{{ route('admin.crm.leads.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Full Name</label>
                <input type="text" name="full_name" required placeholder="John Doe" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Company</label>
                <input type="text" name="company_name" placeholder="Enterprise Group LLC" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Email</label>
                <input type="email" name="email" required placeholder="johndoe@email.com" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Phone</label>
                <input type="text" name="phone" placeholder="+1 (555) 123-4567" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Source</label>
                <select name="source" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="website forms">Website Forms</option>
                    <option value="contact page">Contact Page</option>
                    <option value="newsletter">Newsletter Subscription</option>
                    <option value="chatbot">Intelligent Chatbot</option>
                    <option value="API webhook">External API Webhook</option>
                    <option value="manual entry" selected>Manual Entry</option>
                    <option value="referrals">Referral Code</option>
                    <option value="social media campaigns">Social Media Campaign</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Service of Interest</label>
                <input type="text" name="service_interest" placeholder="AI Automations / Cloud Infrastructure" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Budget Range</label>
                <input type="text" name="budget_range" placeholder="₦5,000,000 - ₦10,000,000" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Country</label>
                <input type="text" name="country" placeholder="Nigeria" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Assign Staff Manager</label>
                <select name="assigned_staff_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">Leave Unassigned</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->role->title ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Intake Notes</label>
                <textarea name="notes" rows="2" placeholder="Briefly describe customer background requirements..." class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none"></textarea>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddLead = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Register Lead</button>
            </div>
        </form>
    </div>

    <!-- Leads Listing -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Ecosystem Leads Pipeline</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Lead Info</th>
                        <th class="pb-3">Company &amp; Location</th>
                        <th class="pb-3">Source &amp; Interest</th>
                        <th class="pb-3">Lead Score</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Manager</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors">
                            <td class="py-3">
                                <strong class="block text-brand-white text-sm">{{ $lead->full_name }}</strong>
                                <span class="text-brand-gray/60 block">{{ $lead->email }}</span>
                                <span class="text-brand-gray/60 block">{{ $lead->phone ?? 'No Phone' }}</span>
                            </td>
                            <td class="py-3 text-brand-white">
                                <span class="block">{{ $lead->company_name ?? 'Individual' }}</span>
                                <span class="text-brand-gray text-[10px]">{{ $lead->country ?? 'N/A' }}</span>
                            </td>
                            <td class="py-3 text-brand-white">
                                <span class="block font-semibold text-brand-cyan">{{ ucfirst($lead->source) }}</span>
                                <span class="text-brand-gray text-[10px]">{{ $lead->service_interest ?? 'Not Specified' }}</span>
                            </td>
                            <td class="py-3 font-mono font-bold text-center">
                                <span class="inline-block rounded px-2 py-0.5
                                    @if($lead->lead_score >= 60) bg-emerald-950 text-emerald-400
                                    @elseif($lead->lead_score >= 30) bg-brand-teal/15 text-brand-cyan
                                    @else bg-brand-dark-secondary text-brand-gray
                                    @endif">
                                    {{ $lead->lead_score }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase
                                    @if($lead->status === 'Won') bg-emerald-950 text-emerald-400
                                    @elseif($lead->status === 'Lost') bg-rose-950 text-rose-400
                                    @elseif($lead->status === 'Qualified') bg-brand-teal/20 text-brand-cyan
                                    @else bg-brand-teal/10 text-brand-white
                                    @endif">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td class="py-3 text-brand-gray">
                                {{ $lead->assignedStaff ? $lead->assignedStaff->name : 'Unassigned' }}
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($lead->status !== 'Won')
                                        <form action="{{ route('admin.crm.leads.convert', $lead->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded bg-brand-cyan text-brand-dark-secondary px-2 py-1 text-[10px] font-bold cursor-pointer" title="Convert to client">
                                                Convert
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.crm.leads.delete', $lead->id) }}" method="POST" onsubmit="return confirm('Archive lead permanently?');">
                                        @csrf
                                        <button type="submit" class="rounded bg-rose-500/10 border border-rose-500/20 text-rose-400 px-2 py-1 text-[10px] font-bold cursor-pointer" title="Delete lead">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-brand-gray">No lead channels found. Use the Intake form to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $leads->links() }}
        </div>
    </div>
</div>
@endsection
