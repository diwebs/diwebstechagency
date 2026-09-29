@extends('layouts.academy')

@section('title', 'My Notifications - Diwebs Academy')

@section('academy_content')
<div>
    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">My Notifications</h1>
            <p class="text-sm text-brand-gray mt-1">Stay updated with classroom schedules, exam announcements, and system alerts.</p>
        </div>

        @php $unread = $notifications->where('is_read', false)->count(); @endphp
        @if($unread > 0)
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-brand-teal/10 px-3 py-1 text-[10px] font-bold text-brand-cyan border border-brand-teal/20">
                {{ $unread }} Unread
            </span>
            <form action="{{ route('academy.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit"
                        class="rounded-lg bg-brand-dark-secondary border border-brand-teal/30 hover:border-brand-teal px-4 py-2 text-xs font-bold text-brand-cyan hover:bg-brand-teal/5 transition-all cursor-pointer">
                    ✓ Mark All Read
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 p-3 text-xs text-emerald-400">
        {{ session('success') }}
    </div>
    @endif

    {{-- Notification list --}}
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">
            In-App Alerts
        </h3>

        <div class="space-y-4">
            @forelse($notifications as $notification)
                @php
                    $isUnread = !$notification->is_read;
                    $emoji = match($notification->type) {
                        'invoice'    => '💰',
                        'contract'   => '📝',
                        'service'    => '🛠️',
                        'ticket'     => '🎟️',
                        'project'    => '🚀',
                        'broadcast'  => '📢',
                        'center'     => '🏫',
                        'exam'       => '🏆',
                        'warning'    => '⚠️',
                        'termination'=> '🚨',
                        'plan'       => '🔑',
                        'email'      => '✉️',
                        'academy', 'course' => '🎓',
                        default      => '🔔',
                    };
                @endphp

                <div class="rounded-xl border p-4 flex gap-4 transition-all duration-200
                    {{ $isUnread
                        ? 'border-brand-cyan/25 bg-brand-teal/5 shadow shadow-brand-teal/5'
                        : 'border-brand-teal/10 bg-brand-dark-secondary/20 hover:border-brand-teal/20' }}">

                    {{-- Icon --}}
                    <span class="text-xl shrink-0 mt-0.5">{{ $emoji }}</span>

                    {{-- Body --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center flex-wrap gap-2 mb-1">
                            <h4 class="text-xs font-bold text-brand-white">{{ $notification->title }}</h4>
                            <span class="rounded bg-brand-teal/15 text-brand-cyan text-[8px] font-extrabold uppercase px-1.5 py-0.5">
                                {{ ucfirst($notification->type ?? 'System') }}
                            </span>
                            @if($isUnread)
                                <span class="rounded bg-rose-500/20 text-rose-400 text-[8px] font-extrabold uppercase px-1.5 py-0.5 flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-400 animate-pulse inline-block"></span> New
                                </span>
                            @endif
                        </div>
                        <p class="text-[10px] text-brand-gray/80 leading-relaxed">{{ $notification->message }}</p>
                        <span class="text-[9px] text-brand-gray/50 block mt-2 font-mono">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Mark Read button --}}
                    @if($isUnread)
                    <div class="shrink-0 self-start">
                        <form action="{{ route('academy.notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="text-[10px] px-3 py-1.5 font-bold rounded-lg bg-brand-teal/20 text-brand-cyan border border-brand-teal/35 hover:bg-brand-cyan hover:text-brand-dark-secondary transition-all cursor-pointer whitespace-nowrap">
                                ✓ Mark Read
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="shrink-0 self-start">
                        <span class="text-[9px] text-brand-gray/30 font-mono">Read</span>
                    </div>
                    @endif
                </div>

            @empty
                <div class="py-14 text-center text-brand-gray/60">
                    <span class="text-5xl block mb-3">🔔</span>
                    <p class="text-sm font-semibold text-brand-white/70">No notifications yet.</p>
                    <p class="text-xs text-brand-gray/40 mt-1">When course updates or system alerts are sent, they will appear here.</p>
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
