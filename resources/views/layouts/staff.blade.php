@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Navigation Drawer (Left Sidebar) -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="glass-card rounded-2xl p-5 border border-brand-teal/20 sticky top-24 space-y-6">
                <!-- User details -->
                <div class="flex items-center gap-3 border-b border-brand-teal/10 pb-4">
                    <div class="h-10 w-10 rounded-full border border-brand-cyan bg-brand-teal/10 flex items-center justify-center font-extrabold text-brand-cyan overflow-hidden">
                        @if($staff->profile_picture)
                            <img src="/storage/{{ $staff->profile_picture }}" alt="Profile" class="h-full w-full object-cover">
                        @else
                            <span>{{ strtoupper(substr($staff->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-brand-white truncate max-w-[120px]">{{ $staff->name }}</h4>
                        <span class="text-[9px] text-brand-cyan font-mono block">{{ $staff->staff_id }}</span>
                        <span class="text-[9px] text-brand-gray block truncate max-w-[120px]">{{ $staff->role->title ?? '' }}</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Workspace</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.dashboard') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.dashboard') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📊</span> Overview Dashboard
                        </a>
                        <a href="{{ route('staff.projects') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.projects') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📂</span> Assigned Projects
                        </a>
                        <a href="{{ route('staff.messages') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.messages') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>✉️</span> Internal Messaging
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Personal Records</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.attendance') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.attendance') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📅</span> Attendance logs
                        </a>
                        <a href="{{ route('staff.payroll') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.payroll') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>💵</span> Payroll &amp; Payslips
                        </a>
                        <a href="{{ route('staff.leaves') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.leaves') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>✉️</span> Leave Management
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-brand-cyan mb-3">Growth &amp; Training</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.performance') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.performance') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>📈</span> KPI scorecards
                        </a>
                        <a href="{{ route('staff.academy') }}" 
                           class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-bold transition-all {{ request()->routeIs('staff.academy') ? 'bg-brand-cyan text-brand-dark-secondary' : 'text-brand-gray hover:text-brand-cyan hover:bg-brand-teal/10' }}">
                            <span>🎓</span> Academy Training
                        </a>
                    </div>
                </div>

                <div class="pt-4 border-t border-brand-teal/10">
                    <form action="{{ route('staff.logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-rose-500/10 border border-rose-500/20 py-2.5 text-xs font-bold text-rose-400 hover:bg-rose-500/20 transition-all select-none cursor-pointer">
                            🛑 Terminate Session
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            @yield('staff_content')
        </main>

    </div>
</div>
@endsection
