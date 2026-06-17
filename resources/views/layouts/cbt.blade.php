@extends('layouts.app')

@section('title', 'CBT Ecosystem Panel - Diwebs CBT')

@section('content')
@php
    $route = request()->route()->getName();
    $isPartnerSpace = str_starts_with($route, 'cbt.partner') || in_array($route, ['cbt.institution-management']);
    
    // Check if the user is a center owner
    $user = auth()->user();
    $hasCenter = \App\Models\CbtCenter::where('owner_id', $user->id)->exists();
@endphp

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Switchable CBT Sidebar Navigation -->
        <aside class="w-full lg:w-64 flex-shrink-0" x-data="{ open: false }">
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 sticky top-24 space-y-4 lg:space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between px-2 border-b border-brand-teal/10 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🖥️</span>
                        <div>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Diwebs CBT</h3>
                            <span class="text-[9px] text-brand-gray/80">
                                {{ $isPartnerSpace ? 'Partner Console' : 'Assessment Engine' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Mobile Badges and Toggle -->
                    <div class="flex items-center gap-2 lg:hidden">
                        <span class="inline-flex items-center rounded-full bg-brand-cyan/10 border border-brand-cyan/20 px-2 py-0.5 text-[9px] text-brand-cyan font-bold">
                            @if($isPartnerSpace)
                                {{ request()->routeIs('cbt.partner.dashboard') ? 'Dashboard' : '' }}
                                {{ request()->routeIs('cbt.partner.centers') || request()->routeIs('cbt.partner.centers.seats') ? 'Centers' : '' }}
                                {{ request()->routeIs('cbt.institution-management') ? 'Exams Mgmt' : '' }}
                                {{ request()->routeIs('cbt.partner.candidates') ? 'Candidates' : '' }}
                                {{ request()->routeIs('cbt.partner.revenue') ? 'Revenue' : '' }}
                                {{ request()->routeIs('cbt.partner.reports') ? 'Reports' : '' }}
                                {{ request()->routeIs('cbt.partner.settings') ? 'Settings' : '' }}
                            @else
                                {{ request()->routeIs('cbt.dashboard') ? 'Dashboard' : '' }}
                                {{ request()->routeIs('cbt.practice-tests') ? 'Practice' : '' }}
                                {{ request()->routeIs('cbt.live-exams') || request()->routeIs('cbt.live-exams.lobby') ? 'Live' : '' }}
                                {{ request()->routeIs('cbt.exams') ? 'Scheduled' : '' }}
                                {{ request()->routeIs('cbt.results.history') ? 'Results' : '' }}
                                {{ request()->routeIs('cbt.certificates') || request()->routeIs('cbt.certificate.download') ? 'Certificates' : '' }}
                                {{ request()->routeIs('cbt.sessions') ? 'Sessions' : '' }}
                                {{ request()->routeIs('cbt.notifications') ? 'Notifications' : '' }}
                                {{ request()->routeIs('cbt.profile') ? 'Profile' : '' }}
                                {{ request()->routeIs('cbt.center-enrollment') ? 'Enrollment' : '' }}
                            @endif
                        </span>
                        
                        <button @click="open = !open" type="button" class="flex items-center justify-center p-1.5 rounded-lg border border-brand-teal/20 text-brand-cyan hover:bg-brand-teal/10 transition-all">
                            <svg class="h-4 w-4 transform transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Navigation links -->
                <div :class="open ? 'block' : 'hidden'" class="lg:block space-y-4 lg:space-y-6">
                    <div>
                        <div class="space-y-1">
                            @if($isPartnerSpace)
                                <!-- Partner Dashboard -->
                                <a href="{{ route('cbt.partner.dashboard') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.partner.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📊</span> Dashboard
                                </a>

                                <!-- My Centers -->
                                <a href="{{ route('cbt.partner.centers') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.partner.centers') || request()->routeIs('cbt.partner.centers.seats') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>🏫</span> My Centers
                                </a>

                                <!-- Center Enrollment -->
                                <a href="{{ route('cbt.center-enrollment') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.center-enrollment') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📝</span> Center Enrollment
                                </a>

                                <!-- Exam Management -->
                                <a href="{{ route('cbt.institution-management') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.institution-management') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📚</span> Exam Management
                                </a>

                                <!-- Candidates Monitoring -->
                                <a href="{{ route('cbt.partner.candidates') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.partner.candidates') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>👥</span> Candidates
                                </a>

                                <!-- Revenue tracking -->
                                <a href="{{ route('cbt.partner.revenue') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.partner.revenue') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>💳</span> Revenue
                                </a>

                                <!-- Reports -->
                                <a href="{{ route('cbt.partner.reports') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.partner.reports') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📈</span> Reports
                                </a>

                                <!-- Settings -->
                                <a href="{{ route('cbt.partner.settings') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.partner.settings') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>⚙️</span> Settings
                                </a>
                            @else
                                <!-- Candidate Dashboard -->
                                <a href="{{ route('cbt.dashboard') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📊</span> Dashboard
                                </a>

                                <!-- Practice Exams -->
                                <a href="{{ route('cbt.practice-tests') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.practice-tests') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>🛡️</span> Practice Exams
                                </a>

                                <!-- Live Exams -->
                                <a href="{{ route('cbt.live-exams') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.live-exams') || request()->routeIs('cbt.live-exams.lobby') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📺</span> Live Exams
                                </a>

                                <!-- Scheduled Exams -->
                                <a href="{{ route('cbt.exams') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.exams') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📅</span> Scheduled Exams
                                </a>

                                <!-- Results -->
                                <a href="{{ route('cbt.results.history') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.results.history') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📈</span> Results
                                </a>

                                <!-- Certificates -->
                                <a href="{{ route('cbt.certificates') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.certificates') || request()->routeIs('cbt.certificate.download') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>🏆</span> Certificates
                                </a>

                                <!-- My Sessions -->
                                <a href="{{ route('cbt.sessions') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.sessions') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>📅</span> My Sessions
                                </a>

                                <!-- Notifications -->
                                <a href="{{ route('cbt.notifications') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.notifications') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>🔔</span> Notifications
                                </a>

                                <!-- Profile -->
                                <a href="{{ route('cbt.profile') }}" 
                                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('cbt.profile') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                    <span>👤</span> Profile
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Spacer Context Toggle Actions -->
                    <div class="border-t border-brand-teal/10 pt-4 space-y-3">
                        @if($hasCenter)
                            @if($isPartnerSpace)
                                <a href="{{ route('cbt.dashboard') }}" 
                                   class="w-full flex items-center justify-center gap-2 rounded-xl bg-brand-teal/10 border border-brand-teal/20 py-2.5 text-xs font-bold text-brand-cyan hover:bg-brand-teal/20 transition-all select-none">
                                    🔄 Candidate Space
                                </a>
                            @else
                                <a href="{{ route('cbt.partner.dashboard') }}" 
                                   class="w-full flex items-center justify-center gap-2 rounded-xl bg-brand-cyan/20 border border-brand-cyan/30 py-2.5 text-xs font-bold text-brand-white hover:bg-brand-cyan/30 transition-all select-none">
                                    🔄 Partner View
                                </a>
                            @endif
                        @else
                            @if(!request()->routeIs('cbt.center-enrollment'))
                                <a href="{{ route('cbt.center-enrollment') }}" 
                                   class="w-full flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-2.5 text-xs font-extrabold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all select-none">
                                    🏢 Become Partner
                                </a>
                            @endif
                        @endif
                        
                        <div class="text-[10px] text-brand-gray/60 flex items-center justify-between border-t border-white/5 pt-3">
                            <span>Engine Sandbox</span>
                            <span class="text-emerald-400 font-mono">TLS-1.3</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Dynamic Main Content Slot -->
        <main class="flex-1 min-w-0">
            @yield('cbt_content')
        </main>

    </div>
</div>
@endsection
