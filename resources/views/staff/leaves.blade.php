@extends('layouts.staff')

@section('title', 'Leave Management - Staff Portal')

@section('staff_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Leave Allocation &amp; Requests</h1>
            <p class="text-xs text-brand-gray mt-1 font-sans">Submit sick or annual leave applications, check status indicators, and monitor allocated days balances.</p>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Pane: Apply Form & Balance Card -->
        <div class="space-y-6">
            <!-- Balance Card -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-2">
                <span class="text-brand-gray text-[9px] uppercase tracking-wider block">Leave Days Balance</span>
                <h3 class="text-3xl font-extrabold text-brand-white font-mono">{{ $leaveBalance }} Days</h3>
                <p class="text-[10px] text-brand-gray leading-relaxed">Out of 24 standard annual leave allocations. Approved requests reduce this balance.</p>
            </div>

            <!-- Form -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">✉️ Apply for Leave</h3>
                
                @if(session('error'))
                    <div class="p-3 rounded bg-rose-500/10 border border-rose-500/30 text-[11px] text-rose-400">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('staff.leaves.apply') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Leave Type</label>
                        <select name="leave_type" required
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2.5 text-xs text-brand-white focus:outline-none">
                            <option value="Annual Leave">Annual Leave</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Emergency Leave">Emergency Leave</option>
                            <option value="Maternity/Paternity Leave">Maternity/Paternity Leave</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Start Date</label>
                            <input type="date" name="start_date" required min="{{ date('Y-m-d') }}"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">End Date</label>
                            <input type="date" name="end_date" required min="{{ date('Y-m-d') }}"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Application Reason Details</label>
                        <textarea name="reason" required rows="3" placeholder="Provide details explaining the request..."
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-3 text-xs text-brand-white focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer select-none">
                        Submit Application
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Pane: History list -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">My Leave Requests History</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                                <th class="px-4 py-3 font-bold uppercase text-[9px]">Leave Type</th>
                                <th class="px-4 py-3 font-bold uppercase text-[9px]">Requested Duration</th>
                                <th class="px-4 py-3 font-bold uppercase text-[9px]">Reason</th>
                                <th class="px-4 py-3 font-bold uppercase text-[9px]">Status</th>
                                <th class="px-4 py-3 font-bold uppercase text-[9px]">Admin Response</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                            @forelse($leaves as $lv)
                                <tr class="hover:bg-brand-teal/5 transition-all">
                                    <td class="px-4 py-3 font-bold">
                                        {{ $lv->leave_type }}
                                    </td>
                                    <td class="px-4 py-3 font-mono">
                                        <span class="block font-bold">{{ $lv->days }} Days</span>
                                        <span class="text-[9px] text-brand-gray block">{{ $lv->start_date->format('Y-m-d') }} to {{ $lv->end_date->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="px-4 py-3 max-w-[150px] truncate" title="{{ $lv->reason }}">
                                        {{ $lv->reason }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $lv->status === 'Approved' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : ($lv->status === 'Pending' ? 'bg-amber-500/10 border-amber-500/25 text-amber-400' : 'bg-rose-500/10 border-rose-500/25 text-rose-400') }}">{{ $lv->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-[10px] text-brand-gray">
                                        @if($lv->admin_notes)
                                            <span class="block italic">"{{ $lv->admin_notes }}"</span>
                                        @else
                                            <span class="block italic">Awaiting review...</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-brand-gray">No leaves requests registered in your account database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
