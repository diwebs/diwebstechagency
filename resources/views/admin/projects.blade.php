@extends('layouts.admin')

@section('title', 'Projects Pipeline - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Projects Pipeline Manager</h1>
            <p class="text-sm text-brand-gray mt-1">Track corporate contracts, active project phases, and milestone delivery schedules.</p>
        </div>
    </div>

    <div class="space-y-6">
        @forelse($projects as $project)
            <div class="glass-card rounded-2xl border border-brand-teal/15 p-6 space-y-4">
                <!-- Project info header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-brand-teal/10 pb-4">
                    <div>
                        <span class="text-[9px] font-mono text-brand-cyan uppercase tracking-wider">PROJECT ID: {{ $project->id }}</span>
                        <h3 class="text-base font-bold text-brand-white mt-1">{{ $project->title }}</h3>
                        <p class="text-xs text-brand-gray mt-0.5">Client: <strong>{{ $project->client->name }}</strong> ({{ $project->client->email }})</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-bold text-emerald-400">Budget: @money($project->budget)</span>
                        <span class="rounded px-2.5 py-1 text-[10px] font-bold uppercase
                            @if($project->status === 'delivered') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                            @elseif($project->status === 'active') bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/20
                            @elseif($project->status === 'review') bg-amber-950 text-amber-400 border border-amber-500/20
                            @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/15
                            @endif">
                            {{ strtoupper($project->status) }}
                        </span>
                        <form action="{{ route('admin.projects.delete', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete project: &quot;{{ $project->title }}&quot;? This will remove all milestones and cannot be undone.')">
                            @csrf
                            <button type="submit" class="rounded px-2.5 py-1 text-[10px] font-bold uppercase cursor-pointer bg-red-950 text-red-400 border border-red-500/20 hover:bg-red-900/35 transition-all">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Project Management / Validation and Success Rate Controls -->
                <div class="p-4 bg-brand-dark-secondary/40 border border-brand-teal/10 rounded-xl space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-gray block">Service Segment &amp; Telemetry</span>
                            <span class="text-xs font-semibold text-brand-white mt-1 block">
                                Service: <strong class="text-brand-cyan">{{ $project->service_type ?? 'Unassigned' }}</strong> 
                                @if($project->is_validated)
                                    • Success Rate: <strong class="text-emerald-400">{{ $project->success_rate }}%</strong>
                                @endif
                            </span>
                        </div>
                        
                        <div>
                            @if(!$project->is_validated)
                                <form action="{{ route('admin.projects.validate', $project->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs px-4 py-2 hover:opacity-90 transition-all flex items-center gap-1.5 shadow-md">
                                        🛡️ Validate Project
                                    </button>
                                </form>
                            @else
                                <span class="rounded-full bg-emerald-950 text-emerald-400 border border-emerald-500/20 px-2.5 py-1 text-[9px] font-bold uppercase">
                                    ✔ Validated
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($project->is_validated)
                        <div class="border-t border-brand-teal/5 pt-3">
                            <form action="{{ route('admin.projects.success-rate', $project->id) }}" method="POST" class="flex flex-wrap items-center gap-4">
                                @csrf
                                <div class="flex-grow min-w-[200px]">
                                    <label class="block text-[9px] uppercase font-bold text-brand-gray mb-1">
                                        Update Success / Completion Rate: <span class="text-brand-cyan font-mono" id="rate-val-{{ $project->id }}">{{ $project->success_rate }}%</span>
                                    </label>
                                    <input type="range" name="success_rate" min="0" max="100" value="{{ $project->success_rate }}" 
                                           oninput="document.getElementById('rate-val-{{ $project->id }}').innerText = this.value + '%'"
                                           class="w-full h-1.5 bg-brand-dark rounded-lg appearance-none cursor-pointer accent-brand-cyan">
                                </div>
                                <button type="submit" class="rounded bg-brand-cyan text-brand-dark-secondary px-3 py-1.5 text-[10px] font-bold hover:opacity-90 self-end">
                                    Update Success Rate
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Milestones layout -->
                <div>
                    <h4 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Milestone Delivery Phases</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($project->milestones as $milestone)
                            <div class="glass-card rounded-xl p-4 border border-brand-teal/10 relative">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h5 class="font-bold text-xs text-brand-white line-clamp-1">{{ $milestone->title }}</h5>
                                        <p class="text-[10px] text-brand-gray mt-1 leading-relaxed line-clamp-2">{{ $milestone->description }}</p>
                                    </div>
                                    <span class="rounded-full px-2 py-0.5 text-[8px] font-bold uppercase flex-shrink-0
                                        @if($milestone->status === 'completed') bg-emerald-950 text-emerald-400
                                        @elseif($milestone->status === 'pending') bg-amber-950 text-amber-400
                                        @else bg-brand-dark-secondary text-brand-gray
                                        @endif">
                                        {{ $milestone->status }}
                                    </span>
                                </div>

                                <div class="mt-4 border-t border-brand-teal/5 pt-3 flex items-center justify-between text-[10px]">
                                    <span class="text-brand-gray">Due: {{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : 'N/A' }}</span>
                                    <span class="font-mono text-emerald-400 font-bold">@money($milestone->amount)</span>
                                </div>

                                <!-- Update Milestone Form -->
                                <form action="{{ route('admin.projects.milestone.status', ['id' => $project->id, 'milestoneId' => $milestone->id]) }}" method="POST" class="mt-3.5 flex gap-1.5">
                                    @csrf
                                    <select name="status" class="bg-brand-dark-secondary border border-brand-teal/15 rounded px-2 py-1 text-[9px] text-brand-gray focus:outline-none focus:border-brand-cyan/40 flex-1">
                                        <option value="pending" {{ $milestone->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ $milestone->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    <button type="submit" class="rounded bg-brand-cyan text-brand-dark-secondary px-2.5 py-1 text-[9px] font-bold hover:opacity-90">
                                        Save
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-[11px] text-brand-gray col-span-full">No milestones assigned to this project.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-card rounded-2xl p-12 text-center text-brand-gray">
                No active projects registered in the pipeline.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $projects->links() }}
    </div>
</div>
@endsection
