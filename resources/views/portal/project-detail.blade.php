@extends('layouts.app')

@section('title', $project->title . ' - Project Details')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('portal.dashboard') }}" class="text-xs text-brand-cyan hover:underline">← Back to Dashboard</a>
            <h1 class="text-2xl font-bold text-brand-white mt-2">{{ $project->title }}</h1>
            <p class="text-sm text-brand-gray mt-1">{{ $project->description }}</p>
        </div>
        <span class="rounded-full px-4 py-1.5 text-xs font-bold uppercase
            @if($project->status === 'active') bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
            @elseif($project->status === 'completed') bg-emerald-950 text-emerald-400 border border-emerald-500/20
            @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/10
            @endif">
            {{ $project->status }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Milestones  -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider">Project Milestones</h3>

            @foreach($project->milestones as $milestone)
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-base font-bold text-brand-white">{{ $milestone->title }}</h4>
                            <p class="text-xs text-brand-gray mt-1">{{ $milestone->description }}</p>
                        </div>
                        <span class="rounded px-2 py-0.5 text-[10px] font-bold capitalize
                            @if($milestone->status === 'approved') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                            @elseif($milestone->status === 'working') bg-brand-teal/10 text-brand-cyan border border-brand-teal/20 animate-pulse
                            @else bg-brand-dark-secondary text-brand-gray border border-brand-teal/10
                            @endif">
                            {{ $milestone->status }}
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs text-brand-gray">
                        <span>Due: {{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : 'N/A' }}</span>
                        <strong class="text-brand-white">${{ number_format($milestone->amount, 2) }}</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Sidebar: Actions -->
        <div class="space-y-6">
            <!-- Agreement Signature -->
            <div class="glass-card rounded-2xl p-6">
                <h4 class="text-sm font-bold text-brand-white mb-4">E-Agreement Status</h4>
                @if($project->agreement_signed_at)
                    <div class="rounded-lg bg-emerald-950/30 border border-emerald-500/20 p-4 text-center">
                        <span class="text-sm text-emerald-400 font-bold">✔ Signed on {{ $project->agreement_signed_at->format('M d, Y') }}</span>
                    </div>
                @else
                    <p class="text-xs text-brand-gray mb-4">Sign the project agreement to authorize Diwebs to commence active development.</p>
                    <form action="{{ route('portal.project.sign', $project->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-xs font-bold text-brand-dark-secondary hover:opacity-90 cursor-pointer">
                            Sign Project Agreement
                        </button>
                    </form>
                @endif
            </div>

            <!-- File Upload -->
            <div class="glass-card rounded-2xl p-6">
                <h4 class="text-sm font-bold text-brand-white mb-4">Upload Project File</h4>
                <form action="{{ route('portal.project.upload', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="project_file" id="project_file" 
                           class="block w-full text-xs text-brand-gray file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand-teal/10 file:text-brand-cyan hover:file:bg-brand-teal/20 mb-4">
                    <button type="submit" class="w-full rounded-md border border-brand-teal/30 bg-brand-teal/10 hover:bg-brand-teal/20 py-2.5 text-xs font-bold text-brand-cyan transition-all cursor-pointer">
                        Upload Attachment
                    </button>
                </form>
            </div>

            <!-- Invoice List -->
            <div class="glass-card rounded-2xl p-6">
                <h4 class="text-sm font-bold text-brand-white mb-4">Billing Invoices</h4>
                <div class="space-y-3">
                    @foreach($project->invoices as $invoice)
                        <div class="flex items-center justify-between text-xs border-b border-brand-teal/10 pb-3 last:border-0">
                            <div>
                                <span class="font-bold text-brand-white">{{ $invoice->invoice_number }}</span>
                                <p class="text-brand-gray">${{ number_format($invoice->amount, 2) }}</p>
                            </div>
                            <span class="rounded px-2 py-0.5 text-[10px] font-bold capitalize
                                @if($invoice->status === 'paid') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                @else bg-rose-950 text-rose-400 border border-rose-500/20
                                @endif">
                                {{ $invoice->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
