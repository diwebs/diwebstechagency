@extends('layouts.admin')

@section('title', 'Leave Management - Admin Dashboard')

@section('admin_content')
<div class="space-y-8" x-data="leaveWorkspace()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white font-sans">Leave Management Inbox</h1>
            <p class="text-xs text-brand-gray mt-1 font-sans font-medium">Review employee leave applications, check start/end dates, approve allocations, or record rejection details.</p>
        </div>
    </div>

    <!-- Active Applications List -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="px-6 py-4 border-b border-brand-teal/10 bg-brand-dark-secondary/20 flex justify-between items-center">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Incoming Applications Inbox</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Staff Details</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Leave Type</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Requested Duration</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Reason Details</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Status</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4">
                                <span class="font-bold block text-sm">{{ $leave->staff->name }}</span>
                                <span class="text-[10px] text-brand-cyan font-mono block">{{ $leave->staff->staff_id }} | {{ $leave->staff->department->name ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-brand-white">
                                {{ $leave->leave_type }}
                            </td>
                            <td class="px-6 py-4 text-brand-white font-mono">
                                <span class="block font-bold">{{ $leave->days }} Days Requested</span>
                                <span class="text-[10px] text-brand-gray block">{{ $leave->start_date->format('Y-m-d') }} to {{ $leave->end_date->format('Y-m-d') }}</span>
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <p class="text-[11px] text-brand-gray leading-relaxed truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $leave->status === 'Approved' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : ($leave->status === 'Pending' ? 'bg-amber-500/10 border-amber-500/25 text-amber-400' : 'bg-rose-500/10 border-rose-500/25 text-rose-400') }}">
                                    {{ $leave->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($leave->status === 'Pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Approve form -->
                                        <form action="{{ route('admin.staff.leaves.approve', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 px-2.5 py-1 rounded select-none cursor-pointer">
                                                Approve
                                            </button>
                                        </form>

                                        <!-- Reject Trigger -->
                                        <button type="button" 
                                                @click='rejectRequest(@json($leave))'
                                                class="text-[10px] font-bold bg-rose-500/10 border border-rose-500/30 text-rose-400 hover:bg-rose-500/20 px-2.5 py-1 rounded select-none cursor-pointer">
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[10px] text-brand-gray font-mono block">Reviewed by: {{ $leave->reviewer->name ?? 'System' }}</span>
                                    @if($leave->admin_notes)
                                        <span class="text-[9px] text-brand-gray italic block">"{{ $leave->admin_notes }}"</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-brand-gray">No leave applications currently queued in the inbox.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaves->hasPages())
            <div class="px-6 py-4 bg-brand-dark-secondary/20 border-t border-brand-teal/10">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>

    <!-- Reject Reason Modal Overlay -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="glass-card rounded-2xl max-w-md w-full border border-brand-teal/20 p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-brand-teal/10 pb-3">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan">Reject Leave Application</h3>
                <button type="button" @click="showModal = false" class="text-brand-gray hover:text-brand-white text-xs">✕</button>
            </div>

            <form :action="'/admin/staff/leaves/reject/' + activeLeave.id" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[9px] uppercase tracking-wider font-bold text-brand-gray mb-1">Applicant details</label>
                    <span class="block text-xs font-bold text-brand-white" x-text="activeLeave.staff ? activeLeave.staff.name : ''"></span>
                    <span class="block text-[9px] font-mono text-brand-cyan" x-text="activeLeave.staff ? activeLeave.staff.staff_id : ''"></span>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Rejection Reason / Notes</label>
                    <textarea name="admin_notes" rows="3" required placeholder="Describe the operational reason for rejecting this leave application..."
                              class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-3 text-xs text-brand-white focus:outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" @click="showModal = false" class="rounded border border-white/5 px-4 py-2 text-xs text-brand-gray hover:bg-white/5">
                        Cancel
                    </button>
                    <button type="submit" class="rounded bg-rose-500/20 border border-rose-500/40 px-5 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/30">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function leaveWorkspace() {
    return {
        showModal: false,
        activeLeave: {},
        rejectRequest(leave) {
            this.activeLeave = { ...leave };
            this.showModal = true;
        }
    }
}
</script>
@endsection
