@extends('layouts.admin')

@section('title', 'Contracts Hub - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddContract: false, signingContractId: null }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Contracts &amp; Legal Agreements</h1>
            <p class="text-xs text-brand-gray mt-1">Manage NDAs, Service Agreements, and contractor files with e-signature audits.</p>
        </div>
        <button @click="showAddContract = !showAddContract" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Generate Agreement
        </button>
    </div>

    <!-- Add Contract Form -->
    <div x-show="showAddContract" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Contract Agreement Provision</h3>
        <form action="{{ route('admin.crm.contracts.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Contract number</label>
                <input type="text" name="contract_number" required placeholder="DWS-AGR-2026-001" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Agreement Title</label>
                <input type="text" name="title" required placeholder="Service SLA - Federal CBT Project" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Contract Type</label>
                <select name="contract_type" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="NDA">NDA (Non-Disclosure Agreement)</option>
                    <option value="Service Agreement">Service Agreement</option>
                    <option value="Partnership Agreement">Partnership Agreement</option>
                    <option value="Contractor Agreement">Contractor Agreement</option>
                    <option value="Employee Agreement">Employee Agreement</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Client Link</label>
                <select name="crm_client_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">No Client Link</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->contact_person }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">B2B Company Link</label>
                <select name="crm_company_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">No Company Link</option>
                    @foreach($companies as $cp)
                        <option value="{{ $cp->id }}">{{ $cp->organization_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Agreement Valuation (₦)</label>
                <input type="number" name="value" step="0.01" value="0.00" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Start Date</label>
                <input type="date" name="start_date" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">End/Expiration Date</label>
                <input type="date" name="end_date" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Status</label>
                <select name="status" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Draft">Draft Mode</option>
                    <option value="Active">Active Agreement</option>
                    <option value="Expired">Expired</option>
                    <option value="Terminated">Terminated</option>
                </select>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddContract = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Issue Agreement</button>
            </div>
        </form>
    </div>

    <!-- Contracts Listing -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Contracts Directory</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Contract Info</th>
                        <th class="pb-3">Type</th>
                        <th class="pb-3">Client Partner</th>
                        <th class="pb-3">Value</th>
                        <th class="pb-3">Period</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">E-Signature Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($contracts as $con)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors text-brand-white">
                            <td class="py-4">
                                <strong class="block text-sm">{{ $con->title }}</strong>
                                <span class="text-brand-gray/60 font-mono text-[9px]">{{ $con->contract_number }}</span>
                            </td>
                            <td class="py-4 font-bold text-brand-cyan">{{ $con->contract_type }}</td>
                            <td class="py-4">
                                @if($con->client)
                                    <span>{{ $con->client->contact_person }}</span>
                                @elseif($con->company)
                                    <span>{{ $con->company->organization_name }}</span>
                                @else
                                    <span class="text-brand-gray/50">N/A</span>
                                @endif
                            </td>
                            <td class="py-4 font-mono font-bold text-emerald-400">₦{{ number_format($con->value, 2) }}</td>
                            <td class="py-4 text-brand-gray text-[10px]">
                                {{ $con->start_date->format('Y-m-d') }} to {{ $con->end_date ? $con->end_date->format('Y-m-d') : 'Ongoing' }}
                            </td>
                            <td class="py-4">
                                <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase
                                    @if($con->status === 'Active') bg-emerald-950 text-emerald-400
                                    @elseif($con->status === 'Expired') bg-rose-950 text-rose-400
                                    @else bg-brand-teal/10 text-brand-white
                                    @endif">
                                    {{ $con->status }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                @if(!$con->signature_data)
                                    <button @click="signingContractId = {{ $con->id }}; $nextTick(() => initSignaturePad())" class="rounded bg-brand-cyan text-brand-dark-secondary px-2.5 py-1 text-[10px] font-bold cursor-pointer">
                                        Sign E-Signature
                                    </button>
                                @else
                                    <div class="flex items-center justify-end gap-1.5 text-emerald-400 font-bold text-[10px]">
                                        <span>✔ Signed</span>
                                        <img src="{{ $con->signature_data }}" class="h-6 bg-white border border-brand-teal/30 rounded p-0.5" alt="Signature">
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-brand-gray">No contract agreements registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $contracts->links() }}
        </div>
    </div>

    <!-- E-Signature Modal Drawer -->
    <div x-show="signingContractId !== null" class="fixed inset-0 bg-brand-dark/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="glass-card rounded-2xl border border-brand-teal/35 w-full max-w-md p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-brand-teal/10 pb-2">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan">Sign Agreement Digitally</h3>
                <button @click="signingContractId = null" class="text-brand-gray hover:text-brand-white">✕</button>
            </div>
            
            <p class="text-xs text-brand-gray">Use your mouse or touchpad to sign in the box below:</p>
            
            <div class="bg-white rounded-xl border border-brand-teal/20 overflow-hidden relative h-40">
                <canvas id="signatureCanvas" class="w-full h-full cursor-crosshair"></canvas>
            </div>

            <div class="flex justify-between gap-2 text-xs">
                <button type="button" @click="clearSignaturePad()" class="rounded bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Clear Box</button>
                <form id="signatureForm" :action="'/admin/crm/contracts/' + signingContractId + '/sign'" method="POST">
                    @csrf
                    <input type="hidden" name="signature_data" id="signatureDataInput">
                    <button type="submit" @click="saveSignaturePad($event)" class="rounded bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">
                        Authenticate &amp; Sign
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let canvas, ctx, drawing = false;

    function initSignaturePad() {
        canvas = document.getElementById('signatureCanvas');
        if (!canvas) return;
        ctx = canvas.getContext('2d');
        
        // Match canvas dimensions to offset container
        canvas.width = canvas.parentElement.clientWidth;
        canvas.height = canvas.parentElement.clientHeight;
        
        ctx.strokeStyle = '#02182B'; // Dark ink color
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        
        // Touch events
        canvas.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            ctx.beginPath();
            ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
            drawing = true;
        });
        canvas.addEventListener('touchmove', (e) => {
            if (!drawing) return;
            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
            ctx.stroke();
            e.preventDefault();
        });
        canvas.addEventListener('touchend', stopDrawing);
    }

    function startDrawing(e) {
        drawing = true;
        ctx.beginPath();
        const rect = canvas.getBoundingClientRect();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    function draw(e) {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
    }

    function stopDrawing() {
        drawing = false;
    }

    function clearSignaturePad() {
        if (canvas) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    function saveSignaturePad(event) {
        if (!canvas) return;
        
        // Get signature base64 data
        const dataUrl = canvas.toDataURL();
        document.getElementById('signatureDataInput').value = dataUrl;
    }
</script>
@endsection
