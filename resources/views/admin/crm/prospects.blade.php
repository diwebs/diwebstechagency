@extends('layouts.admin')

@section('title', 'Prospects Evaluation - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ editingId: null }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Prospects Evaluation</h1>
            <p class="text-xs text-brand-gray mt-1">Deep analysis of qualified leads and deal conversion probability.</p>
        </div>
    </div>

    <!-- Prospects List -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Prospect Audit Queue</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Lead / Prospect Info</th>
                        <th class="pb-3">Qualification Score</th>
                        <th class="pb-3">Service Analysis</th>
                        <th class="pb-3">Budget Review</th>
                        <th class="pb-3">Win Probability</th>
                        <th class="pb-3">Conversion Log</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($prospects as $pr)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors">
                            <td class="py-4">
                                <strong class="block text-brand-white text-sm">{{ $pr->lead->full_name }}</strong>
                                <span class="text-brand-gray text-[10px]">{{ $pr->lead->company_name }}</span>
                                <span class="text-brand-gray/60 block">{{ $pr->lead->email }}</span>
                            </td>
                            <td class="py-4">
                                <span class="text-brand-cyan font-bold font-mono">{{ $pr->qualification_score }}%</span>
                            </td>
                            <td class="py-4 text-brand-white max-w-xs">
                                <div class="text-[11px] leading-relaxed truncate">{{ $pr->service_requirement_analysis ?? 'Needs evaluation' }}</div>
                            </td>
                            <td class="py-4 text-brand-white max-w-xs">
                                <div class="text-[11px] leading-relaxed truncate">{{ $pr->budget_analysis ?? 'Needs budget profiling' }}</div>
                            </td>
                            <td class="py-4 font-mono font-bold">
                                <span class="inline-block rounded px-2 py-0.5
                                    @if($pr->probability_scoring >= 70) bg-emerald-950 text-emerald-400
                                    @elseif($pr->probability_scoring >= 40) bg-brand-teal/15 text-brand-cyan
                                    @else bg-rose-950/60 text-rose-400
                                    @endif">
                                    {{ $pr->probability_scoring }}%
                                </span>
                            </td>
                            <td class="py-4 text-brand-gray">
                                {{ $pr->conversion_tracking ?? 'In validation queue' }}
                            </td>
                            <td class="py-4 text-right">
                                <button @click="editingId = (editingId === {{ $pr->id }} ? null : {{ $pr->id }})" class="rounded bg-brand-teal/20 border border-brand-teal/30 hover:bg-brand-teal/30 text-brand-cyan px-2.5 py-1 text-[11px] font-bold cursor-pointer">
                                    Assess
                                </button>
                            </td>
                        </tr>

                        <!-- Interactive inline assessment form -->
                        <tr x-show="editingId === {{ $pr->id }}" x-cloak class="bg-brand-dark-secondary/20">
                            <td colspan="7" class="p-6">
                                <form action="{{ route('admin.crm.prospects.update', $pr->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                                    @csrf
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Qualification Score (0-100)</label>
                                        <input type="number" name="qualification_score" value="{{ $pr->qualification_score }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Deal Close Probability (%)</label>
                                        <input type="number" name="probability_scoring" value="{{ $pr->probability_scoring }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Conversion Status/Log</label>
                                        <input type="text" name="conversion_tracking" value="{{ $pr->conversion_tracking }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Service Requirement Analysis</label>
                                        <textarea name="service_requirement_analysis" rows="2" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">{{ $pr->service_requirement_analysis }}</textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Detailed Budget Profiling</label>
                                        <textarea name="budget_analysis" rows="2" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">{{ $pr->budget_analysis }}</textarea>
                                    </div>
                                    <div class="col-span-1 md:col-span-4 flex justify-end gap-2 mt-2">
                                        <button type="button" @click="editingId = null" class="rounded-lg bg-brand-dark border border-brand-teal/10 px-4 py-1.5 text-brand-gray">Cancel</button>
                                        <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-1.5 font-bold cursor-pointer">Save Evaluation</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-brand-gray">No qualified prospects detected in pipeline.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $prospects->links() }}
        </div>
    </div>
</div>
@endsection
