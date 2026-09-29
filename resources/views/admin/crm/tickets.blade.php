@extends('layouts.admin')

@section('title', 'CRM Support Queue - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showLogTicket: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Support Tickets</h1>
            <p class="text-xs text-brand-gray mt-1">Manage technical errors, billing queries, complaints, and onboarding support requests.</p>
        </div>
        <button @click="showLogTicket = !showLogTicket" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Log Support Ticket
        </button>
    </div>

    <!-- Log Ticket Form -->
    <div x-show="showLogTicket" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Log Issue</h3>
        <form action="{{ route('admin.crm.tickets.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Ticket number</label>
                <input type="text" name="ticket_number" required placeholder="DWS-TKT-2026-001" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
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
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Subject / Issue Overview</label>
                <input type="text" name="subject" required placeholder="LMS Course video streaming failure" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Category</label>
                <select name="category" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Technical Issue">Technical Issue</option>
                    <option value="Billing">Billing / Payment Query</option>
                    <option value="Complaint">Complaint / Service Delay</option>
                    <option value="Inquiry">General Inquiry</option>
                    <option value="Project Support">Project Support</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Priority</label>
                <select name="priority" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent (Immediate)</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Assign Staff Specialist</label>
                <select name="assigned_staff_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">Leave Unassigned</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Detailed Description of Problem</label>
                <textarea name="message" rows="3" required placeholder="Provide error logs, steps to reproduce, or billing invoice numbers..." class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none"></textarea>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showLogTicket = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">File Ticket</button>
            </div>
        </form>
    </div>

    <!-- Tickets List Queue -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Support Queue</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Ticket ID &amp; Subject</th>
                        <th class="pb-3">Category</th>
                        <th class="pb-3">Client</th>
                        <th class="pb-3">Priority</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Assigned Agent</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($tickets as $tkt)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors text-brand-white">
                            <td class="py-4">
                                <strong class="block text-sm">{{ $tkt->subject }}</strong>
                                <span class="text-brand-gray/60 font-mono text-[9px]">{{ $tkt->ticket_number }}</span>
                                <span class="text-brand-gray/50 block max-w-xs truncate">{{ $tkt->message }}</span>
                            </td>
                            <td class="py-4 text-brand-gray font-semibold">{{ $tkt->category }}</td>
                            <td class="py-4">
                                <span class="block">{{ $tkt->client->contact_person }}</span>
                                <span class="text-brand-gray text-[10px]">{{ $tkt->client->company_name }}</span>
                            </td>
                            <td class="py-4 font-bold">
                                <span class="rounded px-2 py-0.5 text-[9px] uppercase
                                    @if($tkt->priority === 'Urgent') bg-rose-950 text-rose-400 border border-rose-500/20
                                    @elseif($tkt->priority === 'High') bg-amber-950 text-amber-400
                                    @else bg-brand-teal/10 text-brand-cyan
                                    @endif">
                                    {{ $tkt->priority }}
                                </span>
                            </td>
                            <td class="py-4">
                                <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase
                                    @if($tkt->status === 'Resolved') bg-emerald-950 text-emerald-400
                                    @elseif($tkt->status === 'Closed') bg-brand-dark text-brand-gray
                                    @elseif($tkt->status === 'Open') bg-rose-950 text-rose-400
                                    @else bg-brand-teal/20 text-brand-cyan
                                    @endif">
                                    {{ $tkt->status }}
                                </span>
                            </td>
                            <td class="py-4 text-brand-gray">{{ $tkt->assignedStaff ? $tkt->assignedStaff->name : 'Unassigned' }}</td>
                            <td class="py-4 text-right">
                                @if($tkt->status !== 'Resolved' && $tkt->status !== 'Closed')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form action="{{ route('admin.crm.tickets.status', $tkt->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Resolved">
                                            <button type="submit" class="rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-2 py-1 text-[10px] font-bold cursor-pointer">
                                                Resolve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.crm.tickets.status', $tkt->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Closed">
                                            <button type="submit" class="rounded bg-brand-dark-secondary border border-brand-teal/10 text-brand-gray px-2 py-1 text-[10px] font-bold cursor-pointer">
                                                Close
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-brand-gray/50 text-[10px]">No Actions</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-brand-gray">No customer tickets logged in the queue.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
