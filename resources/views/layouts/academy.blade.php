@extends('layouts.app')

@section('title', 'Academy Dashboard - Diwebs Academy')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Academy Sidebar Navigation -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 sticky top-24 space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-4 px-2 border-b border-brand-teal/10 pb-3">
                        <span class="text-2xl">🎓</span>
                        <div>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Diwebs Academy</h3>
                            <span class="text-[9px] text-brand-gray/80">Next-Gen Hybrid LMS</span>
                        </div>
                    </div>
                    
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
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            @yield('academy_content')
        </main>

    </div>
</div>
@endsection
