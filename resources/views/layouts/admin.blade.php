@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Navigation Drawer (Left Sidebar) -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 sticky top-24 space-y-6">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-3">System Control</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6a2 2 0 002 2h2a2 2 0 002-2V9a2 2 0 00-2-2h-3l-1-2H9L8 7H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                            Overview Dashboard
                        </a>
                        <a href="{{ route('admin.settings') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.settings') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            System Settings
                        </a>
                        <a href="{{ route('admin.payment-settings') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.payment-settings') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Payment Gateway
                        </a>
                        <a href="{{ route('admin.notifications') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.notifications') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Dispatch Alerts
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Core Modules</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.users') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.users') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            User Accounts
                        </a>
                        <a href="{{ route('admin.portfolios') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.portfolios*') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Portfolio Showcase
                        </a>
                        <a href="{{ route('admin.leads') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.leads') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Clients &amp; Leads
                        </a>
                        <a href="{{ route('admin.referrals') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.referrals') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Referrals Tracker
                        </a>
                        <a href="{{ route('admin.finance') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.finance') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Financial Billing
                        </a>
                    </div>
                </div>

                <div x-data="{ openCrm: {{ (request()->routeIs('admin.crm.*') || request()->routeIs('admin.projects') || request()->routeIs('admin.portal-control')) ? 'true' : 'false' }} }">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-3 flex items-center justify-between cursor-pointer select-none border-b border-brand-teal/10 pb-1.5" @click="openCrm = !openCrm">
                        <span class="flex items-center gap-2"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> CRM System</span>
                        <span class="text-[8px] transition-transform duration-200" :class="openCrm ? 'rotate-180' : ''">▼</span>
                    </h3>
                    <div x-show="openCrm" x-transition class="space-y-1 pl-1">
                        <a href="{{ route('admin.crm.dashboard') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            CRM Dashboard
                        </a>
                        <a href="{{ route('admin.projects') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.projects') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            Projects Pipeline
                        </a>
                        <a href="{{ route('admin.portal-control') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.portal-control') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Client Portals
                        </a>
                        <a href="{{ route('admin.crm.leads') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.leads') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Leads
                        </a>
                        <a href="{{ route('admin.crm.prospects') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.prospects') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            Prospects
                        </a>
                        <a href="{{ route('admin.crm.clients') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.clients') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Clients
                        </a>
                        <a href="{{ route('admin.crm.companies') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.companies') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Companies
                        </a>
                        <a href="{{ route('admin.crm.deals') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.deals') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Deals Pipeline
                        </a>
                        <a href="{{ route('admin.crm.tasks') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.tasks') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tasks &amp; Follows
                        </a>
                        <a href="{{ route('admin.crm.meetings') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.meetings') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Meetings
                        </a>
                        <a href="{{ route('admin.crm.communications') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.communications') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Comms Center
                        </a>
                        <a href="{{ route('admin.crm.contracts') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.contracts') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Contracts
                        </a>
                        <a href="{{ route('admin.crm.invoices') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.invoices') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Invoices
                        </a>
                        <a href="{{ route('admin.crm.tickets') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.tickets') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            Support Tickets
                        </a>
                        <a href="{{ route('admin.crm.campaigns') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.campaigns') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            Campaigns
                        </a>
                        <a href="{{ route('admin.crm.reports') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.reports') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                            Reports &amp; Analytics
                        </a>
                        <a href="{{ route('admin.crm.settings') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.crm.settings') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            CRM Settings
                        </a>
                    </div>
                </div>

                <div x-data="{ openStaff: {{ request()->routeIs('admin.staff.*') ? 'true' : 'false' }} }">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-3 flex items-center justify-between cursor-pointer select-none border-b border-brand-teal/10 pb-1.5" @click="openStaff = !openStaff">
                        <span class="flex items-center gap-2"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg> Staff &amp; HRMS</span>
                        <span class="text-[8px] transition-transform duration-200" :class="openStaff ? 'rotate-180' : ''">▼</span>
                    </h3>
                    <div x-show="openStaff" x-transition class="space-y-1 pl-1">
                        <a href="{{ route('admin.staff.dashboard') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6a2 2 0 002 2h2a2 2 0 002-2V9a2 2 0 00-2-2h-3l-1-2H9L8 7H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                            HR Dashboard
                        </a>
                        <a href="{{ route('admin.staff.index') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.index') && !request()->routeIs('admin.staff.create') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            All Staff
                        </a>
                        <a href="{{ route('admin.staff.create') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.create') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Staff
                        </a>
                        <a href="{{ route('admin.staff.departments') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.departments') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Departments &amp; Roles
                        </a>
                        <a href="{{ route('admin.staff.rbac') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.rbac') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Roles &amp; Permissions
                        </a>
                        <a href="{{ route('admin.staff.attendance') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.attendance') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Attendance Logs
                        </a>
                        <a href="{{ route('admin.staff.payroll') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.payroll') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Payroll &amp; Payslips
                        </a>
                        <a href="{{ route('admin.staff.performance') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.performance') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            Performance Reviews
                        </a>
                        <a href="{{ route('admin.staff.leaves') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.leaves') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Leave Management
                        </a>
                        <a href="{{ route('admin.staff.activity-logs') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.staff.activity-logs') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Staff Activity Logs
                        </a>
                    </div>
                </div>

                <div x-data="{ openAcademic: {{ (request()->routeIs('admin.courses') || request()->routeIs('admin.academy.*') || request()->routeIs('admin.academy-plans*') || request()->routeIs('admin.exams') || request()->routeIs('admin.centers') || request()->routeIs('admin.cbt.*')) ? 'true' : 'false' }} }">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-3 flex items-center justify-between cursor-pointer select-none border-b border-brand-teal/10 pb-1.5" @click="openAcademic = !openAcademic">
                        <span class="flex items-center gap-2"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg> Academic &amp; CBT</span>
                        <span class="text-[8px] transition-transform duration-200" :class="openAcademic ? 'rotate-180' : ''">▼</span>
                    </h3>
                    <div x-show="openAcademic" x-transition class="space-y-1 pl-1">
                        <a href="{{ route('admin.courses') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.courses') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            LMS Courses
                        </a>
                        <a href="{{ route('admin.academy.teachers') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.academy.teachers') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Academy Teachers
                        </a>
                        <a href="{{ route('admin.academy.live-sessions') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.academy.live-sessions') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Live Sessions
                        </a>
                        <a href="{{ route('admin.academy-plans') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.academy-plans*') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Academy Plans
                        </a>
                        <a href="{{ route('admin.exams') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.exams') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Exam Sessions
                        </a>
                        <a href="{{ route('admin.centers') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.centers') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            CBT Centers
                        </a>
                        <a href="{{ route('admin.cbt.command') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.cbt.command') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            CBT Command Center
                        </a>
                    </div>
                </div>

                <div x-data="{ openResources: {{ (request()->routeIs('admin.reviews*') || request()->routeIs('admin.news*') || request()->routeIs('admin.support') || request()->routeIs('admin.security-logs') || request()->routeIs('admin.ai')) ? 'true' : 'false' }} }">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-3 flex items-center justify-between cursor-pointer select-none border-b border-brand-teal/10 pb-1.5" @click="openResources = !openResources">
                        <span class="flex items-center gap-2"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> Resources &amp; Logs</span>
                        <span class="text-[8px] transition-transform duration-200" :class="openResources ? 'rotate-180' : ''">▼</span>
                    </h3>
                    <div x-show="openResources" x-transition class="space-y-1 pl-1">
                        <a href="{{ route('admin.reviews') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.reviews*') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            Customer Reviews
                        </a>
                        <a href="{{ route('admin.news') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.news*') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            Newsroom Blog
                        </a>
                        <a href="{{ route('admin.support') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.support') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            Support Tickets
                        </a>
                        <a href="{{ route('admin.security-logs') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.security-logs') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Security Audits
                        </a>
                        <a href="{{ route('admin.ai') }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('admin.ai') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Intelligent AI
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            @yield('admin_content')
        </main>

    </div>
</div>
@endsection
