@extends('layouts.admin')

@section('title', 'Projects Pipeline - Admin Control Center')

@section('admin_content')
<div>
    <!-- Page Header -->
    <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Projects Pipeline Manager</h1>
            <p class="text-sm text-brand-gray mt-1">Full visibility into all client-submitted proposals, active contracts, and project delivery phases.</p>
        </div>
    </div>

    {{-- ─── Flash Messages ─── --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 rounded-xl px-5 py-4 text-sm">
            <span class="text-lg">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 bg-red-950/60 border border-red-500/30 text-red-300 rounded-xl px-5 py-4 text-sm">
            <span class="text-lg">❌</span> {{ session('error') }}
        </div>
    @endif

    {{-- ─── Stats Summary Cards ─── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <a href="{{ route('admin.projects') }}?status=all"
           class="glass-card rounded-2xl p-4 border border-brand-teal/15 flex flex-col items-center text-center hover:border-brand-cyan/40 transition-all">
            <span class="text-2xl font-black text-brand-white">{{ $stats['total'] }}</span>
            <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider mt-1">All Projects</span>
        </a>
        <a href="{{ route('admin.projects') }}?status=initiated"
           class="glass-card rounded-2xl p-4 border {{ $stats['pending'] > 0 ? 'border-amber-500/40 bg-amber-950/10' : 'border-brand-teal/15' }} flex flex-col items-center text-center hover:border-amber-400/50 transition-all relative overflow-hidden">
            @if($stats['pending'] > 0)
                <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
            @endif
            <span class="text-2xl font-black {{ $stats['pending'] > 0 ? 'text-amber-400' : 'text-brand-white' }}">{{ $stats['pending'] }}</span>
            <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider mt-1">Pending Review</span>
        </a>
        <a href="{{ route('admin.projects') }}?status=planning"
           class="glass-card rounded-2xl p-4 border border-brand-teal/15 flex flex-col items-center text-center hover:border-brand-cyan/40 transition-all">
            <span class="text-2xl font-black text-brand-cyan">{{ $stats['planning'] }}</span>
            <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider mt-1">Planning</span>
        </a>
        <a href="{{ route('admin.projects') }}?status=active"
           class="glass-card rounded-2xl p-4 border border-brand-teal/15 flex flex-col items-center text-center hover:border-brand-cyan/40 transition-all">
            <span class="text-2xl font-black text-emerald-400">{{ $stats['active'] }}</span>
            <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider mt-1">Active</span>
        </a>
        <a href="{{ route('admin.projects') }}?status=review"
           class="glass-card rounded-2xl p-4 border border-brand-teal/15 flex flex-col items-center text-center hover:border-brand-cyan/40 transition-all">
            <span class="text-2xl font-black text-purple-400">{{ $stats['review'] }}</span>
            <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider mt-1">In Review</span>
        </a>
        <a href="{{ route('admin.projects') }}?status=delivered"
           class="glass-card rounded-2xl p-4 border border-brand-teal/15 flex flex-col items-center text-center hover:border-brand-cyan/40 transition-all">
            <span class="text-2xl font-black text-sky-400">{{ $stats['delivered'] }}</span>
            <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider mt-1">Delivered</span>
        </a>
    </div>

    {{-- ─── Client Submissions Inbox ─── --}}
    @if($pendingInbox->count() > 0)
    <div class="glass-card rounded-2xl border border-amber-500/25 bg-amber-950/5 p-6 mb-8">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-400 text-sm">📥</span>
                <div>
                    <h2 class="text-sm font-bold text-brand-white">Client Proposal Inbox</h2>
                    <p class="text-[11px] text-brand-gray">{{ $pendingInbox->count() }} new project request{{ $pendingInbox->count() > 1 ? 's' : '' }} submitted by clients awaiting your review and validation.</p>
                </div>
            </div>
            <span class="rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-400 font-black text-xs px-3 py-1">
                {{ $pendingInbox->count() }} Pending
            </span>
        </div>

        <div class="space-y-3">
            @foreach($pendingInbox as $inbox)
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-xl border border-amber-500/15 bg-brand-dark-secondary/40 p-4 hover:border-amber-400/30 transition-all">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="rounded bg-amber-950 border border-amber-500/20 text-amber-400 text-[9px] font-black uppercase px-2 py-0.5">🆕 Client Submitted</span>
                        <span class="text-[9px] text-brand-gray font-mono">{{ $inbox->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 class="text-sm font-bold text-brand-white mt-1.5 truncate">{{ $inbox->title }}</h4>
                    <p class="text-[11px] text-brand-gray mt-0.5">
                        Client: <strong class="text-brand-cyan">{{ $inbox->client->name }}</strong>
                        <span class="text-brand-gray/50 mx-1">•</span>
                        {{ $inbox->client->email }}
                        <span class="text-brand-gray/50 mx-1">•</span>
                        Service: <span class="text-brand-white">{{ $inbox->service_type ?? 'Not specified' }}</span>
                    </p>
                    <p class="text-[11px] text-emerald-400 font-mono mt-0.5">
                        Budget: @money($inbox->budget)
                    </p>
                    @if($inbox->description)
                        <p class="text-[11px] text-brand-gray mt-1 line-clamp-2 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($inbox->description, 160) }}
                        </p>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
                    <form action="{{ route('admin.projects.validate', $inbox->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-[11px] px-4 py-2 hover:opacity-90 transition-all w-full sm:w-auto flex items-center gap-1.5 shadow-md whitespace-nowrap">
                            🛡️ Validate & Activate
                        </button>
                    </form>
                    <form action="{{ route('admin.projects.delete', $inbox->id) }}" method="POST"
                          onsubmit="return confirm('Reject and delete this project request from {{ addslashes($inbox->client->name) }}?')">
                        @csrf
                        <button type="submit" class="rounded-xl bg-red-950/60 border border-red-500/20 text-red-400 font-bold text-[11px] px-4 py-2 hover:bg-red-900/40 transition-all w-full sm:w-auto whitespace-nowrap">
                            ✕ Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Search & Filter Toolbar ─── --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
        <form method="GET" action="{{ route('admin.projects') }}" class="flex flex-1 items-center gap-2">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-brand-gray text-sm">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by project title or client name / email..."
                       class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark pl-9 pr-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan/50 transition-all placeholder-brand-gray/50">
            </div>
            <button type="submit" class="rounded-xl bg-brand-teal/20 border border-brand-teal/30 text-brand-cyan font-bold text-xs px-5 py-2.5 hover:bg-brand-teal/35 transition-all whitespace-nowrap">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.projects') }}?status={{ request('status', 'all') }}" class="text-xs text-brand-gray hover:text-brand-white transition-colors whitespace-nowrap">Clear</a>
            @endif
        </form>
    </div>

    {{-- ─── Status Filter Tabs ─── --}}
    @php
        $currentStatus = request('status', 'all');
        $currentSearch = request('search', '');
        $statusTabs = [
            'all'       => ['label' => 'All Projects', 'icon' => '📂'],
            'initiated' => ['label' => 'Pending Review', 'icon' => '⏳'],
            'planning'  => ['label' => 'Planning',      'icon' => '📋'],
            'active'    => ['label' => 'Active',        'icon' => '🟢'],
            'review'    => ['label' => 'In Review',     'icon' => '🔍'],
            'delivered' => ['label' => 'Delivered',     'icon' => '✅'],
        ];
    @endphp
    <div class="flex items-center gap-1.5 flex-wrap mb-6">
        @foreach($statusTabs as $statusKey => $tabInfo)
            <a href="{{ route('admin.projects') }}?status={{ $statusKey }}@if($currentSearch)&search={{ urlencode($currentSearch) }}@endif"
               class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all border
                      {{ $currentStatus === $statusKey
                          ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md'
                          : 'bg-brand-dark-secondary/40 border-brand-teal/15 text-brand-gray hover:text-brand-white hover:border-brand-teal/30' }}">
                {{ $tabInfo['icon'] }} {{ $tabInfo['label'] }}
            </a>
        @endforeach
    </div>

    {{-- ─── Results count ─── --}}
    <p class="text-[11px] text-brand-gray mb-4">
        Showing <strong class="text-brand-white">{{ $projects->total() }}</strong> project{{ $projects->total() !== 1 ? 's' : '' }}
        @if($currentStatus !== 'all') with status <strong class="text-brand-cyan">{{ ucfirst($currentStatus) }}</strong>@endif
        @if($currentSearch) matching <strong class="text-brand-white">"{{ $currentSearch }}"</strong>@endif
    </p>

    {{-- ─── Projects List ─── --}}
    <div class="space-y-6">
        @forelse($projects as $project)
            <div class="glass-card rounded-2xl border {{ !$project->is_validated ? 'border-amber-500/20' : 'border-brand-teal/15' }} p-6 space-y-4">
                <!-- Project info header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-brand-teal/10 pb-4">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="text-[9px] font-mono text-brand-cyan uppercase tracking-wider">PROJECT ID: {{ $project->id }}</span>
                            @if(!$project->is_validated)
                                <span class="rounded bg-amber-950 border border-amber-500/20 text-amber-400 text-[9px] font-black uppercase px-2 py-0.5">📥 Client Submitted</span>
                            @endif
                            <span class="text-[9px] text-brand-gray font-mono">{{ $project->created_at->format('M d, Y • g:i A') }}</span>
                        </div>
                        <h3 class="text-base font-bold text-brand-white mt-1">{{ $project->title }}</h3>
                        <p class="text-xs text-brand-gray mt-0.5">Client: <strong>{{ $project->client->name }}</strong> ({{ $project->client->email }})</p>
                        @if($project->service_type)
                            <p class="text-xs text-brand-gray mt-0.5">Service: <span class="text-brand-cyan">{{ $project->service_type }}</span></p>
                        @endif
                        @if($project->description)
                            <p class="text-[11px] text-brand-gray/70 mt-1.5 line-clamp-2 leading-relaxed max-w-2xl">{{ \Illuminate\Support\Str::limit($project->description, 200) }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <span class="text-xs font-bold text-emerald-400">Budget: @money($project->budget)</span>
                        <span class="rounded px-2.5 py-1 text-[10px] font-bold uppercase
                            @if($project->status === 'delivered') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                            @elseif($project->status === 'active') bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/20
                            @elseif($project->status === 'review') bg-purple-950 text-purple-400 border border-purple-500/20
                            @elseif($project->status === 'planning') bg-sky-950 text-sky-400 border border-sky-500/20
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

                <!-- Pipeline Status Note -->
                <div class="p-4 bg-brand-dark-secondary/20 border border-brand-teal/10 rounded-xl space-y-3">
                    <div class="flex justify-between items-center flex-wrap gap-2">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-cyan block">Pipeline Status Note</span>
                            <span class="text-xs text-brand-gray mt-0.5 block">Visible to the client in their workspace and delivered to their email mailbox.</span>
                        </div>
                        @if($project->pipeline_note)
                            <span class="rounded bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/20 px-2.5 py-1 text-[9px] font-bold">Has active note</span>
                        @else
                            <span class="rounded bg-brand-dark-secondary text-brand-gray border border-brand-teal/10 px-2.5 py-1 text-[9px] font-bold">No note set</span>
                        @endif
                    </div>
                    
                    <form action="{{ route('admin.projects.update-note', $project->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <textarea name="pipeline_note" rows="3" 
                                  placeholder="Enter status update details, current blocker, next actions or important information for the client..."
                                  class="w-full bg-brand-dark border border-brand-teal/15 rounded-xl p-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan/40 placeholder-brand-gray/60 resize-none">{{ $project->pipeline_note }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="rounded bg-brand-cyan text-brand-dark-secondary px-3 py-1.5 text-[10px] font-bold hover:opacity-90 transition-all flex items-center gap-1.5">
                                💬 Update Note &amp; Notify Client
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Staff Assignments & Handlers Section -->
                <div class="p-4 bg-brand-dark-secondary/40 border border-brand-teal/10 rounded-xl space-y-4">
                    <div class="flex justify-between items-center border-b border-brand-teal/10 pb-2">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-cyan block">Technical Handlers &amp; Project Team</span>
                            <span class="text-xs text-brand-gray mt-0.5 block">Assign staff specialists to deliver on specific project modules.</span>
                        </div>
                    </div>

                    <!-- Current Assignments List -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @forelse($project->assignments as $assignment)
                            <div class="bg-[#1A1D21]/60 border border-brand-teal/10 rounded-xl p-3 flex justify-between items-center gap-2">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-brand-white truncate">{{ $assignment->staff->name }}</div>
                                    <div class="text-[10px] text-brand-cyan font-mono truncate mt-0.5">{{ $assignment->role }}</div>
                                </div>
                                <form action="{{ route('admin.projects.remove-assignment', ['id' => $project->id, 'assignmentId' => $assignment->id]) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="text-xs hover:text-red-400 text-brand-gray/60 p-1 transition-all" title="Remove Assignment">
                                        ❌
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="col-span-full text-[11px] text-brand-gray italic">No staff members currently assigned to this project.</div>
                        @endforelse
                    </div>

                    <!-- Assign New Staff Form -->
                    <form action="{{ route('admin.projects.assign', $project->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-brand-teal/5 items-end">
                        @csrf
                        <div>
                            <label class="block text-[9px] uppercase font-bold text-brand-gray mb-1.5">Assign Specialist</label>
                            <select name="staff_id" required class="w-full rounded-lg border border-brand-teal/15 bg-brand-dark px-3 py-2 text-xs text-brand-white focus:outline-none focus:border-brand-cyan/40">
                                <option value="" disabled selected>Select staff member...</option>
                                @foreach($staffMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role->name ?? 'No Role' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase font-bold text-brand-gray mb-1.5">Project Role / Module Responsibility</label>
                            <select name="role" required class="w-full rounded-lg border border-brand-teal/15 bg-brand-dark px-3 py-2 text-xs text-brand-white focus:outline-none focus:border-brand-cyan/40">
                                <option value="" disabled selected>Choose responsibility...</option>
                                <option value="Frontend Engineer">Frontend Engineer</option>
                                <option value="Backend Developer">Backend Developer</option>
                                <option value="Full Stack Developer">Full Stack Developer</option>
                                <option value="UI/UX Designer">UI/UX Designer</option>
                                <option value="QA Lead & Testing">QA Lead &amp; Testing</option>
                                <option value="Project Manager">Project Manager</option>
                                <option value="DevOps & Deployments">DevOps &amp; Deployments</option>
                                <option value="Solutions Architect">Solutions Architect</option>
                            </select>
                        </div>
                        <button type="submit" class="rounded-lg bg-brand-cyan/15 border border-brand-cyan/30 text-brand-cyan hover:bg-brand-cyan/25 font-bold text-xs py-2 px-4 transition-all">
                            ➕ Assign Member
                        </button>
                    </form>
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
                <div class="text-4xl mb-3">📂</div>
                <p class="text-sm font-semibold text-brand-white">No projects found</p>
                <p class="text-xs mt-1">
                    @if(request('search') || (request('status') && request('status') !== 'all'))
                        No projects match your current search or filter. <a href="{{ route('admin.projects') }}" class="text-brand-cyan hover:underline">Clear filters</a>
                    @else
                        No active projects registered in the pipeline yet.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $projects->links() }}
    </div>
</div>
@endsection
