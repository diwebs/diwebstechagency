@extends('layouts.admin')

@section('title', 'Client Portals Administration - Diwebs operations')

@section('admin_content')
<div x-data="{
    milestones: [{ title: '', amount: '' }],
    addMilestone() {
        this.milestones.push({ title: '', amount: '' });
    },
    removeMilestone(index) {
        if (this.milestones.length > 1) {
            this.milestones.splice(index, 1);
        }
    }
}">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Client Portals Control</h1>
            <p class="text-sm text-brand-gray mt-1">Manage portal accesses, send project agreements, configure milestone sprints, and check client system audits.</p>
        </div>
    </div>

    <!-- Top Grid: Forms splits -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        
        <!-- Onboard new Client account -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-sm font-bold text-brand-cyan uppercase tracking-wider">Provision Client Workspace</h3>
            
            <form action="{{ route('admin.portal-control.create-client') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Client Full Name</label>
                        <input type="text" name="name" required placeholder="Sarah Jenkins" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Company / Institution Name</label>
                        <input type="text" name="company_name" placeholder="E-Gov Group" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Client Email Address</label>
                        <input type="email" name="email" required placeholder="client@company.com" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Initial Account Password</label>
                        <input type="text" name="password" value="password" required class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    👥 Register Client Account
                </button>
            </form>
        </div>

        <!-- Dispatch Proposal & Milestones -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-sm font-bold text-brand-cyan uppercase tracking-wider">Send Proposal &amp; Contract Sprints</h3>
            
            <form action="{{ route('admin.portal-control.send-proposal') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Select Target Client</label>
                        <select name="client_id" required class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none transition-all">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Project Working Title</label>
                        <input type="text" name="title" required placeholder="Federal CBT Exam Infrastructure" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Commission Budget</label>
                        <input type="number" name="budget" required placeholder="150000" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Agreed Terms &amp; Scope Summary</label>
                        <input type="text" name="description" required placeholder="ERD, Anti-cheat synchronization..." class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                    </div>
                </div>

                <!-- Milestone Dynamic Builder -->
                <div class="p-3 bg-brand-dark-secondary/50 border border-brand-teal/10 rounded-xl space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider">Configure Sprint Milestones</span>
                        <button type="button" @click="addMilestone()" class="text-[10px] text-brand-cyan font-bold hover:underline">+ Add Milestone</button>
                    </div>

                    <div class="space-y-2 max-h-36 overflow-y-auto pr-1">
                        <template x-for="(milestone, index) in milestones" :key="index">
                            <div class="flex gap-2">
                                <input type="text" x-model="milestone.title" :name="`milestone_titles[${index}]`" required placeholder="Sprint Title (e.g. Wireframes)" class="flex-grow rounded-lg border border-brand-teal/15 bg-brand-dark px-2 py-1.5 text-xs text-brand-white">
                                <input type="number" x-model="milestone.amount" :name="`milestone_amounts[${index}]`" required placeholder="Value ($)" class="w-24 rounded-lg border border-brand-teal/15 bg-brand-dark px-2 py-1.5 text-xs text-brand-white">
                                <button type="button" @click="removeMilestone(index)" class="text-rose-400 font-bold px-1">×</button>
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Legal Agreement Contract Body (Content)</label>
                    <textarea name="contract_content" required rows="3" placeholder="This Service Agreement is entered between Diwebs Tech Agency..." class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none transition-all"></textarea>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    ✉️ Dispatch Project Agreement Proposal
                </button>
            </form>
        </div>

    </div>

    <!-- Active Client Portals Telemetry lists -->
    <div class="space-y-6">
        
        <!-- Workspace projects -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
            <h3 class="text-sm font-bold text-brand-white mb-4">Active Projects Telemetry &amp; Sign-off</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-[10px] uppercase font-bold text-brand-gray border-b border-brand-teal/10">
                        <tr>
                            <th class="pb-3">Client</th>
                            <th class="pb-3">Project Title</th>
                            <th class="pb-3">Agreement Signed</th>
                            <th class="pb-3 text-right">Budget</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($projects as $p)
                            <tr>
                                <td class="py-3 font-semibold">{{ $p->client->name }}</td>
                                <td class="py-3 text-brand-gray">{{ $p->title }}</td>
                                <td class="py-3 font-mono text-[10px] text-brand-gray">
                                    @if($p->agreement_signed_at)
                                        <span class="text-emerald-400">✔ Signed {{ $p->agreement_signed_at->format('M d, Y') }}</span>
                                    @else
                                        <span class="text-rose-400">Pending Sign-off</span>
                                    @endif
                                </td>
                                <td class="py-3 text-right font-mono">@money($p->budget)</td>
                                <td class="py-3 text-center">
                                    <span class="rounded px-2 py-0.5 text-[9px] uppercase font-bold bg-brand-teal/15 text-brand-cyan border border-brand-teal/20">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.portal-control.export', $p->id) }}" class="rounded bg-brand-teal/15 border border-brand-teal/30 hover:bg-brand-teal/25 px-3 py-1.5 text-[10px] text-brand-cyan transition-all">
                                        Export JSON Report
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-brand-gray">No active project telemetry recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Service requests desk -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
            <h3 class="text-sm font-bold text-brand-white mb-4">Incoming Client Service Requests</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-[10px] uppercase font-bold text-brand-gray border-b border-brand-teal/10">
                        <tr>
                            <th class="pb-3">Client</th>
                            <th class="pb-3">Request Summary</th>
                            <th class="pb-3">Segment Category</th>
                            <th class="pb-3">Budget Range</th>
                            <th class="pb-3">Target Date</th>
                            <th class="pb-3 text-right">CRM Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                        @forelse($serviceRequests as $req)
                            <tr>
                                <td class="py-3 font-semibold">{{ $req->client->name }}</td>
                                <td class="py-3 text-brand-gray">
                                    <div class="font-semibold text-brand-white">{{ $req->title }}</div>
                                    <div class="text-[10px] text-brand-gray">{{ Str::limit($req->description, 50) }}</div>
                                </td>
                                <td class="py-3 text-brand-cyan text-[10px] font-bold uppercase">{{ $req->service_type }}</td>
                                <td class="py-3 font-mono">{{ $req->budget_range }}</td>
                                <td class="py-3 text-brand-gray">{{ $req->deadline->format('M d, Y') }}</td>
                                <td class="py-3 text-right">
                                    <span class="rounded px-2.5 py-0.5 text-[9px] uppercase font-bold bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/35">
                                        {{ $req->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-brand-gray">No service request inbox items.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
