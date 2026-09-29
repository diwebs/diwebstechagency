@extends('layouts.staff')

@section('title', 'Assigned Projects - Staff Portal')

@section('staff_content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-brand-white">Assigned Projects Pipelines</h1>
        <p class="text-xs text-brand-gray mt-1">Check active software development pipelines, corporate milestones, and contract budgets.</p>
    </div>

    <!-- Directory table -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Project Name</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Client Details</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Budget</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Status</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-right">Agreement Signed At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($projects as $p)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4">
                                <span class="font-bold block text-sm">{{ $p->title }}</span>
                                <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">{{ $p->description }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold block">{{ $p->client->name ?? 'N/A' }}</span>
                                <span class="text-[9px] text-brand-gray block">{{ $p->client->email ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-brand-cyan">
                                ₦{{ number_format($p->budget, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-brand-teal/10 px-2.5 py-0.5 text-[9px] font-bold text-brand-cyan border border-brand-teal/20">{{ $p->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-brand-gray">
                                {{ $p->agreement_signed_at ? $p->agreement_signed_at->format('Y-m-d') : 'No Stamp' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-brand-gray">No projects registered under your workspace.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
