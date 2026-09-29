@extends('layouts.admin')

@section('title', 'Sales Pipeline Kanban - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="dealsPipelineManager()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Sales Pipelines</h1>
            <p class="text-xs text-brand-gray mt-1">Drag and drop deal cards to advance stages in the sales lifecycle.</p>
        </div>
        <button @click="showAddDeal = !showAddDeal" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Register Deal Card
        </button>
    </div>

    <!-- Deal Intake Form -->
    <div x-show="showAddDeal" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">New Sales Pipeline Deal</h3>
        <form action="{{ route('admin.crm.deals.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Deal Title</label>
                <input type="text" name="title" required placeholder="E-Gov Portal Redesign" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Service Category</label>
                <input type="text" name="service" required placeholder="Web Development / AI Integration" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Deal Value (₦)</label>
                <input type="number" name="deal_value" step="0.01" required placeholder="12500000.00" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Lead Associated</label>
                <select name="crm_lead_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">No Lead association</option>
                    @foreach($leads as $ld)
                        <option value="{{ $ld->id }}">{{ $ld->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Client Associated</label>
                <select name="crm_client_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">No Client profile link</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->contact_person }} ({{ $cl->company_name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Expected Close Date</label>
                <input type="date" name="expected_close_date" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Probability (%)</label>
                <input type="number" name="probability_percent" value="50" min="0" max="100" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Pipeline Stage</label>
                <select name="stage" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    @foreach($pipelineStages as $stg)
                        <option value="{{ $stg }}">{{ $stg }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Assigned Sales Manager</label>
                <select name="assigned_sales_rep_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">Unassigned</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddDeal = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Register Pipeline Card</button>
            </div>
        </form>
    </div>

    <!-- Kanban Board Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-8 gap-4 overflow-x-auto pb-6">
        @foreach($pipelineStages as $stage)
            <div class="bg-brand-dark-secondary/35 rounded-xl border border-brand-teal/10 p-3 min-w-[200px] flex flex-col h-[600px]"
                 @dragover.prevent=""
                 @drop="handleDrop($event, '{{ $stage }}')">
                <div class="flex items-center justify-between border-b border-brand-teal/10 pb-2 mb-3">
                    <span class="text-[10px] uppercase font-bold text-brand-cyan tracking-wider truncate max-w-[130px]">{{ $stage }}</span>
                    <span class="rounded bg-brand-teal/15 px-2 py-0.5 text-[10px] font-bold text-brand-cyan font-mono" x-text="getDealsCount('{{ $stage }}')">0</span>
                </div>

                <!-- Cards Queue -->
                <div class="flex-1 space-y-3 overflow-y-auto pr-1">
                    <template x-for="deal in getDealsForStage('{{ $stage }}')" :key="deal.id">
                        <div class="glass-card rounded-xl p-3 border border-brand-teal/15 hover:border-brand-teal/30 transition-all cursor-grab active:cursor-grabbing text-xs space-y-2 select-none"
                             draggable="true"
                             @dragstart="handleDragStart($event, deal)">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] uppercase font-semibold text-brand-cyan tracking-wider font-mono" x-text="'DWS-' + deal.id"></span>
                                <span class="text-[9px] text-brand-gray" x-text="deal.probability_percent + '%'"></span>
                            </div>
                            <strong class="block text-brand-white font-bold leading-snug" x-text="deal.title"></strong>
                            <span class="block text-brand-gray text-[10px]" x-text="deal.service"></span>
                            
                            <div class="flex items-center justify-between pt-2 border-t border-brand-teal/5">
                                <span class="text-brand-cyan font-mono font-bold" x-text="'₦' + formatCurrency(deal.deal_value)"></span>
                                <span class="text-[9px] text-brand-gray/60" x-text="deal.expected_close_date ? formatDate(deal.expected_close_date) : 'N/A'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function dealsPipelineManager() {
        return {
            showAddDeal: false,
            deals: @json($deals),
            
            getDealsForStage(stage) {
                return this.deals.filter(d => d.stage === stage);
            },
            
            getDealsCount(stage) {
                return this.getDealsForStage(stage).length;
            },
            
            handleDragStart(event, deal) {
                event.dataTransfer.setData('text/plain', deal.id);
            },
            
            async handleDrop(event, newStage) {
                const dealId = event.dataTransfer.getData('text/plain');
                const deal = this.deals.find(d => d.id == dealId);
                if (deal && deal.stage !== newStage) {
                    const oldStage = deal.stage;
                    deal.stage = newStage;
                    
                    try {
                        const response = await fetch(`/admin/crm/deals/${dealId}/stage`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ stage: newStage })
                        });
                        const data = await response.json();
                        if (data.status !== 'success') {
                            deal.stage = oldStage;
                            alert('Failed to update stage in database.');
                        }
                    } catch (e) {
                        deal.stage = oldStage;
                        console.error(e);
                    }
                }
            },
            
            formatCurrency(val) {
                return Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            },
            
            formatDate(str) {
                const d = new Date(str);
                return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
            }
        };
    }
</script>
@endsection
