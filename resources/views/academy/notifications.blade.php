@extends('layouts.academy')

@section('title', 'Academy Notifications - Diwebs Academy')

@section('academy_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Academy Notifications</h1>
        <p class="text-sm text-brand-gray mt-1">Stay updated with classroom schedules, exam announcements, and system alerts.</p>
    </div>

    <!-- Alerts listing -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">My In-App Alerts</h3>
        
        <div class="space-y-4">
            @forelse($notifications as $notification)
                @php
                    $emoji = match($notification->type) {
                        'invoice' => '💰',
                        'contract' => '📝',
                        'service' => '🛠️',
                        'ticket' => '🎟️',
                        'project' => '🚀',
                        'broadcast' => '📢',
                        'center' => '🏫',
                        'exam' => '🏆',
                        'warning' => '⚠️',
                        'termination' => '🚨',
                        'academy', 'course' => '🎓',
                        default => '🔔',
                    };
                @endphp
                <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-4 flex gap-3.5 hover:border-brand-teal/20 transition-all">
                    <span class="text-xl">{{ $emoji }}</span>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h4 class="text-xs font-bold text-brand-white">{{ $notification->title }}</h4>
                            <span class="rounded bg-brand-teal/15 text-brand-cyan text-[8px] font-extrabold uppercase px-1.5 py-0.5">{{ ucfirst($notification->type ?? 'System') }}</span>
                            @if(!$notification->is_read)
                                <span class="rounded bg-rose-500/20 text-rose-400 text-[8px] font-extrabold uppercase px-1.5 py-0.5">New</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-brand-gray/80 leading-relaxed">
                            {{ $notification->message }}
                        </p>
                        <span class="text-[9px] text-brand-gray/50 block mt-2 font-mono">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-brand-gray/60">
                    <span class="text-4xl block mb-2">🔔</span>
                    <p class="text-sm font-semibold">No notifications yet.</p>
                    <p class="text-xs text-brand-gray/40 mt-1">When course updates or system alerts are dispatched, they will appear here.</p>
                </div>
            @endforelse

            @if($notifications->count() > 0 && $notifications->hasPages())
                <div class="pt-4 border-t border-brand-teal/10">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
