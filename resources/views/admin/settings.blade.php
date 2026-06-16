@extends('layouts.admin')

@section('title', 'System Settings - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">System Settings &amp; Branding</h1>
        <p class="text-sm text-brand-gray mt-1">Configure global application branding, toggle maintenance modes, and schedule automated backups.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Configuration form -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Global Configurations</h3>
                
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- App Name -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Application Name Branding</label>
                        <input type="text" 
                               name="app_name" 
                               value="{{ $settings['app_name'] }}" 
                               required 
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Referral Bonus Amount -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Default Referral Bonus Amount ($)</label>
                        <input type="number" 
                               name="referral_bonus_amount" 
                               value="{{ $settings['referral_bonus_amount'] }}" 
                               required 
                               step="0.01"
                               min="0"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <span class="block text-[10px] text-brand-gray mt-1">Configure the amount credited to the referrer when a new client signs up using their code.</span>
                    </div>

                    <!-- System state flags -->
                    <div class="space-y-4 pt-4 border-t border-brand-teal/10">
                        <!-- Maintenance Mode -->
                        <div class="flex items-start gap-3">
                            <input type="checkbox" 
                                   id="maintenance_mode" 
                                   name="maintenance_mode" 
                                   value="1" 
                                   {{ $settings['maintenance_mode'] ? 'checked' : '' }}
                                   class="h-4.5 w-4.5 rounded border-brand-teal/15 bg-brand-dark-secondary text-brand-cyan focus:ring-0 cursor-pointer">
                            <div>
                                <label for="maintenance_mode" class="block text-xs font-bold text-brand-white uppercase cursor-pointer">Enable Maintenance Mode</label>
                                <span class="block text-[10px] text-brand-gray mt-0.5">Places the corporate interface in offline mode for all role metrics except administrators.</span>
                            </div>
                        </div>

                        <!-- Registration -->
                        <div class="flex items-start gap-3">
                            <input type="checkbox" 
                                   id="allow_registration" 
                                   name="allow_registration" 
                                   value="1" 
                                   {{ $settings['allow_registration'] ? 'checked' : '' }}
                                   class="h-4.5 w-4.5 rounded border-brand-teal/15 bg-brand-dark-secondary text-brand-cyan focus:ring-0 cursor-pointer">
                            <div>
                                <label for="allow_registration" class="block text-xs font-bold text-brand-white uppercase cursor-pointer">Allow Guest Registrations</label>
                                <span class="block text-[10px] text-brand-gray mt-0.5">Allow public onboarding register triggers on `/register`. Toggling off blocks account registration.</span>
                            </div>
                        </div>

                        <!-- Auto Backups -->
                        <div class="flex items-start gap-3">
                            <input type="checkbox" 
                                   id="auto_backups" 
                                   name="auto_backups" 
                                   value="1" 
                                   {{ $settings['auto_backups'] ? 'checked' : '' }}
                                   class="h-4.5 w-4.5 rounded border-brand-teal/15 bg-brand-dark-secondary text-brand-cyan focus:ring-0 cursor-pointer">
                            <div>
                                <label for="auto_backups" class="block text-xs font-bold text-brand-white uppercase cursor-pointer">Automated DB Backup Rotation</label>
                                <span class="block text-[10px] text-brand-gray mt-0.5">Triggers database backup snapshots every 24 hours (highly optimized for shared hosting environments).</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-brand-teal/10">
                        <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Tools/Controls -->
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Maintenance Actions</h3>
                
                <div class="space-y-2.5">
                    <button class="w-full rounded bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 text-brand-cyan px-4 py-2.5 text-xs font-bold text-center transition-all">
                        🧹 Clear Cache
                    </button>
                    <button class="w-full rounded bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 text-brand-cyan px-4 py-2.5 text-xs font-bold text-center transition-all">
                        ⚙️ Optimize Database
                    </button>
                    <button class="w-full rounded bg-rose-950/20 border border-rose-500/20 text-rose-400 px-4 py-2.5 text-xs font-bold text-center transition-all">
                        💀 Flush Sessions
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
