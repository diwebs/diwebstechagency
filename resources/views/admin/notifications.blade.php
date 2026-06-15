@extends('layouts.admin')

@section('title', 'Notifications Hub - Admin Control Center')

@section('admin_content')
<div>
    <!-- Header with statistics -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Notifications &amp; Inbound Submissions</h1>
            <p class="text-sm text-brand-gray mt-1">Manage outbound broadcasts and inspect client form inquiries, tickets, and newsletter leads.</p>
        </div>
        
        @php
            $unreadCount = \App\Models\AdminNotification::where('is_read', false)->count();
        @endphp
        
        @if($unreadCount > 0)
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/30 hover:border-brand-teal px-4 py-2 text-xs font-bold text-brand-cyan shadow hover:bg-brand-teal/5 transition-all cursor-pointer">
                        ✓ Mark All Read
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: System Alert Dispatcher -->
        <div class="lg:col-span-5 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3 flex items-center gap-2">
                    <span>⚡</span> Outbound Broadcast Dispatcher
                </h3>
                
                <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <!-- Target Audience -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Target Audience Group</label>
                        <select name="target_role" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="all">All Registered Users</option>
                            <option value="student">Academy Students Only</option>
                            <option value="candidate">CBT Candidates Only</option>
                            <option value="client">Enterprise Clients Only</option>
                            <option value="super_admin">System Administrators</option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Announcement Subject</label>
                        <input type="text" 
                               name="subject" 
                               required 
                               placeholder="e.g. Server Maintenance or Syllabus Expansion Updates" 
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Message content -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Announcement / Push Message Body</label>
                        <textarea name="message" 
                                  rows="6" 
                                  required 
                                  placeholder="Provide the notification message context. HTML text formatting tags are supported." 
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-4 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all"></textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-brand-teal/10">
                        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                            ⚡ Dispatch Broadcast Alert
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Information Panel -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 bg-brand-dark-secondary/10">
                <h4 class="text-xs font-bold text-brand-cyan uppercase tracking-wider mb-2">System Telemetry Logs</h4>
                <p class="text-xs text-brand-gray/80 leading-relaxed">
                    Form submissions across the main marketing pages (contact requests, newsletter signups) and client portal operations are funneled here in real-time.
                </p>
                <div class="mt-4 flex items-center gap-4 text-[10px] text-brand-gray/60">
                    <div class="flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-brand-cyan"></span> Active Listener
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> TLS Encrypted
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Inbound Form Submissions Feed -->
        <div class="lg:col-span-7 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <div class="flex items-center justify-between mb-5 border-b border-brand-teal/10 pb-3">
                    <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider flex items-center gap-2">
                        <span>📥</span> Inbound Submissions Feed
                    </h3>
                    <span class="rounded-full bg-brand-teal/10 px-2.5 py-0.5 text-[10px] font-bold text-brand-cyan border border-brand-teal/20">
                        {{ $unreadCount }} Unread
                    </span>
                </div>

                <!-- Feed list -->
                <div class="space-y-4">
                    @forelse($notifications as $notification)
                        @php
                            $details = $notification->details ?? [];
                            $isUnread = !$notification->is_read;
                        @endphp
                        
                        <div class="rounded-xl border p-5 transition-all duration-300 relative 
                            {{ $isUnread 
                                ? 'border-brand-teal/30 bg-brand-teal/5 shadow-md shadow-brand-teal/5' 
                                : 'border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/25 hover:bg-brand-dark-secondary/30' }}">
                            
                            <!-- Unread dot -->
                            @if($isUnread)
                                <div class="absolute top-5 right-5 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-cyan opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-cyan"></span>
                                </div>
                            @endif

                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div>
                                    <!-- Type badge & Time -->
                                    <div class="flex items-center gap-2.5 mb-2">
                                        @if($notification->type === 'contact_form')
                                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-blue-950/40 text-blue-400 border border-blue-500/20">
                                                📧 Contact Lead
                                            </span>
                                        @elseif($notification->type === 'newsletter_subscribe')
                                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-purple-950/40 text-purple-400 border border-purple-500/20">
                                                📰 Newsletter
                                            </span>
                                        @elseif($notification->type === 'service_request')
                                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-cyan-950/40 text-brand-cyan border border-brand-teal/20">
                                                💼 Service Request
                                            </span>
                                        @elseif($notification->type === 'support_ticket')
                                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-amber-950/40 text-amber-400 border border-amber-500/20">
                                                🎫 Support Ticket
                                            </span>
                                        @elseif($notification->type === 'project_create')
                                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-emerald-950/40 text-emerald-400 border border-emerald-500/20">
                                                📂 Project Proposal
                                            </span>
                                        @else
                                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-brand-teal/10 text-brand-cyan border border-brand-teal/20">
                                                🔔 Alert
                                            </span>
                                        @endif

                                        <span class="text-[10px] text-brand-gray/50">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <h4 class="text-sm font-bold text-brand-white mt-1 pr-6">{{ $notification->title }}</h4>
                                </div>

                                <!-- Action button -->
                                @if($isUnread)
                                    <div class="flex-shrink-0 self-start sm:self-center mt-1 sm:mt-0">
                                        <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-[11px] px-3 py-1 font-bold rounded-lg bg-brand-teal/20 text-brand-cyan border border-brand-teal/35 hover:bg-brand-cyan hover:text-brand-dark-secondary transition-all cursor-pointer">
                                                Mark Read
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Details breakdown -->
                            @if($notification->type === 'contact_form')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 mt-4 p-3 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5 text-xs text-brand-gray">
                                    <div><span class="text-brand-cyan font-semibold">Name:</span> <span class="text-brand-white">{{ $details['name'] ?? '—' }}</span></div>
                                    <div><span class="text-brand-cyan font-semibold">Email:</span> <a href="mailto:{{ $details['email'] ?? '' }}" class="text-brand-cyan hover:underline">{{ $details['email'] ?? '—' }}</a></div>
                                    <div><span class="text-brand-cyan font-semibold">Phone:</span> <span class="text-brand-white">{{ $details['phone'] ?? '—' }}</span></div>
                                    <div><span class="text-brand-cyan font-semibold">Company:</span> <span class="text-brand-white">{{ $details['company'] ?? '—' }}</span></div>
                                    <div class="sm:col-span-2"><span class="text-brand-cyan font-semibold">Service Needed:</span> <span class="text-brand-white">{{ $details['service_needed'] ?? '—' }}</span></div>
                                    <div class="sm:col-span-2 mt-1">
                                        <span class="text-brand-cyan font-semibold block mb-1">Message:</span>
                                        <div class="p-2.5 rounded bg-brand-dark-secondary text-brand-white whitespace-pre-wrap border border-brand-teal/10">{{ $details['message'] ?? '—' }}</div>
                                    </div>
                                </div>
                            @elseif($notification->type === 'newsletter_subscribe')
                                <div class="grid grid-cols-1 gap-x-4 gap-y-2 mt-4 p-3 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5 text-xs text-brand-gray">
                                    <div><span class="text-brand-cyan font-semibold">Subscriber Email:</span> <a href="mailto:{{ $details['email'] ?? '' }}" class="text-brand-cyan hover:underline text-brand-white font-medium">{{ $details['email'] ?? '—' }}</a></div>
                                </div>
                            @elseif($notification->type === 'service_request')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 mt-4 p-3 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5 text-xs text-brand-gray">
                                    <div><span class="text-brand-cyan font-semibold">Client Name:</span> <span class="text-brand-white font-medium">{{ $details['client_name'] ?? '—' }}</span></div>
                                    <div><span class="text-brand-cyan font-semibold">Email:</span> <a href="mailto:{{ $details['client_email'] ?? '' }}" class="text-brand-cyan hover:underline">{{ $details['client_email'] ?? '—' }}</a></div>
                                    <div><span class="text-brand-cyan font-semibold">Service Type:</span> <span class="text-brand-white">{{ $details['service_type'] ?? '—' }}</span></div>
                                    <div><span class="text-brand-cyan font-semibold">Budget Range:</span> <span class="text-emerald-400 font-bold">{{ $details['budget_range'] ?? '—' }}</span></div>
                                    <div class="sm:col-span-2"><span class="text-brand-cyan font-semibold">Deadline:</span> <span class="text-brand-white">{{ $details['deadline'] ?? '—' }}</span></div>
                                    <div class="sm:col-span-2 mt-1">
                                        <span class="text-brand-cyan font-semibold block mb-1">Description:</span>
                                        <div class="p-2.5 rounded bg-brand-dark-secondary text-brand-white whitespace-pre-wrap border border-brand-teal/10">{{ $details['description'] ?? '—' }}</div>
                                    </div>
                                </div>
                            @elseif($notification->type === 'support_ticket')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 mt-4 p-3 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5 text-xs text-brand-gray">
                                    <div><span class="text-brand-cyan font-semibold">Client Name:</span> <span class="text-brand-white font-medium">{{ $details['client_name'] ?? '—' }}</span></div>
                                    <div><span class="text-brand-cyan font-semibold">Email:</span> <a href="mailto:{{ $details['client_email'] ?? '' }}" class="text-brand-cyan hover:underline">{{ $details['client_email'] ?? '—' }}</a></div>
                                    <div class="sm:col-span-2">
                                        <span class="text-brand-cyan font-semibold">Priority:</span>
                                        @php
                                            $priority = strtolower($details['priority'] ?? 'low');
                                            $pColor = 'bg-gray-950 text-gray-400 border border-gray-500/20';
                                            if ($priority === 'critical') $pColor = 'bg-rose-950 text-rose-400 border border-rose-500/20 animate-pulse';
                                            elseif ($priority === 'high') $pColor = 'bg-amber-950 text-amber-400 border border-amber-500/20';
                                            elseif ($priority === 'medium') $pColor = 'bg-blue-950 text-blue-400 border border-blue-500/20';
                                        @endphp
                                        <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase {{ $pColor }}">
                                            {{ $priority }}
                                        </span>
                                    </div>
                                    <div class="sm:col-span-2 mt-1">
                                        <span class="text-brand-cyan font-semibold block mb-1">Ticket Message:</span>
                                        <div class="p-2.5 rounded bg-brand-dark-secondary text-brand-white whitespace-pre-wrap border border-brand-teal/10">{{ $details['message'] ?? '—' }}</div>
                                    </div>
                                </div>
                            @elseif($notification->type === 'project_create')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 mt-4 p-3 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5 text-xs text-brand-gray">
                                    <div><span class="text-brand-cyan font-semibold">Client Name:</span> <span class="text-brand-white font-medium">{{ $details['client_name'] ?? '—' }}</span></div>
                                    <div><span class="text-brand-cyan font-semibold">Email:</span> <a href="mailto:{{ $details['client_email'] ?? '' }}" class="text-brand-cyan hover:underline">{{ $details['client_email'] ?? '—' }}</a></div>
                                    <div><span class="text-brand-cyan font-semibold">Service Type:</span> <span class="text-brand-white">{{ $details['service_type'] ?? '—' }}</span></div>
                                    <div><span class="text-brand-cyan font-semibold">Proposed Budget:</span> <span class="text-emerald-400 font-bold">${{ number_format($details['budget'] ?? 0) }}</span></div>
                                    <div class="sm:col-span-2 mt-1">
                                        <span class="text-brand-cyan font-semibold block mb-1">Project Description:</span>
                                        <div class="p-2.5 rounded bg-brand-dark-secondary text-brand-white whitespace-pre-wrap border border-brand-teal/10">{{ $details['description'] ?? '—' }}</div>
                                    </div>
                                </div>
                            @else
                                <!-- Fallback Loop -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 mt-4 p-3 rounded-lg bg-brand-dark-secondary/40 border border-brand-teal/5 text-xs text-brand-gray">
                                    @foreach($details as $key => $value)
                                        @if(is_array($value))
                                            <div class="sm:col-span-2">
                                                <span class="text-brand-cyan font-semibold block mb-1">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                                <div class="p-2.5 rounded bg-brand-dark-secondary text-brand-white whitespace-pre-wrap border border-brand-teal/10">{{ json_encode($value, JSON_PRETTY_PRINT) }}</div>
                                            </div>
                                        @else
                                            <div class="{{ strlen($value) > 60 ? 'sm:col-span-2' : '' }}">
                                                <span class="text-brand-cyan font-semibold">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                                @if(strlen($value) > 60)
                                                    <div class="p-2.5 mt-1 rounded bg-brand-dark-secondary text-brand-white whitespace-pre-wrap border border-brand-teal/10">{{ $value }}</div>
                                                @else
                                                    <span class="text-brand-white">{{ $value }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-brand-teal/20 p-8 text-center text-brand-gray text-xs">
                            <p>No inbound form submissions or notifications recorded yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="mt-6 border-t border-brand-teal/10 pt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
