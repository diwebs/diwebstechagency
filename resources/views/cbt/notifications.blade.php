@extends('layouts.cbt')

@section('title', 'CBT Notifications - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Notifications &amp; Schedule Reminders</h1>
            <p class="text-sm text-brand-gray mt-1">Updates concerning your examination seat bookings, grades releases, and registration parameters.</p>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
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
            <div class="flex items-start gap-4 p-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/20 transition-all">
                <span class="text-2xl">{{ $emoji }}</span>
                <div class="space-y-1 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-brand-white">{{ $notification->title }}</span>
                        <span class="rounded bg-brand-teal/15 text-brand-cyan text-[8px] font-extrabold uppercase px-1.5 py-0.5">{{ ucfirst($notification->type ?? 'System') }}</span>
                        @if(!$notification->is_read)
                            <span class="rounded bg-rose-500/20 text-rose-400 text-[8px] font-extrabold uppercase px-1.5 py-0.5">New</span>
                        @endif
                    </div>
                    <p class="text-xs text-brand-gray/80 leading-relaxed">{{ $notification->message }}</p>
                    <span class="block text-[9px] text-brand-gray/50 font-mono">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-brand-gray/60">
                <span class="text-4xl block mb-2">🔔</span>
                <p class="text-sm font-semibold">No notifications yet.</p>
                <p class="text-xs text-brand-gray/40 mt-1">When you receive exam updates or system announcements, they will appear here.</p>
            </div>
        @endforelse

        @if($notifications->count() > 0 && $notifications->hasPages())
            <div class="pt-4 border-t border-brand-teal/10">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
