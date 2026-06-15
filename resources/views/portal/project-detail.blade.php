@extends('layouts.app')

@section('title', $project->title . ' - Project Details')

@section('content')
    @if(!$project->is_validated || !$project->payment_made)
        <!-- Awaiting Validation or Payment Screen -->
        <div class="glass-card rounded-2xl p-12 text-center border border-brand-teal/20 space-y-6 max-w-2xl mx-auto my-12 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-brand-cyan/5 rounded-full blur-2xl"></div>
            
            <div class="text-5xl">🔒</div>
            <h2 class="text-2xl font-extrabold text-brand-white">Project Sprints Locked</h2>
            
            <p class="text-sm text-brand-gray leading-relaxed">
                @if(!$project->is_validated)
                    🛡️ This project is currently awaiting administrator validation. Once validated, we will publish the service contract agreement and kickoff invoice.
                @else
                    💳 Project is validated, but initial payment has not been made. Please complete the digital agreement signature under the <strong>Digital Contracts</strong> tab and pay the initialization invoice under <strong>Invoices &amp; Payments</strong> on your dashboard to unlock sprint tracking and telemetry features.
                @endif
            </p>
            
            <div class="flex justify-center gap-3">
                <a href="{{ route('portal.dashboard') }}" class="rounded-xl bg-brand-dark-secondary border border-brand-teal/15 text-brand-white font-semibold text-xs px-6 py-3.5 hover:bg-brand-dark transition-all">
                    ← Return to Workspace
                </a>
            </div>
        </div>
    @else
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
            <div>
                <a href="{{ route('portal.dashboard') }}" class="text-xs text-brand-cyan hover:underline">← Back to Workspace Dashboard</a>
                <h1 class="text-2xl font-bold text-brand-white mt-2">{{ $project->title }}</h1>
                <p class="text-sm text-brand-gray mt-1 max-w-3xl">{{ $project->description }}</p>
            </div>
            <span class="rounded-full px-4 py-1.5 text-xs font-bold uppercase
                @if($project->status === 'active') bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                @elseif($project->status === 'completed') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/10
                @endif">
                {{ $project->status }}
            </span>
        </div>

        @if($project->service_type === 'Website Development' || $project->service_type === 'Web Development')
            <!-- Premium success rate header banner -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/20 mb-8 relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="absolute right-0 top-0 w-48 h-48 bg-brand-cyan/5 rounded-full blur-3xl"></div>
                <div>
                    <span class="text-[9px] font-mono text-brand-cyan uppercase tracking-wider block">Real-time Quality Telemetry</span>
                    <h3 class="text-base font-bold text-brand-white mt-1">🌐 Web Development Project Success Rate</h3>
                    <p class="text-xs text-brand-gray mt-0.5 leading-relaxed">This metric represents code coverage, sprint deliveries, and overall launch-readiness updated by the admin.</p>
                </div>
                
                <div class="w-full md:w-80 flex-shrink-0">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-semibold text-brand-gray">Launch Progress:</span>
                        <span class="text-xs font-mono font-bold text-emerald-400">{{ $project->success_rate }}%</span>
                    </div>
                    <div class="w-full bg-brand-dark rounded-full h-3.5 border border-brand-teal/10 p-0.5">
                        <div class="bg-gradient-to-r from-brand-teal to-brand-cyan h-2 rounded-full transition-all duration-700 shadow-[0_0_8px_rgba(0,194,209,0.4)]" style="width: {{ $project->success_rate }}%"></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Milestones list -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">Project Milestone Sprints</h3>

            @foreach($project->milestones as $milestone)
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                    <div class="flex items-start justify-between flex-wrap gap-2">
                        <div>
                            <h4 class="text-base font-bold text-brand-white">{{ $milestone->title }}</h4>
                            <p class="text-xs text-brand-gray mt-1">{{ $milestone->description }}</p>
                        </div>
                        <span class="rounded px-2.5 py-0.5 text-[10px] font-bold capitalize
                            @if($milestone->status === 'approved') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                            @elseif($milestone->status === 'working') bg-brand-teal/10 text-brand-cyan border border-brand-teal/20 animate-pulse
                            @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/10
                            @endif">
                            {{ $milestone->status }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-brand-gray">
                        <span>Sprint Target Date: <strong>{{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : 'N/A' }}</strong></span>
                        <strong class="text-brand-white">@money($milestone->amount)</strong>
                    </div>

                    @if($milestone->status === 'working' || $milestone->status === 'pending')
                        <div class="pt-2 border-t border-brand-teal/5 flex gap-2">
                            <form action="{{ route('portal.milestone.action', $milestone->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="approved">
                                <button type="submit" class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 hover:bg-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-wider px-3.5 py-2 transition-all">
                                    Sign Off / Approve
                                </button>
                            </form>

                            <button onclick="const comment = prompt('Describe modifications needed:'); if(comment) {
                                const f = document.createElement('form'); f.method='POST'; f.action='{{ route('portal.milestone.action', $milestone->id) }}';
                                const c = document.createElement('input'); c.name='comments'; c.value=comment; f.appendChild(c);
                                const a = document.createElement('input'); a.name='action'; a.value='revision_requested'; f.appendChild(a);
                                const t = document.createElement('input'); t.name='_token'; t.value='{{ csrf_token() }}'; f.appendChild(t);
                                document.body.appendChild(f); f.submit();
                            }" class="rounded-lg bg-rose-500/10 border border-rose-500/30 hover:bg-rose-500/20 text-rose-400 text-[10px] font-bold uppercase tracking-wider px-3.5 py-2 transition-all">
                                Request Sprint Revision
                            </button>
                        </div>
                    @endif

                    @if($milestone->logs->isNotEmpty())
                        <div class="pt-2 pl-4 border-l border-brand-teal/20 space-y-1 text-[10px] text-brand-gray">
                            <span class="uppercase font-bold block mb-1">Sprint History Audits</span>
                            @foreach($milestone->logs as $log)
                                <div>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</strong> by client • {{ $log->created_at->format('M d, Y H:i') }}
                                    @if($log->comments) <div class="italic text-brand-gray/80 mt-0.5">"{{ $log->comments }}"</div> @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Right: Operations panel -->
        <div class="space-y-6">
            
            <!-- Project files directory -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h4 class="text-sm font-bold text-brand-white">Upload Assets &amp; Specs</h4>
                
                <form action="{{ route('portal.project.upload', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <input type="file" name="project_file" required class="block w-full text-xs text-brand-gray file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand-teal/10 file:text-brand-cyan hover:file:bg-brand-teal/20">
                    </div>
                    <div>
                        <label class="block text-[9px] uppercase font-bold text-brand-gray mb-1">Destination Folder</label>
                        <select name="folder" class="w-full rounded-lg bg-[#25282D] border border-brand-teal/15 px-3 py-1.5 text-xs text-brand-white focus:outline-none">
                            <option value="assets">Assets &amp; Layouts</option>
                            <option value="deliverables">Source Packages</option>
                            <option value="backups">Data Backups</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-teal/20 border border-brand-teal/40 text-brand-cyan text-xs font-bold py-2.5 hover:bg-brand-teal/30 transition-all">
                        Upload to Directory
                    </button>
                </form>

                @if($projectFiles->isNotEmpty())
                    <div class="pt-3 border-t border-brand-teal/10 space-y-2">
                        <span class="text-[10px] uppercase font-bold text-brand-gray block">Project Files</span>
                        <div class="space-y-2 max-h-[200px] overflow-y-auto pr-1">
                            @foreach($projectFiles as $file)
                                <div class="flex justify-between items-center text-xs">
                                    <div class="min-w-0 flex-grow pr-2">
                                        <span class="text-brand-white font-semibold truncate block">{{ $file->filename }}</span>
                                        <span class="text-[9px] text-brand-gray uppercase">{{ $file->folder }} • v{{ $file->version }}</span>
                                    </div>
                                    <a href="{{ route('portal.file.download', $file->id) }}" class="text-[10px] text-brand-cyan hover:underline">Download</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Billing -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h4 class="text-sm font-bold text-brand-white">Billing &amp; Invoices</h4>
                <div class="space-y-3">
                    @forelse($project->invoices as $invoice)
                        <div class="flex items-center justify-between text-xs border-b border-brand-teal/5 pb-3 last:border-0 last:pb-0">
                            <div>
                                <span class="font-bold text-brand-white">Invoice #{{ $invoice->invoice_number }}</span>
                                <p class="text-brand-gray">@money($invoice->amount)</p>
                            </div>
                            
                            <div class="text-right">
                                <span class="rounded px-2 py-0.5 text-[9px] uppercase font-bold
                                    @if($invoice->status === 'paid') bg-emerald-500/20 text-emerald-400 border border-emerald-500/35
                                    @elseif($invoice->status === 'pending') bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/35
                                    @else bg-rose-500/20 text-rose-400 border border-rose-500/35
                                    @endif">
                                    {{ $invoice->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-brand-gray text-center py-2">No billing invoices found.</p>
                    @endforelse
                </div>
            </div>

        </div>
        </div>
    @endif
</div>
@endsection
