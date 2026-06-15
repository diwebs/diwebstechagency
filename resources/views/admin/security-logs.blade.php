@extends('layouts.admin')

@section('title', 'Security Audit Logs - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Security Audit Logs</h1>
        <p class="text-sm text-brand-gray mt-1">Full tamper-evident log of all authentication events, tab switches, and anti-cheat triggers.</p>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Event Type</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Exam Session</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="rounded px-2 py-1 text-[10px] font-bold uppercase
                                    @if(str_contains($log->event_type, 'switch') || str_contains($log->event_type, 'exit') || str_contains($log->event_type, 'failed'))
                                        bg-rose-950 text-rose-400 border border-rose-500/20
                                    @elseif(str_contains($log->event_type, 'webcam'))
                                        bg-amber-950 text-amber-400 border border-amber-500/20
                                    @else
                                        bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                                    @endif">
                                    {{ str_replace('_', ' ', $log->event_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-white font-medium">
                                {{ $log->user ? $log->user->name : '—' }}
                            </td>
                            <td class="px-6 py-4 text-brand-gray">
                                {{ $log->examSession ? $log->examSession->exam->title : '—' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-brand-gray/70">{{ $log->ip_address ?? '—' }}</td>
                            <td class="px-6 py-4 text-brand-gray">{{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-brand-gray">No security log events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-brand-teal/10">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
