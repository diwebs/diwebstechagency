@extends('layouts.admin')

@section('title', 'CBT Command Center - Super Admin Diwebs')

@section('admin_content')
<div class="space-y-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">CBT Command Center</h1>
            <p class="text-sm text-brand-gray mt-1">Supervise physical test hubs, live proctoring violations, and scheduled examinations.</p>
        </div>
        <div class="flex items-center gap-2 bg-brand-teal/10 border border-brand-teal/20 px-4 py-2 rounded-xl">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs text-brand-cyan font-bold">Pearson VUE / Prometric-Grade Engine Active</span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Center Applications Checklist -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-3 mb-4">
                    Center Partner Applications
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-brand-dark-secondary/40 text-brand-gray border-b border-brand-teal/10">
                                <th class="p-4 font-bold">Partner Org</th>
                                <th class="p-4 font-bold">Parameters</th>
                                <th class="p-4 font-bold">Status</th>
                                <th class="p-4 font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                            @forelse($enrollments as $enrollment)
                                <tr class="hover:bg-brand-teal/5 transition-all">
                                    <td class="p-4">
                                        <strong class="text-brand-white block">{{ $enrollment->organization_name }}</strong>
                                        <span class="text-brand-gray text-[10px] block">Owner: {{ $enrollment->user->name }}</span>
                                    </td>
                                    <td class="p-4 text-[10px] space-y-0.5">
                                        <span class="block">💻 Systems: {{ $enrollment->systems_count }}</span>
                                        <span class="block">📶 ISP Link: {{ ucfirst($enrollment->internet_quality) }}</span>
                                        <span class="block">🔋 Power: {{ str_replace('_', ' ', $enrollment->power_backup) }}</span>
                                    </td>
                                    <td class="p-4">
                                        @if($enrollment->status === 'approved')
                                            <span class="rounded bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 text-[9px] font-bold text-emerald-400 uppercase">Approved</span>
                                        @elseif($enrollment->status === 'rejected')
                                            <span class="rounded bg-rose-500/10 border border-rose-500/30 px-2 py-0.5 text-[9px] font-bold text-rose-400 uppercase">Rejected</span>
                                        @else
                                            <span class="rounded bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 text-[9px] font-bold text-amber-400 uppercase">{{ $enrollment->status }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        @if($enrollment->status === 'pending' || $enrollment->status === 'under_review')
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('admin.cbt.enrollment.status', $enrollment->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved" />
                                                    <button type="submit" class="rounded bg-emerald-500 hover:bg-emerald-600 px-2.5 py-1 text-[10px] font-bold text-brand-dark-secondary cursor-pointer">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.cbt.enrollment.status', $enrollment->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected" />
                                                    <button type="submit" class="rounded bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 px-2.5 py-1 text-[10px] font-bold text-rose-400 cursor-pointer">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-brand-gray text-[10px]">Processed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-brand-gray">No center applications pending validation.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Proctored Live Exams List -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-3 mb-4">
                    Scheduled Synchronized Events
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-brand-dark-secondary/40 text-brand-gray border-b border-brand-teal/10">
                                <th class="p-4 font-bold">Exam</th>
                                <th class="p-4 font-bold">Scheduled At</th>
                                <th class="p-4 font-bold">Security Checklist</th>
                                <th class="p-4 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                            @forelse($liveExams as $liveExam)
                                <tr class="hover:bg-brand-teal/5 transition-all">
                                    <td class="p-4">
                                        <strong class="text-brand-white block">{{ $liveExam->exam->title }}</strong>
                                        <span class="text-brand-cyan font-mono text-[10px]">{{ $liveExam->exam->code }}</span>
                                    </td>
                                    <td class="p-4 font-mono text-brand-gray">{{ $liveExam->scheduled_at->format('M d, Y H:i') }}</td>
                                    <td class="p-4 text-[10px] space-y-0.5">
                                        <span class="block {{ $liveExam->camera_required ? 'text-emerald-400' : 'text-brand-gray' }}">
                                            {{ $liveExam->camera_required ? '📹 Camera Mandatory' : '📷 Camera Optional' }}
                                        </span>
                                        <span class="block {{ $liveExam->mic_required ? 'text-emerald-400' : 'text-brand-gray' }}">
                                            {{ $liveExam->mic_required ? '🎤 Mic Mandatory' : '🎤 Mic Optional' }}
                                        </span>
                                        <span class="block {{ $liveExam->browser_lock_required ? 'text-emerald-400' : 'text-brand-gray' }}">
                                            {{ $liveExam->browser_lock_required ? '🔒 Secure Browser Active' : '🔓 Browser Lock Off' }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if($liveExam->status === 'scheduled')
                                            <span class="rounded bg-brand-cyan/15 text-brand-cyan border border-brand-cyan/25 px-2 py-0.5 text-[9px] font-bold uppercase">Scheduled</span>
                                        @else
                                            <span class="rounded bg-brand-gray/15 text-brand-gray px-2 py-0.5 text-[9px] font-bold uppercase">{{ $liveExam->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-brand-gray">No live assessment slots registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Sidebar (Schedule Event Form & Anti-Cheat Violations) -->
        <div class="space-y-6">
            
            <!-- Schedule Event Form -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">
                    Schedule Live Proctor Exam
                </h3>
                
                <form action="{{ route('admin.cbt.live-exam.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="block text-brand-gray uppercase font-bold text-[9px]">Select Assessment</label>
                        <select name="exam_id" class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan">
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-brand-gray uppercase font-bold text-[9px]">Event DateTime</label>
                        <input type="datetime-local" name="scheduled_at" required class="w-full rounded-xl bg-brand-dark-secondary border border-brand-teal/15 p-3 text-brand-white focus:outline-none focus:ring-1 focus:ring-brand-cyan" />
                    </div>

                    <div class="space-y-2 border-t border-brand-teal/5 pt-3">
                        <label class="flex items-center gap-2 text-brand-white select-none">
                            <input type="checkbox" name="camera_required" value="1" checked class="rounded bg-brand-dark-secondary border-brand-teal/20 text-brand-cyan focus:ring-0" />
                            <span>Mandate Webcam Feed Validation</span>
                        </label>
                        <label class="flex items-center gap-2 text-brand-white select-none">
                            <input type="checkbox" name="mic_required" value="1" checked class="rounded bg-brand-dark-secondary border-brand-teal/20 text-brand-cyan focus:ring-0" />
                            <span>Mandate Microphone Verification</span>
                        </label>
                        <label class="flex items-center gap-2 text-brand-white select-none">
                            <input type="checkbox" name="browser_lock_required" value="1" checked class="rounded bg-brand-dark-secondary border-brand-teal/20 text-brand-cyan focus:ring-0" />
                            <span>Mandate Fullscreen Browser Lock</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full rounded bg-gradient-to-r from-brand-teal to-brand-cyan py-2.5 text-xs font-bold text-brand-dark-secondary hover:opacity-90 active:scale-95 transition-all cursor-pointer">
                        ⚡ Create Proctor Event
                    </button>
                </form>
            </div>

            <!-- Live Proctor Security Violation Feed -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">
                    Security Activity log
                </h3>
                
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($violations as $violation)
                        <div class="p-3 rounded-xl bg-rose-500/5 border border-rose-500/15 text-[11px] space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-brand-white">{{ $violation->session->user->name }}</span>
                                <span class="text-[9px] font-mono text-rose-400 bg-rose-500/10 border border-rose-500/20 px-1.5 py-0.5 rounded">
                                    {{ strtoupper(str_replace('_', ' ', $violation->violation_type)) }}
                                </span>
                            </div>
                            <p class="text-brand-gray leading-normal">{{ $violation->details }}</p>
                            <span class="block text-[8px] text-brand-gray/60 text-right">{{ $violation->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-brand-gray text-center py-6">No security alerts or candidate flags recorded today.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- Active Physical Centers registry -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-3 mb-4">
            Certified Physical Center Partners
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($centers as $center)
                <div class="p-4 rounded-xl bg-brand-dark-secondary/40 border border-brand-teal/10 flex flex-col justify-between gap-4">
                    <div>
                        <div class="flex justify-between items-start">
                            <strong class="text-sm text-brand-white block">{{ $center->name }}</strong>
                            <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded uppercase">Active</span>
                        </div>
                        <span class="text-xs text-brand-cyan font-mono mt-1 block">ID: {{ $center->code }}</span>
                        <div class="mt-3 text-xs text-brand-gray space-y-0.5">
                            <p>City: {{ $center->city }}</p>
                            <p>Capacity: {{ $center->capacity }} Seats</p>
                            <p>Owner: {{ $center->owner->name }} ({{ $center->owner->email }})</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-brand-teal/5 pt-3 flex justify-between text-[11px] text-brand-cyan">
                        <span>Commission model: {{ $center->commission_rate }}%</span>
                        <strong>Accrued: ${{ number_format($center->revenue, 2) }}</strong>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-8 text-center text-brand-gray text-xs">
                    No physical examination centers certified yet. Approve applications to establish centers.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
