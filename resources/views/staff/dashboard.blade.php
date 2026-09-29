@extends('layouts.staff')

@section('title', 'Staff Dashboard - Diwebs Workspace')

@section('staff_content')
<div class="space-y-8">
    
    <!-- Welcome card -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h1 class="text-xl font-bold text-brand-white">Welcome back, {{ $staff->name }}!</h1>
            <p class="text-xs text-brand-gray mt-1">Operational role: <strong class="text-brand-cyan">{{ $staff->role->title ?? 'Unassigned' }}</strong> under the <strong class="text-brand-cyan">{{ $staff->department->name ?? 'Unassigned' }}</strong>.</p>
        </div>
        
        <!-- Attendance Clock In/Out -->
        <div class="shrink-0">
            <form action="{{ route('staff.attendance.clock') }}" method="POST">
                @csrf
                @if(!$attendance)
                    <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-3 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all select-none cursor-pointer flex items-center gap-2">
                        ⏱️ Clock In Today Shift
                    </button>
                @elseif(!$attendance->clock_out)
                    <button type="submit" class="rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 hover:bg-rose-500/20 px-6 py-3 text-xs font-bold transition-all select-none cursor-pointer flex items-center gap-2">
                        ⏱️ Clock Out Shift
                    </button>
                @else
                    <button type="button" disabled class="rounded-lg bg-brand-dark-secondary/60 border border-brand-teal/10 text-brand-gray px-6 py-3 text-xs font-bold transition-all select-none opacity-50 cursor-not-allowed">
                        ✔ Shift Completed
                    </button>
                @endif
            </form>
        </div>
    </div>

    <!-- Quick stats grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI Index -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Productivity score</span>
            <h3 class="text-2xl font-extrabold text-brand-white mt-1 font-mono">{{ round($avgScore) }}%</h3>
            <span class="text-[9px] text-brand-cyan font-mono block mt-1">Based on KPI reviews</span>
        </div>

        <!-- Leave Balance -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Leave Days Balance</span>
            <h3 class="text-2xl font-extrabold text-brand-white mt-1 font-mono">{{ $leaveBalance }} Days</h3>
            <span class="text-[9px] text-brand-gray block mt-1">Out of 24 annual days</span>
        </div>

        <!-- Last Payout -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Last Paid Net Salary</span>
            <h3 class="text-2xl font-extrabold text-brand-cyan mt-1 font-mono">
                ₦{{ $lastPayroll ? number_format($lastPayroll->net_salary, 2) : '0.00' }}
            </h3>
            <span class="text-[9px] text-brand-gray block mt-1">Period: {{ $lastPayroll ? $lastPayroll->month : 'N/A' }}</span>
        </div>

        <!-- Academy Training progress -->
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
            <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Training Progress</span>
            <h3 class="text-2xl font-extrabold text-brand-white mt-1 font-mono">{{ $academyProgress }}%</h3>
            <span class="text-[9px] text-brand-cyan font-mono block mt-1">Academy courses catalog</span>
        </div>
    </div>

    <!-- Main Workspace Split row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Active Tasks / Projects -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <div class="flex justify-between items-center border-b border-brand-teal/10 pb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Active Assigned Projects</h3>
                    <a href="{{ route('staff.projects') }}" class="text-[10px] text-brand-cyan hover:underline">View All Projects</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-brand-gray border-b border-brand-teal/10">
                                <th class="py-2.5 font-bold uppercase text-[9px]">Project Name</th>
                                <th class="py-2.5 font-bold uppercase text-[9px]">Status</th>
                                <th class="py-2.5 font-bold uppercase text-[9px] text-right">Action Stamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                            @forelse($projects as $p)
                                <tr>
                                    <td class="py-3.5">
                                        <span class="font-bold block text-sm">{{ $p->title }}</span>
                                        <span class="text-[9px] text-brand-gray block max-w-sm truncate">{{ $p->description }}</span>
                                    </td>
                                    <td class="py-3.5">
                                        <span class="inline-flex rounded-full bg-brand-teal/10 px-2 py-0.5 text-[9px] font-bold text-brand-cyan border border-brand-teal/20">{{ $p->status }}</span>
                                    </td>
                                    <td class="py-3.5 text-right font-mono text-brand-gray">
                                        {{ $p->agreement_signed_at ? $p->agreement_signed_at->format('Y-m-d') : 'No Stamp' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-brand-gray">No projects currently registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Multi-Factor Authentication setups -->
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">🛡️ Two-Factor Security</h3>
                
                @if($staff->two_factor_secret && $staff->two_factor_confirmed_at)
                    <div class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 p-4 text-xs text-emerald-400">
                        <span class="font-bold block">✔ 2FA Active</span>
                        <p class="text-[10px] text-emerald-300/80 mt-1 leading-relaxed">Google Authenticator checks are active on your login gateway.</p>
                    </div>

                    <form action="{{ route('staff.2fa.disable') }}" method="POST" class="space-y-3 pt-2">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Authenticator Code</label>
                            <input type="text" name="code" maxlength="6" required placeholder="000000"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                        </div>
                        <button type="submit" class="w-full rounded-lg border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 py-2.5 text-xs font-bold transition-all cursor-pointer select-none">
                            Disable 2FA Security
                        </button>
                    </form>
                @else
                    <div class="text-[11px] text-brand-gray leading-relaxed space-y-3">
                        <p>Protect your staff account using Google Authenticator code checks. Scan the QR code below using your mobile authenticator app, then submit the 6-digit confirmation token.</p>
                        
                        <!-- QR Code Frame -->
                        <div class="flex justify-center bg-white p-2.5 rounded-xl w-36 h-36 mx-auto border border-brand-teal/20">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrCodeUrl) }}" alt="Scan QR Code" class="w-full h-full">
                        </div>

                        <form action="{{ route('staff.2fa.enable') }}" method="POST" class="space-y-3 pt-2">
                            @csrf
                            <input type="hidden" name="secret" value="{{ $totpSecret }}">
                            
                            <div>
                                <label class="block text-[9px] font-mono text-brand-cyan tracking-wider font-bold">Secret Key: {{ $totpSecret }}</label>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Confirmation Token</label>
                                <input type="text" name="code" maxlength="6" required placeholder="000000"
                                       class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-brand-teal/20 border border-brand-teal/30 hover:bg-brand-teal/30 text-brand-cyan py-2.5 text-xs font-bold transition-all cursor-pointer select-none">
                                Verify &amp; Enable 2FA
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
