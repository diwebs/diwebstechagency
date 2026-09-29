@extends('layouts.admin')

@section('title', 'Comms Hub - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showLogComms: false, channelFilter: 'All' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Communication Center</h1>
            <p class="text-xs text-brand-gray mt-1">Track customer interaction history across Email, WhatsApp, SMS, and Call notes.</p>
        </div>
        <button @click="showLogComms = !showLogComms" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Log Interaction
        </button>
    </div>

    <!-- Log Comm form -->
    <div x-show="showLogComms" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Record Customer Interaction</h3>
        <form action="{{ route('admin.crm.communications.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Channel</label>
                <select name="channel" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="email">Email Session</option>
                    <option value="WhatsApp">WhatsApp Message</option>
                    <option value="SMS">SMS dispatch</option>
                    <option value="call_note">Inbound/Outbound Call Note</option>
                    <option value="internal_note">Internal Account Note</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Direction</label>
                <select name="direction" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="inbound">Inbound (From Client)</option>
                    <option value="outbound">Outbound (To Client)</option>
                    <option value="internal">Internal Team Exchange</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Logged By (Staff)</label>
                <select name="staff_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">Select Staff</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Sender Label</label>
                <input type="text" name="sender" placeholder="Diwebs Sales Rep" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Recipient Label</label>
                <input type="text" name="recipient" placeholder="client@vanguard.com" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Link Lead</label>
                <select name="crm_lead_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">None</option>
                    @foreach($leads as $ld)
                        <option value="{{ $ld->id }}">{{ $ld->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Link Client</label>
                <select name="crm_client_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">None</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->contact_person }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Message Content / Interaction Summary</label>
                <textarea name="content" rows="3" required placeholder="Paste chat log transcript, call summary, or email copy here..." class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none"></textarea>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showLogComms = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Log Interaction</button>
            </div>
        </form>
    </div>

    <!-- Filter Toolbar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <span class="text-xs text-brand-gray font-bold">Channel filter:</span>
        @foreach(['All', 'email', 'WhatsApp', 'SMS', 'call_note', 'internal_note'] as $ch)
            <button @click="channelFilter = '{{ $ch }}'" class="rounded px-3 py-1 text-xs font-bold transition-all border cursor-pointer
                " :class="channelFilter === '{{ $ch }}' ? 'bg-brand-cyan border-brand-cyan text-brand-dark-secondary' : 'bg-brand-dark border-brand-teal/20 text-brand-gray hover:text-brand-cyan'">
                {{ $ch === 'All' ? 'All Channels' : ucfirst(str_replace('_', ' ', $ch)) }}
            </button>
        @endforeach
    </div>

    <!-- Communication Timeline -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-6">Interaction History Timeline</h3>
        
        <div class="space-y-6 relative border-l border-brand-teal/15 pl-6 ml-3">
            @forelse($messages as $msg)
                <div class="relative" x-show="channelFilter === 'All' || channelFilter === '{{ $msg->channel }}'">
                    <!-- Bullet icon -->
                    <span class="absolute -left-[35px] top-1 h-5 w-5 rounded-full border border-brand-cyan bg-brand-dark flex items-center justify-center text-[10px] text-brand-cyan">
                        @if($msg->channel === 'email') ✉
                        @elseif($msg->channel === 'WhatsApp') 💬
                        @elseif($msg->channel === 'SMS') 📱
                        @else 📝
                        @endif
                    </span>
                    
                    <div class="glass-card rounded-xl p-4 border border-brand-teal/10 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-brand-white">{{ $msg->sender ?? 'System' }}</span>
                                <span class="text-brand-gray font-mono mx-1">→</span>
                                <span class="text-brand-gray">{{ $msg->recipient ?? 'Client' }}</span>
                            </div>
                            <span class="text-brand-cyan font-bold font-mono">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <p class="text-brand-white text-xs leading-relaxed whitespace-pre-line">{{ $msg->content }}</p>
                        
                        <div class="flex justify-between items-center text-[10px] text-brand-gray/60 pt-2 border-t border-brand-teal/5">
                            <span>Logged via {{ ucfirst(str_replace('_', ' ', $msg->channel)) }} | Direction: {{ ucfirst($msg->direction) }}</span>
                            @if($msg->staff)
                                <span>Agent: {{ $msg->staff->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-brand-gray">No client communication history logged yet.</p>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
