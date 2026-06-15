@extends('layouts.admin')

@section('title', 'Exam Sessions - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">CBT Exam Sessions</h1>
        <p class="text-sm text-brand-gray mt-1">Monitor all candidate exam runs, anti-cheat flags, and auto-graded scores.</p>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Candidate</th>
                        <th class="px-6 py-4">Exam Title</th>
                        <th class="px-6 py-4">Center</th>
                        <th class="px-6 py-4">Score</th>
                        <th class="px-6 py-4">Flags</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Started At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-brand-white">{{ $session->user->name }}</td>
                            <td class="px-6 py-4 text-brand-gray">{{ $session->exam->title }}</td>
                            <td class="px-6 py-4 text-brand-gray">{{ $session->center ? $session->center->name : 'Remote' }}</td>
                            <td class="px-6 py-4 font-mono font-bold 
                                {{ $session->score !== null && $session->score >= $session->exam->passing_score ? 'text-emerald-400' : ($session->score !== null ? 'text-rose-400' : 'text-brand-gray') }}">
                                {{ $session->score !== null ? $session->score . '%' : '—' }}
                            </td>
                            <td class="px-6 py-4 font-bold {{ $session->anti_cheat_flags > 0 ? 'text-rose-400' : 'text-brand-gray' }}">
                                {{ $session->anti_cheat_flags }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase
                                    @if($session->status === 'completed') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                    @elseif($session->status === 'flagged') bg-rose-950 text-rose-400 border border-rose-500/20
                                    @elseif($session->status === 'active') bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/20
                                    @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/10
                                    @endif">
                                    {{ $session->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">{{ $session->started_at ? $session->started_at->format('M d, Y H:i') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-brand-gray">No exam sessions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-brand-teal/10">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection
