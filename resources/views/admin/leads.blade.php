@extends('layouts.admin')

@section('title', 'Clients & CRM Leads - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Clients &amp; CRM Leads Pipeline</h1>
            <p class="text-sm text-brand-gray mt-1">Manage corporate contract opportunities, project request details, and lead conversions.</p>
        </div>
        <span class="text-xs text-brand-gray">{{ $leads->total() }} total contacts</span>
    </div>

    <!-- Leads table -->
    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Client Detail</th>
                        <th class="px-6 py-4">Service Needed</th>
                        <th class="px-6 py-4">Message / Project Inquiry</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Received</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-brand-white">{{ $lead->name }}</p>
                                <p class="text-[10px] text-brand-gray">{{ $lead->email }}</p>
                                @if($lead->phone)
                                    <p class="text-[10px] text-brand-gray/60 mt-0.5">{{ $lead->phone }}</p>
                                @endif
                                @if($lead->company)
                                    <p class="text-[10px] text-brand-cyan/70 mt-0.5 font-semibold">{{ $lead->company }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[10px] font-semibold text-brand-cyan">
                                    {{ $lead->service_needed }}
                                </span>
                            </td>
                            <td class="px-6 py-4 max-w-sm">
                                <p class="text-brand-gray/90 leading-relaxed line-clamp-3">
                                    {{ $lead->message }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase
                                    @if($lead->status === 'closed' || $lead->status === 'proposal') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                    @elseif($lead->status === 'contacted') bg-blue-950 text-blue-400 border border-blue-500/20
                                    @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/10
                                    @endif">
                                    {{ $lead->status ?? 'new' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">{{ $lead->created_at ? $lead->created_at->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.leads.status', $lead->id) }}" method="POST" class="inline-flex gap-1.5 justify-end">
                                    @csrf
                                    <select name="status" class="bg-brand-dark-secondary border border-brand-teal/15 rounded px-2 py-1 text-[10px] text-brand-gray focus:outline-none focus:border-brand-cyan/40">
                                        <option value="new" {{ ($lead->status ?? 'new') === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="contacted" {{ ($lead->status ?? '') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                        <option value="proposal" {{ ($lead->status ?? '') === 'proposal' ? 'selected' : '' }}>Proposal</option>
                                        <option value="closed" {{ ($lead->status ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    <button type="submit" class="rounded bg-brand-cyan text-brand-dark-secondary px-3 py-1 text-[10px] font-bold hover:opacity-90">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-brand-gray">No CRM leads recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-brand-teal/10">
            {{ $leads->links() }}
        </div>
    </div>
</div>
@endsection
