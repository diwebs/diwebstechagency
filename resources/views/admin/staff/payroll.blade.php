@extends('layouts.admin')

@section('title', 'Payroll Management - Admin Dashboard')

@section('admin_content')
<div class="space-y-8" x-data="payrollWorkspace()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white font-sans">Payroll &amp; Payslips</h1>
            <p class="text-xs text-brand-gray mt-1">Generate monthly payroll outputs, compute taxes, adjust staff bonuses or deductions, and manage payment states.</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Run Payroll Form -->
            <form action="{{ route('admin.staff.payroll.run') }}" method="POST" class="flex gap-2 bg-[#1A1D21] border border-brand-teal/20 rounded-lg p-1.5 items-center">
                @csrf
                <input type="month" name="month" value="{{ $month }}" required
                       class="rounded bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-xs text-brand-white focus:outline-none">
                <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 select-none cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Run Monthly Payroll
                </button>
            </form>
        </div>
    </div>

    <!-- Active Cycle Overview -->
    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Payroll Cycle</span>
            <span class="text-sm font-extrabold text-brand-white font-mono">{{ date('F Y', strtotime($month . '-01')) }}</span>
        </div>
        <div>
            <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Total Net Salary Outlay</span>
            <span class="text-sm font-extrabold text-brand-cyan font-mono">₦{{ number_format($payrolls->sum('net_salary'), 2) }}</span>
        </div>
        <div>
            <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Registered Records</span>
            <span class="text-sm font-extrabold text-brand-white font-mono">{{ $payrolls->count() }} Employees Invoiced</span>
        </div>
    </div>

    <!-- Slips Directory -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-teal/10 bg-brand-dark-secondary/20 flex justify-between items-center">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Monthly Payslips Directory</h3>
            <span class="text-[9px] text-brand-gray font-mono">Period: {{ $month }}</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Staff Details</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Base Salary</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Bonuses (₦)</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Deductions (₦)</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Estimated Tax</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Net Payout</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Payment Status</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($payrolls as $p)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4">
                                <span class="font-bold block text-sm">{{ $p->staff->name ?? 'Staff Record Deleted' }}</span>
                                <span class="text-[10px] text-brand-cyan font-mono block">{{ $p->staff->staff_id ?? 'N/A' }} | {{ $p->staff->department->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px]">
                                ₦{{ number_format($p->base_salary, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-emerald-400">
                                +₦{{ number_format($p->bonuses, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-rose-400">
                                -₦{{ number_format($p->deductions, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-rose-400/70">
                                -₦{{ number_format($p->tax, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-sm font-extrabold text-brand-cyan">
                                ₦{{ number_format($p->net_salary, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $p->payment_status === 'Paid' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : ($p->payment_status === 'Pending' ? 'bg-amber-500/10 border-amber-500/25 text-amber-400' : 'bg-rose-500/10 border-rose-500/25 text-rose-400') }}">
                                    {{ $p->payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <!-- Edit Action -->
                                    <button type="button" 
                                            @click='editSlip(@json($p))'
                                            class="text-[10px] text-brand-cyan hover:underline select-none cursor-pointer inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Adjust
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-brand-gray">No payroll entries created for this month. Run payroll above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Adjust Slip Overlay Modal -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="glass-card rounded-2xl max-w-md w-full border border-brand-teal/20 p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-brand-teal/10 pb-3">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Adjust Payroll Parameters</h3>
                <button type="button" @click="showModal = false" class="text-brand-gray hover:text-brand-white text-xs">✕</button>
            </div>

            <form :action="'/admin/staff/payroll/update/' + activeSlip.id" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[9px] uppercase tracking-wider font-bold text-brand-gray mb-1">Staff Member</label>
                    <span class="block text-xs font-bold text-brand-white" x-text="activeSlip.staff ? activeSlip.staff.name : ''"></span>
                    <span class="block text-[9px] font-mono text-brand-cyan" x-text="activeSlip.staff ? activeSlip.staff.staff_id : ''"></span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Bonuses (₦)</label>
                        <input type="number" name="bonuses" step="0.01" required x-model="activeSlip.bonuses"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Deductions (₦)</label>
                        <input type="number" name="deductions" step="0.01" required x-model="activeSlip.deductions"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Payment Status</label>
                    <select name="payment_status" required x-model="activeSlip.payment_status"
                            class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Failed">Failed</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" @click="showModal = false" class="rounded border border-white/5 px-4 py-2 text-xs text-brand-gray hover:bg-white/5">
                        Cancel
                    </button>
                    <button type="submit" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-5 py-2 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90">
                        Update Slip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function payrollWorkspace() {
    return {
        showModal: false,
        activeSlip: {},
        editSlip(slip) {
            this.activeSlip = { ...slip };
            this.showModal = true;
        }
    }
}
</script>
@endsection
