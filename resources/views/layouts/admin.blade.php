@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Navigation Drawer (Left Sidebar) -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 sticky top-24 space-y-6">
                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">System Control</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📊</span> Overview Dashboard
                        </a>
                        <a href="{{ route('admin.settings') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.settings') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>⚙️</span> System Settings
                        </a>
                        <a href="{{ route('admin.payment-settings') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.payment-settings') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>💳</span> Payment Gateway
                        </a>
                        <a href="{{ route('admin.notifications') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.notifications') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>🔔</span> Dispatch Alerts
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Core Modules</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.users') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.users') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>👥</span> User Accounts
                        </a>
                        <a href="{{ route('admin.projects') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.projects') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📂</span> Projects Pipeline
                        </a>
                        <a href="{{ route('admin.leads') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.leads') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>💼</span> Clients &amp; Leads
                        </a>
                        <a href="{{ route('admin.portal-control') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.portal-control') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>💼</span> Client Portals
                        </a>
                        <a href="{{ route('admin.finance') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.finance') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>💳</span> Financial Billing
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Academic &amp; CBT</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.courses') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.courses') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>🎓</span> LMS Courses
                        </a>
                        <a href="{{ route('admin.academy.teachers') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.academy.teachers') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>👥</span> Academy Teachers
                        </a>
                        <a href="{{ route('admin.academy.live-sessions') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.academy.live-sessions') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📺</span> Live Sessions
                        </a>
                        <a href="{{ route('admin.exams') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.exams') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📋</span> Exam Sessions
                        </a>
                        <a href="{{ route('admin.centers') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.centers') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>🏢</span> CBT Centers
                        </a>
                        <a href="{{ route('admin.cbt.command') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.cbt.command') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>⚡</span> CBT Command Center
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Resources &amp; Logs</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.news') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.news*') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>✍️</span> Newsroom Blog
                        </a>
                        <a href="{{ route('admin.support') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.support') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>🎫</span> Support Tickets
                        </a>
                        <a href="{{ route('admin.security-logs') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.security-logs') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>🛡️</span> Security Audits
                        </a>
                        <a href="{{ route('admin.ai') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('admin.ai') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>🤖</span> Intelligent AI
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
