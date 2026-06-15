@extends('layouts.admin')

@section('title', 'Support Tickets - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Ecosystem Support Tickets</h1>
            <p class="text-sm text-brand-gray mt-1">Review, assign, and update support tickets filed by candidates, clients, and students.</p>
        </div>
        <span class="text-xs text-brand-gray">{{ $tickets->total() }} unresolved requests</span>
    </div>

    <!-- Tickets table list -->
    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Filer / Account</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Inquiry / Message Details</th>
                        <th class="px-6 py-4">Priority</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-brand-white">{{ $ticket->user ? $ticket->user->name : 'Anonymous' }}</p>
                                <p class="text-[10px] text-brand-gray">{{ $ticket->user ? $ticket->user->email : '—' }}</p>
                            </td>
                            <td class="px-6 py-4 font-bold text-brand-white">
                                {{ $ticket->subject }}
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-brand-gray/90 leading-relaxed line-clamp-3">
                                    {{ $ticket->message }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2 py-0.5 text-[8px] font-bold uppercase
                                    @if($ticket->priority === 'high') bg-rose-950 text-rose-400 border border-rose-500/20
                                    @elseif($ticket->priority === 'medium') bg-amber-950 text-amber-400 border border-amber-500/20
                                    @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/15
                                    @endif">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2.5 py-1 text-[9px] font-bold uppercase
                                    @if($ticket->status === 'resolved') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                    @elseif($ticket->status === 'open') bg-rose-950 text-rose-400 border border-rose-500/20
                                    @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/10
                                    @endif">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">{{ $ticket->created_at ? $ticket->created_at->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.support.ticket.status', $ticket->id) }}" method="POST" class="inline-flex gap-1.5 justify-end">
                                    @csrf
                                    <select name="status" class="bg-brand-dark-secondary border border-brand-teal/15 rounded px-2 py-1 text-[10px] text-brand-gray focus:outline-none focus:border-brand-cyan/40">
                                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    <button type="submit" class="rounded bg-brand-cyan text-brand-dark-secondary px-3 py-1 text-[10px] font-bold hover:opacity-90">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-brand-gray">No support tickets active.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-brand-teal/10">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
