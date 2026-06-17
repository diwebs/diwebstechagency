@extends('layouts.app')

@section('title', 'Academy Dashboard - Diwebs Academy')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Academy Sidebar Navigation -->
        <aside class="w-full lg:w-64 flex-shrink-0" x-data="{ open: false }">
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 sticky top-24 space-y-4 lg:space-y-6">
                <!-- Header / Toggle -->
                <div class="flex items-center justify-between px-2 border-b border-brand-teal/10 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🎓</span>
                        <div>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Diwebs Academy</h3>
                            <span class="text-[9px] text-brand-gray/80">Next-Gen Hybrid LMS</span>
                        </div>
                    </div>
                    
                    <!-- Mobile Badges and Toggle -->
                    <div class="flex items-center gap-2 lg:hidden">
                        <span class="inline-flex items-center rounded-full bg-brand-cyan/10 border border-brand-cyan/20 px-2 py-0.5 text-[9px] text-brand-cyan font-bold">
                            {{ request()->routeIs('academy.dashboard') ? 'Overview' : '' }}
                            {{ request()->routeIs('academy.courses') ? 'Courses' : '' }}
                            {{ request()->routeIs('academy.audio-learning') ? 'Audio' : '' }}
                            {{ request()->routeIs('academy.live-classes') ? 'Live' : '' }}
                            {{ request()->routeIs('academy.mentorship') ? 'Mentors' : '' }}
                            {{ request()->routeIs('academy.sessions') ? 'Schedule' : '' }}
                            {{ request()->routeIs('academy.assignments') ? 'Assignments' : '' }}
                            {{ request()->routeIs('academy.certificates') ? 'Certificates' : '' }}
                            {{ request()->routeIs('academy.messages') ? 'Messages' : '' }}
                            {{ request()->routeIs('academy.notifications') ? 'Notifications' : '' }}
                            {{ request()->routeIs('academy.settings') ? 'Settings' : '' }}
                        </span>
                        
                        <button @click="open = !open" type="button" class="flex items-center justify-center p-1.5 rounded-lg border border-brand-teal/20 text-brand-cyan hover:bg-brand-teal/10 transition-all">
                            <svg class="h-4 w-4 transform transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Navigation links & info -->
                <div :class="open ? 'block' : 'hidden'" class="lg:block space-y-4 lg:space-y-6">
                    <div>
                        <div class="space-y-1">
                            <!-- Overview -->
                            <a href="{{ route('academy.dashboard') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>📊</span> Overview
                            </a>

                            <!-- My Courses -->
                            <a href="{{ route('academy.courses') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.courses') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>📚</span> My Courses
                            </a>

                            <!-- Audio Learning -->
                            <a href="{{ route('academy.audio-learning') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.audio-learning') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>🎧</span> Audio Learning
                            </a>

                            <!-- Live Classes -->
                            <a href="{{ route('academy.live-classes') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.live-classes') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>📺</span> Live Classes
                            </a>

                            <!-- My Teachers -->
                            <a href="{{ route('academy.mentorship') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.mentorship') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>👥</span> My Teachers
                            </a>

                            <!-- Session Schedule -->
                            <a href="{{ route('academy.sessions') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.sessions') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>📅</span> Session Schedule
                            </a>

                            <!-- Assignments -->
                            <a href="{{ route('academy.assignments') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.assignments') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>📝</span> Assignments
                            </a>

                            <!-- Certificates -->
                            <a href="{{ route('academy.certificates') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.certificates') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>🏆</span> Certificates
                            </a>

                            <!-- Messages -->
                            <a href="{{ route('academy.messages') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.messages') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>💬</span> Messages
                            </a>

                            <!-- Notifications -->
                            <a href="{{ route('academy.notifications') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.notifications') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>🔔</span> Notifications
                            </a>

                            <!-- Settings -->
                            <a href="{{ route('academy.settings') }}" 
                               class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('academy.settings') ? 'bg-brand-cyan text-brand-dark-secondary shadow' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                                <span>⚙️</span> Settings
                            </a>
                        </div>
                    </div>

                    <!-- Academy Progress Info -->
                    <div class="border-t border-brand-teal/10 pt-4 text-[11px] text-brand-gray/80 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span>Learning Status</span>
                            <span class="text-emerald-400 font-bold">Active</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Network Mode</span>
                            <span class="text-brand-cyan font-semibold flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand-cyan animate-pulse"></span> Online
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            @yield('academy_content')
        </main>

    </div>
</div>
@endsection
