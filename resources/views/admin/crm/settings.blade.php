@extends('layouts.admin')

@section('title', 'CRM Settings - Diwebs CRM')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">CRM Settings</h1>
            <p class="text-xs text-brand-gray mt-1">Configure automation variables, thresholds, and CRM rules.</p>
        </div>
    </div>

    <!-- Settings Card -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 w-full max-w-2xl">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2 mb-6">CRM Variables &amp; Automations</h3>
        
        <form action="{{ route('admin.crm.settings.update') }}" method="POST" class="space-y-6 text-xs text-brand-white">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Auto assignment toggle -->
                <div>
                    <label class="block text-brand-gray mb-2 uppercase tracking-wider text-[9px] font-bold">Auto Assign Leads</label>
                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" name="auto_assign_leads" value="1" {{ $settings['auto_assign_leads'] ? 'checked' : '' }}
                               class="rounded bg-brand-dark border-brand-teal/20 text-brand-cyan focus:ring-0">
                        <span class="text-brand-gray">Enable Round-Robin Assignment</span>
                    </div>
                </div>

                <!-- Minimum lead score -->
                <div>
                    <label class="block text-brand-gray mb-2 uppercase tracking-wider text-[9px] font-bold">Minimum Qualified Lead Score</label>
                    <input type="number" name="min_lead_score" value="{{ $settings['min_lead_score'] }}" required
                           class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <span class="block text-[10px] text-brand-gray/60 mt-1">Leads with scores exceeding this threshold will automatically qualify.</span>
                </div>

                <!-- Currency symbol -->
                <div>
                    <label class="block text-brand-gray mb-2 uppercase tracking-wider text-[9px] font-bold">Default Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] }}" required maxlength="5"
                           class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                </div>

                <!-- Ticket escalation hours -->
                <div>
                    <label class="block text-brand-gray mb-2 uppercase tracking-wider text-[9px] font-bold">Support Ticket Escalation Window (Hours)</label>
                    <input type="number" name="ticket_escalation_hours" value="{{ $settings['ticket_escalation_hours'] }}" required min="1"
                           class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <span class="block text-[10px] text-brand-gray/60 mt-1">Unresolved tickets older than this will escalate to Urgent priority.</span>
                </div>
            </div>

            <div class="border-t border-brand-teal/10 pt-4 flex justify-end">
                <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
                    Save CRM Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
