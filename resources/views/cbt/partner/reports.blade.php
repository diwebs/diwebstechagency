@extends('layouts.cbt')

@section('title', 'Analytical Center Reports - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Operational Reports & Analytics</h1>
            <p class="text-sm text-brand-gray mt-1">Review statistical charts on pass rates, daily attendance logs, and candidate violations.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-brand-teal/10 border border-brand-teal/20 px-4 py-2 rounded-xl">
            <span class="text-xs text-brand-cyan font-bold">📅 Report Period: Last 30 Days</span>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Attendance Chart Mockup -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Candidate Volume Dynamics</h3>
            
            <div class="h-48 flex items-end justify-between gap-2 pt-6">
                <!-- Bar chart representing candidates daily -->
                <div class="w-full flex flex-col items-center gap-2">
                    <div class="w-full bg-brand-cyan/20 border border-brand-cyan/40 rounded-t-lg transition-all hover:bg-brand-cyan/40" style="height: 60px;"></div>
                    <span class="text-[9px] text-brand-gray font-mono">Mon</span>
                </div>
                <div class="w-full flex flex-col items-center gap-2">
                    <div class="w-full bg-brand-cyan/20 border border-brand-cyan/40 rounded-t-lg transition-all hover:bg-brand-cyan/40" style="height: 110px;"></div>
                    <span class="text-[9px] text-brand-gray font-mono">Tue</span>
                </div>
                <div class="w-full flex flex-col items-center gap-2">
                    <div class="w-full bg-brand-cyan/25 border border-brand-cyan/45 rounded-t-lg transition-all hover:bg-brand-cyan/40" style="height: 90px;"></div>
                    <span class="text-[9px] text-brand-gray font-mono">Wed</span>
                </div>
                <div class="w-full flex flex-col items-center gap-2">
                    <div class="w-full bg-brand-cyan/30 border border-brand-cyan/50 rounded-t-lg transition-all hover:bg-brand-cyan/40" style="height: 140px;"></div>
                    <span class="text-[9px] text-brand-gray font-mono">Thu</span>
                </div>
                <div class="w-full flex flex-col items-center gap-2">
                    <div class="w-full bg-brand-cyan/40 border border-brand-cyan/60 rounded-t-lg transition-all hover:bg-brand-cyan/50" style="height: 170px;"></div>
                    <span class="text-[9px] text-brand-gray font-mono">Fri</span>
                </div>
                <div class="w-full flex flex-col items-center gap-2">
                    <div class="w-full bg-brand-cyan/15 border border-brand-cyan/25 rounded-t-lg transition-all hover:bg-brand-cyan/30" style="height: 40px;"></div>
                    <span class="text-[9px] text-brand-gray font-mono">Sat</span>
                </div>
            </div>
            
            <p class="text-[11px] text-brand-gray text-center pt-2">Peak candidate load registered on <strong class="text-brand-white">Friday (124 sessions timed)</strong>.</p>
        </div>

        <!-- Pass / Fail analytical ratios -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Academic Passing Ratio</h3>
            
            <div class="flex items-center justify-center py-4">
                <!-- SVG Ring Chart -->
                <svg class="w-32 h-32 transform -rotate-90">
                    <circle cx="64" cy="64" r="50" class="stroke-brand-dark-secondary" stroke-width="12" fill="transparent" />
                    <!-- 78% pass rate circle -->
                    <circle cx="64" cy="64" r="50" class="stroke-emerald-400" stroke-width="12" fill="transparent" 
                            stroke-dasharray="314" stroke-dashoffset="69" />
                </svg>
                
                <div class="ml-6 space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                        <span class="text-brand-white font-bold">78% Pass Rate</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                        <span class="text-brand-white font-bold">22% Fail/Retake</span>
                    </div>
                </div>
            </div>
            
            <p class="text-[11px] text-brand-gray text-center pt-2">Standard passing threshold configured at <strong class="text-brand-white">65.00% accuracy minimum</strong>.</p>
        </div>
    </div>

</div>
@endsection
