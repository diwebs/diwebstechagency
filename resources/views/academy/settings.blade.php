@extends('layouts.academy')

@section('title', 'Academy Settings - Diwebs Academy')

@section('academy_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Academy Settings</h1>
        <p class="text-sm text-brand-gray mt-1">Configure your weekly target goals, coaching reminders, and offline playback limits.</p>
    </div>

    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Student Preferences</h3>
        
        <form @submit.prevent="alert('Preferences saved successfully!')" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Weekly hours target -->
                <div>
                    <label class="block text-xs font-bold text-brand-white uppercase mb-2">Weekly study goal</label>
                    <select class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <option value="5">5 Hours per week (Casual)</option>
                        <option value="10" selected>10 Hours per week (Normal)</option>
                        <option value="20">20 Hours per week (Bootcamp Specialist)</option>
                        <option value="40">40 Hours per week (Full Immersion)</option>
                    </select>
                </div>

                <!-- Provider -->
                <div>
                    <label class="block text-xs font-bold text-brand-white uppercase mb-2">Preferred Meeting Provider</label>
                    <select class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <option value="meet" selected>Google Meet Sandbox</option>
                        <option value="zoom">Zoom Video SDK</option>
                        <option value="jitsi">Jitsi Open-Source Meet</option>
                    </select>
                </div>
            </div>

            <!-- Toggles -->
            <div class="space-y-4 pt-4 border-t border-brand-teal/10">
                <h4 class="text-xs font-bold text-brand-white uppercase">Notification Channels</h4>
                
                <div class="flex items-center justify-between text-xs text-brand-gray">
                    <div>
                        <strong class="text-brand-white block">Email Reminders</strong>
                        <span>Receive scheduled notifications 30 mins before live classes.</span>
                    </div>
                    <input type="checkbox" checked class="h-4 w-4 rounded bg-brand-dark-secondary border border-brand-teal/15 text-brand-cyan focus:ring-0">
                </div>

                <div class="flex items-center justify-between text-xs text-brand-gray">
                    <div>
                        <strong class="text-brand-white block">PWA Push Notifications</strong>
                        <span>Enable instant notifications inside browser/OS channels.</span>
                    </div>
                    <input type="checkbox" checked class="h-4 w-4 rounded bg-brand-dark-secondary border border-brand-teal/15 text-brand-cyan focus:ring-0">
                </div>

                <div class="flex items-center justify-between text-xs text-brand-gray">
                    <div>
                        <strong class="text-brand-white block">Offline Audio Sync limits</strong>
                        <span>Only sync audio tracks when connected to unmetered Wi-Fi.</span>
                    </div>
                    <input type="checkbox" checked class="h-4 w-4 rounded bg-brand-dark-secondary border border-brand-teal/15 text-brand-cyan focus:ring-0">
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-brand-teal/10">
                <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-extrabold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                    ✓ Save Parameters
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
