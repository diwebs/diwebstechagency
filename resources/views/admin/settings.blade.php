@extends('layouts.admin')

@section('title', 'System Settings - Admin Control Center')

@section('admin_content')
<div x-data="{ activeTab: 'branding' }">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">System Settings &amp; Control Center</h1>
            <p class="text-sm text-brand-gray mt-1">Configure global application branding, SEO metadata, SMTP credentials, and perform database maintenance rotations.</p>
        </div>
    </div>

    <!-- Premium Tab Navigation Bar -->
    <div class="flex items-center gap-1.5 overflow-x-auto border-b border-brand-teal/15 pb-px mb-8 scrollbar-none">
        <button @click="activeTab = 'branding'" 
                :class="activeTab === 'branding' ? 'text-brand-cyan border-brand-cyan bg-brand-teal/5 font-semibold' : 'text-brand-gray border-transparent hover:text-brand-white hover:border-brand-teal/40'" 
                class="px-5 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-all select-none cursor-pointer whitespace-nowrap">
            🏢 Branding &amp; General
        </button>
        <button @click="activeTab = 'seo'" 
                :class="activeTab === 'seo' ? 'text-brand-cyan border-brand-cyan bg-brand-teal/5 font-semibold' : 'text-brand-gray border-transparent hover:text-brand-white hover:border-brand-teal/40'" 
                class="px-5 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-all select-none cursor-pointer whitespace-nowrap">
            🔍 SEO &amp; Analytics
        </button>
        <button @click="activeTab = 'mail'" 
                :class="activeTab === 'mail' ? 'text-brand-cyan border-brand-cyan bg-brand-teal/5 font-semibold' : 'text-brand-gray border-transparent hover:text-brand-white hover:border-brand-teal/40'" 
                class="px-5 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-all select-none cursor-pointer whitespace-nowrap">
            ✉️ Mail Configuration
        </button>
        <button @click="activeTab = 'oauth'" 
                :class="activeTab === 'oauth' ? 'text-brand-cyan border-brand-cyan bg-brand-teal/5 font-semibold' : 'text-brand-gray border-transparent hover:text-brand-white hover:border-brand-teal/40'" 
                class="px-5 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-all select-none cursor-pointer whitespace-nowrap">
            🔐 Social Login
        </button>
        <button @click="activeTab = 'maintenance'" 
                :class="activeTab === 'maintenance' ? 'text-brand-cyan border-brand-cyan bg-brand-teal/5 font-semibold' : 'text-brand-gray border-transparent hover:text-brand-white hover:border-brand-teal/40'" 
                class="px-5 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-all select-none cursor-pointer whitespace-nowrap">
            ⚙️ Maintenance Center
        </button>
    </div>

    <!-- Tab Panels -->
    <div class="grid grid-cols-1 gap-8">
        
        <!-- TAB 1: Branding & General -->
        <div x-show="activeTab === 'branding'" x-transition class="space-y-6 max-w-4xl">
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

                    <!-- Session Inactivity Idle Timeout -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Session Inactivity Timeout (Minutes)</label>
                        <input type="number" 
                               name="session_idle_timeout" 
                               value="{{ $settings['session_idle_timeout'] }}" 
                               required 
                               min="1"
                               max="1440"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <span class="block text-[10px] text-brand-gray mt-1">Specify the duration of inactivity (in minutes) after which an inactive user session will be automatically terminated and logged out.</span>
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
                            Save Branding Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 2: SEO & Analytics -->
        <div x-show="activeTab === 'seo'" x-transition class="space-y-6 max-w-4xl">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">SEO &amp; Google Analytics Management</h3>
                
                <form action="{{ route('admin.settings.seo.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Google Analytics Measurement ID -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Google Analytics Measurement ID</label>
                            <input type="text" 
                                   name="google_analytics_id" 
                                   value="{{ $settings['google_analytics_id'] }}" 
                                   placeholder="G-XXXXXXXXXX"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <span class="block text-[10px] text-brand-gray mt-1">Leave empty to temporarily disable GA tracking across the entire web ecosystem.</span>
                        </div>

                        <!-- Meta Title Suffix -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Global Meta Title Suffix</label>
                            <input type="text" 
                                   name="seo_meta_title_suffix" 
                                   value="{{ $settings['seo_meta_title_suffix'] }}" 
                                   required
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <span class="block text-[10px] text-brand-gray mt-1">Appended to the page-specific title (e.g. "Services | Diwebs Tech Agency").</span>
                        </div>
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Global Meta Description</label>
                        <textarea name="seo_meta_description" 
                                  rows="3" 
                                  required
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all resize-none">{{ $settings['seo_meta_description'] }}</textarea>
                        <span class="block text-[10px] text-brand-gray mt-1">Default meta description for indexing search engines (recommended length: 150-160 characters).</span>
                    </div>

                    <!-- Meta Keywords -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Global Meta Keywords</label>
                        <input type="text" 
                               name="seo_meta_keywords" 
                               value="{{ $settings['seo_meta_keywords'] }}" 
                               required
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <span class="block text-[10px] text-brand-gray mt-1">Comma-separated key phrases summarizing agency solutions and training systems.</span>
                    </div>

                    <!-- OG Image URL -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Default Open Graph (OG) Image URL</label>
                        <input type="url" 
                               name="seo_og_image_url" 
                               value="{{ $settings['seo_og_image_url'] }}" 
                               placeholder="https://..."
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <span class="block text-[10px] text-brand-gray mt-1">Preview thumbnail displayed when site links are shared on social portals.</span>
                    </div>

                    <!-- Google Reviews URL -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Google Reviews Page URL</label>
                        <input type="url" 
                               name="google_reviews_url" 
                               value="{{ $settings['google_reviews_url'] }}" 
                               placeholder="https://search.google.com/local/writereview?placeid=..."
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <span class="block text-[10px] text-brand-gray mt-1">Direct destination link to let clients share testimonials on your business page. Fallback is set to search for "diwebs tech agency review".</span>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-brand-teal/10">
                        <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                            Update SEO &amp; Analytics
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 3: Mail Configuration -->
        <div x-show="activeTab === 'mail'" x-transition class="space-y-8 max-w-4xl">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Dynamic Mail &amp; SMTP settings</h3>
                
                <form action="{{ route('admin.settings.mail.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Mail Mailer -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Mail Transport/Mailer</label>
                            <select name="mail_mailer" 
                                    class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all cursor-pointer">
                                <option value="smtp" {{ $settings['mail_mailer'] === 'smtp' ? 'selected' : '' }}>SMTP Server</option>
                                <option value="log" {{ $settings['mail_mailer'] === 'log' ? 'selected' : '' }}>Local Log Channel</option>
                                <option value="sendmail" {{ $settings['mail_mailer'] === 'sendmail' ? 'selected' : '' }}>Local Sendmail</option>
                                <option value="array" {{ $settings['mail_mailer'] === 'array' ? 'selected' : '' }}>Array (In-Memory Testing)</option>
                            </select>
                            <span class="block text-[10px] text-brand-gray mt-1">Select the driver. SMTP is required for live delivery.</span>
                        </div>

                        <!-- Mail Host -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">SMTP Host Server</label>
                            <input type="text" 
                                   name="mail_host" 
                                   value="{{ $settings['mail_host'] }}" 
                                   placeholder="mail.example.com"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Mail Port -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">SMTP Port</label>
                            <input type="number" 
                                   name="mail_port" 
                                   value="{{ $settings['mail_port'] }}" 
                                   placeholder="465"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Mail Username -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">SMTP Username</label>
                            <input type="text" 
                                   name="mail_username" 
                                   value="{{ $settings['mail_username'] }}" 
                                   placeholder="noreply@example.com"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Mail Password (with Visibility Toggle using Alpine.js) -->
                        <div x-data="{ showPass: false }">
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">SMTP Password</label>
                            <div class="relative">
                                <input :type="showPass ? 'text' : 'password'" 
                                       name="mail_password" 
                                       placeholder="••••••••••••"
                                       class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 pl-4 pr-10 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                                <button type="button" 
                                        @click="showPass = !showPass" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-brand-gray hover:text-brand-cyan transition-colors cursor-pointer">
                                    <span class="text-xs" x-text="showPass ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                            <span class="block text-[10px] text-brand-gray mt-1">Leave empty to keep currently saved password.</span>
                        </div>

                        <!-- Mail Encryption Scheme -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Encryption Scheme</label>
                            <select name="mail_scheme" 
                                    class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all cursor-pointer">
                                <option value="null" {{ empty($settings['mail_scheme']) ? 'selected' : '' }}>None (Clear Text / 587)</option>
                                <option value="ssl" {{ $settings['mail_scheme'] === 'ssl' ? 'selected' : '' }}>SSL (Encrypted / 465)</option>
                                <option value="tls" {{ $settings['mail_scheme'] === 'tls' ? 'selected' : '' }}>TLS (Secure Transport)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mail From Address -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Global 'From' Email Address</label>
                            <input type="email" 
                                   name="mail_from_address" 
                                   value="{{ $settings['mail_from_address'] }}" 
                                   required
                                   placeholder="noreply@diwebstechagency.website"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <span class="block text-[10px] text-brand-gray mt-1">All system emails will appear sent from this address.</span>
                        </div>

                        <!-- Mail From Name -->
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Global 'From' Display Name</label>
                            <input type="text" 
                                   name="mail_from_name" 
                                   value="{{ $settings['mail_from_name'] }}" 
                                   required
                                   placeholder="Diwebs Tech Agency"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-brand-teal/10">
                        <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                            Update Mail Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- SMTP Connection Test Tool -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-2">Verify Connection &amp; Send Test Email</h3>
                <p class="text-xs text-brand-gray mb-5">Confirm your dynamic mail parameters are fully configured and functional by dispatching a verification test email.</p>

                <form action="{{ route('admin.settings.mail.test') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Recipient Email Address</label>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <input type="email" 
                                   name="test_email" 
                                   required 
                                   placeholder="verify@example.com"
                                   class="w-full max-w-md rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <button type="submit" class="rounded-lg bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 text-brand-cyan px-5 py-2.5 text-xs font-bold transition-all cursor-pointer whitespace-nowrap">
                                🚀 Send Test Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 4: OAuth / Social Login -->
        <div x-show="activeTab === 'oauth'" x-transition class="space-y-6 max-w-4xl">

            <!-- Info Banner -->
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/15 flex gap-4 items-start">
                <span class="text-2xl mt-0.5">🔐</span>
                <div>
                    <h3 class="text-sm font-bold text-brand-white">Social Login Configuration</h3>
                    <p class="text-xs text-brand-gray mt-1 leading-relaxed">Enable your users to sign in with Google, Apple, or Microsoft accounts. You must create an OAuth app in each provider's developer console, copy the credentials below, and set the correct <strong class="text-brand-cyan">Redirect URI</strong> in both your app and the provider's dashboard.</p>
                </div>
            </div>

            <form action="{{ route('admin.settings.oauth.update') }}" method="POST" class="space-y-6">
                @csrf

                {{-- ============ GOOGLE ============ --}}
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <div class="flex items-center justify-between border-b border-brand-teal/10 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" class="w-5 h-5"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-brand-white">Google OAuth 2.0</h4>
                                <p class="text-[10px] text-brand-gray">Allow users to sign in with their Google account</p>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="oauth_google_enabled" value="1" {{ $settings['oauth_google_enabled'] ? 'checked' : '' }} class="accent-brand-cyan w-4 h-4">
                            <span class="text-xs font-bold text-brand-white">Enable</span>
                        </label>
                    </div>

                    <div class="p-3 rounded-lg bg-brand-teal/5 border border-brand-teal/10 text-[10px] text-brand-gray leading-relaxed space-y-1">
                        <p class="font-semibold text-brand-cyan text-xs">📋 Setup Steps:</p>
                        <p>1. Go to <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-brand-cyan underline">console.cloud.google.com/apis/credentials</a></p>
                        <p>2. Create a new <strong>OAuth 2.0 Client ID</strong> (Web Application)</p>
                        <p>3. Add your <strong>Redirect URI</strong> (shown below) to the "Authorized redirect URIs" list</p>
                        <p>4. Copy the Client ID and Client Secret here</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Client ID</label>
                            <input type="text" name="oauth_google_client_id" value="{{ $settings['oauth_google_client_id'] }}" placeholder="123456789-xxxx.apps.googleusercontent.com" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Client Secret</label>
                            <input type="password" name="oauth_google_client_secret" placeholder="Leave blank to keep current secret" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Redirect URI <span class="text-brand-gray normal-case font-normal">(copy this into Google Console)</span></label>
                        <div class="flex gap-2">
                            <input type="url" name="oauth_google_redirect_uri" value="{{ $settings['oauth_google_redirect_uri'] }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy', 1500)" class="rounded-lg bg-brand-teal/10 border border-brand-teal/20 text-brand-cyan px-4 py-2 text-xs font-bold whitespace-nowrap hover:bg-brand-teal/20 transition-all cursor-pointer">Copy</button>
                        </div>
                    </div>
                </div>

                {{-- ============ APPLE ============ --}}
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <div class="flex items-center justify-between border-b border-brand-teal/10 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" fill="white" class="w-5 h-5"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-brand-white">Apple Sign In</h4>
                                <p class="text-[10px] text-brand-gray">Allow users to sign in with their Apple ID</p>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="oauth_apple_enabled" value="1" {{ $settings['oauth_apple_enabled'] ? 'checked' : '' }} class="accent-brand-cyan w-4 h-4">
                            <span class="text-xs font-bold text-brand-white">Enable</span>
                        </label>
                    </div>

                    <div class="p-3 rounded-lg bg-brand-teal/5 border border-brand-teal/10 text-[10px] text-brand-gray leading-relaxed space-y-1">
                        <p class="font-semibold text-brand-cyan text-xs">📋 Setup Steps:</p>
                        <p>1. Go to <a href="https://developer.apple.com/account/resources/identifiers/list/serviceId" target="_blank" class="text-brand-cyan underline">developer.apple.com</a> → Identifiers → Services IDs</p>
                        <p>2. Create a Services ID and enable <strong>Sign In with Apple</strong></p>
                        <p>3. Configure the redirect URL and add your domain</p>
                        <p>4. Under <strong>Keys</strong>, create a new key with Sign In enabled and download the .p8 file</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Services ID (Client ID)</label>
                            <input type="text" name="oauth_apple_client_id" value="{{ $settings['oauth_apple_client_id'] }}" placeholder="com.yourapp.web" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Team ID</label>
                            <input type="text" name="oauth_apple_team_id" value="{{ $settings['oauth_apple_team_id'] }}" placeholder="ABCDE12345" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Key ID</label>
                            <input type="text" name="oauth_apple_key_id" value="{{ $settings['oauth_apple_key_id'] }}" placeholder="FGHIJ67890" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Redirect URI <span class="text-brand-gray normal-case font-normal">(copy this into Apple developer console)</span></label>
                        <div class="flex gap-2">
                            <input type="url" name="oauth_apple_redirect_uri" value="{{ $settings['oauth_apple_redirect_uri'] }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy', 1500)" class="rounded-lg bg-brand-teal/10 border border-brand-teal/20 text-brand-cyan px-4 py-2 text-xs font-bold whitespace-nowrap hover:bg-brand-teal/20 transition-all cursor-pointer">Copy</button>
                        </div>
                    </div>
                </div>

                {{-- ============ MICROSOFT ============ --}}
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <div class="flex items-center justify-between border-b border-brand-teal/10 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                                <svg viewBox="0 0 23 23" class="w-5 h-5"><path fill="#f25022" d="M0 0h11v11H0z"/><path fill="#00a4ef" d="M12 0h11v11H12z"/><path fill="#7fba00" d="M0 12h11v11H0z"/><path fill="#ffb900" d="M12 12h11v11H12z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-brand-white">Microsoft Azure AD</h4>
                                <p class="text-[10px] text-brand-gray">Allow users to sign in with their Microsoft / Azure account</p>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="oauth_microsoft_enabled" value="1" {{ $settings['oauth_microsoft_enabled'] ? 'checked' : '' }} class="accent-brand-cyan w-4 h-4">
                            <span class="text-xs font-bold text-brand-white">Enable</span>
                        </label>
                    </div>

                    <div class="p-3 rounded-lg bg-brand-teal/5 border border-brand-teal/10 text-[10px] text-brand-gray leading-relaxed space-y-1">
                        <p class="font-semibold text-brand-cyan text-xs">📋 Setup Steps:</p>
                        <p>1. Go to <a href="https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade" target="_blank" class="text-brand-cyan underline">portal.azure.com</a> → Azure Active Directory → App Registrations</p>
                        <p>2. Click <strong>New Registration</strong>, choose account type (single or multi-tenant)</p>
                        <p>3. Add the <strong>Redirect URI</strong> (shown below) as a Web platform redirect</p>
                        <p>4. Under <strong>Certificates & secrets</strong>, create a Client Secret and copy it here</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Application (Client) ID</label>
                            <input type="text" name="oauth_microsoft_client_id" value="{{ $settings['oauth_microsoft_client_id'] }}" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Client Secret</label>
                            <input type="password" name="oauth_microsoft_client_secret" placeholder="Leave blank to keep current secret" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-white uppercase mb-2">Tenant ID</label>
                            <input type="text" name="oauth_microsoft_tenant_id" value="{{ $settings['oauth_microsoft_tenant_id'] }}" placeholder="common" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <span class="block text-[10px] text-brand-gray mt-1">Use <code class="text-brand-cyan">common</code> for personal &amp; work accounts, or paste your Directory (tenant) ID for single-tenant apps.</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Redirect URI <span class="text-brand-gray normal-case font-normal">(copy this into Azure app registration)</span></label>
                        <div class="flex gap-2">
                            <input type="url" name="oauth_microsoft_redirect_uri" value="{{ $settings['oauth_microsoft_redirect_uri'] }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy', 1500)" class="rounded-lg bg-brand-teal/10 border border-brand-teal/20 text-brand-cyan px-4 py-2 text-xs font-bold whitespace-nowrap hover:bg-brand-teal/20 transition-all cursor-pointer">Copy</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-3 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                        💾 Save Social Login Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 5: Maintenance Center -->
        <div x-show="activeTab === 'maintenance'" x-transition class="space-y-6 max-w-4xl">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-6">
                <div>
                    <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-2">Database &amp; Session Rotations</h3>
                    <p class="text-xs text-brand-gray">Perform system flushing, rebuild routing caches, and purge stale audit logs and expired codes.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Clear Cache -->
                    <div class="border border-brand-teal/10 rounded-xl p-4 bg-brand-teal/5 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-brand-white uppercase">Rebuild System Cache</h4>
                            <p class="text-[10px] text-brand-gray mt-1 mb-4">Purges compiled views, routes, config buffers, and cached system configuration files.</p>
                        </div>
                        <form action="{{ route('admin.settings.clear-cache') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 text-brand-cyan px-4 py-2 text-xs font-bold text-center transition-all cursor-pointer">
                                🧹 Clear Cache
                            </button>
                        </form>
                    </div>

                    <!-- Optimize Database -->
                    <div class="border border-brand-teal/10 rounded-xl p-4 bg-brand-teal/5 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-brand-white uppercase">Optimize Database Tables</h4>
                            <p class="text-[10px] text-brand-gray mt-1 mb-4">Runs SQL table optimization and defragments indices across all engine schemas.</p>
                        </div>
                        <form action="{{ route('admin.settings.optimize-db') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 text-brand-cyan px-4 py-2 text-xs font-bold text-center transition-all cursor-pointer">
                                ⚙️ Optimize Database
                            </button>
                        </form>
                    </div>

                    <!-- Run Migrations -->
                    <div class="border border-brand-teal/10 rounded-xl p-4 bg-brand-teal/5 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-brand-white uppercase text-brand-cyan">Run Schema Migrations</h4>
                            <p class="text-[10px] text-brand-gray mt-1 mb-4">Executes pending database migration scripts to update database structures and tables safely.</p>
                        </div>
                        <form action="{{ route('admin.settings.run-migrations') }}" method="POST" onsubmit="return confirm('🚀 Database Migrations: This will run all pending migrations in production. Do you want to proceed?');">
                            @csrf
                            <button type="submit" class="w-full rounded bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 text-brand-cyan px-4 py-2 text-xs font-bold text-center transition-all cursor-pointer">
                                🚀 Run Migrations
                            </button>
                        </form>
                    </div>

                    <!-- Flush Sessions -->
                    <div class="border border-rose-500/10 rounded-xl p-4 bg-rose-950/5 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-brand-white uppercase text-rose-400">Force Global Session Logouts</h4>
                            <p class="text-[10px] text-brand-gray mt-1 mb-4">Clears active session records. Forces all portal users to immediately authenticate again.</p>
                        </div>
                        <form action="{{ route('admin.settings.flush-sessions') }}" method="POST" onsubmit="return confirm('⚠️ WARNING: Flushing sessions will immediately log out all active users and invalidate all trusted device records. Are you sure you want to proceed?');">
                            @csrf
                            <button type="submit" class="w-full rounded bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/25 px-4 py-2 text-xs font-bold text-center transition-all cursor-pointer">
                                💀 Flush Sessions
                            </button>
                        </form>
                    </div>

                    <!-- Purge Old Data / Site Cleanup -->
                    <div class="border border-amber-500/10 rounded-xl p-4 bg-amber-950/5 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-brand-white uppercase text-amber-400">Site Cleanup &amp; Purge</h4>
                            <p class="text-[10px] text-brand-gray mt-1 mb-4">Prunes historical audit logs (>90 days), old exam session records, and expired security logs.</p>
                        </div>
                        <form action="{{ route('admin.settings.purge-old-data') }}" method="POST" onsubmit="return confirm('🧹 Site Cleanup: This will permanently purge expired OTPs, audit logs older than 90 days, stale trusted device records (60+ days offline), exam sessions older than 90 days, read notifications (30+ days old), and flush all caches. Do you want to run this cleanup?');">
                            @csrf
                            <button type="submit" class="w-full rounded bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500/25 px-4 py-2 text-xs font-bold text-center transition-all cursor-pointer">
                                ✨ Run Site Cleanup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
