@extends('layouts.staff')

@section('title', 'My Payroll & Payslips - Staff Portal')

@section('staff_content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-brand-white">My Payroll &amp; Payslips</h1>
        <p class="text-xs text-brand-gray mt-1">Access monthly generated payslips, inspect bonuses/allowances, tax deductions, and download signed PDFs.</p>
    </div>

    <!-- Slips Grid -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-teal/10 bg-brand-dark-secondary/20">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Payslips History</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Month</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Base Salary</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Bonuses (₦)</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Deductions (₦)</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Estimated Tax</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Net Paid</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Status</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-right">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($payrolls as $pr)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4 font-bold font-mono">
                                {{ date('F Y', strtotime($pr->month . '-01')) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px]">
                                ₦{{ number_format($pr->base_salary, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-emerald-400">
                                +₦{{ number_format($pr->bonuses, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-rose-400">
                                -₦{{ number_format($pr->deductions, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-rose-400/80">
                                -₦{{ number_format($pr->tax, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-extrabold text-brand-cyan text-sm">
                                ₦{{ number_format($pr->net_salary, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $pr->payment_status === 'Paid' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : 'bg-amber-500/10 border-amber-500/25 text-amber-400' }}">{{ $pr->payment_status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($pr->payment_status === 'Paid')
                                    <a href="{{ route('staff.payroll.download', $pr->id) }}" class="inline-flex items-center gap-1 text-[10px] font-bold bg-brand-teal/15 border border-brand-teal/30 hover:border-brand-cyan/60 hover:bg-brand-teal/25 text-brand-cyan px-2.5 py-1 rounded select-none">
                                        📥 Download PDF
                                    </a>
                                @else
                                    <span class="text-[10px] text-brand-gray italic">Awaiting Payment</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-brand-gray">No payroll slip records registered in your account database yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
