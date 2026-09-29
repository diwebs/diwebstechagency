@extends('layouts.admin')

@section('title', 'CRM Appointments - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddMeeting: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Meetings &amp; Consultation Booking</h1>
            <p class="text-xs text-brand-gray mt-1">Manage consultation calls, sales meetings, system demos, and client onboarding sessions.</p>
        </div>
        <button @click="showAddMeeting = !showAddMeeting" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Book Meeting
        </button>
    </div>

    <!-- Intake form -->
    <div x-show="showAddMeeting" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Schedule Meeting</h3>
        <form action="{{ route('admin.crm.meetings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Meeting Title</label>
                <input type="text" name="title" required placeholder="Pre-onboarding Kickoff Session" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Meeting Type</label>
                <select name="meeting_type" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="consultation call">Consultation Call</option>
                    <option value="sales meeting">Sales Pitch / Discovery</option>
                    <option value="demo">Product Demo Session</option>
                    <option value="onboarding session">Client Onboarding</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Scheduled DateTime</label>
                <input type="datetime-local" name="scheduled_at" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Duration (Minutes)</label>
                <input type="number" name="duration_minutes" value="30" min="5" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Lead Associated</label>
                <select name="crm_lead_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">None</option>
                    @foreach($leads as $ld)
                        <option value="{{ $ld->id }}">{{ $ld->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Client Associated</label>
                <select name="crm_client_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">None</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->contact_person }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Staff Organizer</label>
                <select name="assigned_staff_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">None</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 md:col-span-2">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Attendees Emails (comma-separated)</label>
                <input type="text" name="attendees" placeholder="client@email.com, designer@diwebstechagency.website" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Agenda &amp; Notes</label>
                <textarea name="notes" rows="2" placeholder="Agenda of the session..." class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none"></textarea>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddMeeting = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Book Appointment</button>
            </div>
        </form>
    </div>

    <!-- Calendar View & Meeting List Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Interactive Calendars Widgets -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 lg:col-span-2">
            <div class="flex items-center justify-between border-b border-brand-teal/10 pb-3 mb-6">
                <h3 class="text-sm font-semibold uppercase text-brand-cyan">Appointments Calendar</h3>
                <span class="text-xs text-brand-gray">{{ date('F Y') }}</span>
            </div>
            
            <!-- Standard CSS grid calendar -->
            <div class="grid grid-cols-7 gap-2 text-center text-[10px] text-brand-gray font-bold mb-2">
                <div>SUN</div><div>MON</div><div>TUE</div><div>WED</div><div>THU</div><div>FRI</div><div>SAT</div>
            </div>
            <div class="grid grid-cols-7 gap-2 h-64">
                <!-- Empty days padding (simulated for July 2026 starting on Wednesday) -->
                <div class="bg-brand-dark-secondary/15 rounded-lg"></div>
                <div class="bg-brand-dark-secondary/15 rounded-lg"></div>
                <div class="bg-brand-dark-secondary/15 rounded-lg"></div>
                
                @for($day = 1; $day <= 31; $day++)
                    @php
                        // Check if any meetings fall on this day
                        $dayStr = sprintf('2026-07-%02d', $day);
                        $hasMeeting = $meetings->contains(function($value) use ($dayStr) {
                            return $value->scheduled_at->toDateString() === $dayStr;
                        });
                    @endphp
                    <div class="rounded-lg border relative flex flex-col items-center justify-center cursor-pointer hover:bg-brand-teal/10 transition-colors
                        {{ $hasMeeting ? 'border-brand-cyan bg-brand-teal/5 text-brand-cyan' : 'border-brand-teal/5 bg-brand-dark-secondary/20 text-brand-white' }}">
                        <span class="font-bold font-mono">{{ $day }}</span>
                        @if($hasMeeting)
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-cyan mt-1"></span>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Schedule List -->
        <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
            <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Booked Sessions Queue</h3>
            <div class="space-y-4 max-h-[320px] overflow-y-auto pr-1">
                @forelse($meetings as $meet)
                    <div class="bg-brand-dark-secondary/35 rounded-xl border border-brand-teal/10 p-3 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="rounded bg-brand-teal/15 px-2 py-0.5 text-[9px] font-bold text-brand-cyan uppercase">{{ $meet->meeting_type }}</span>
                            <span class="text-brand-gray/60 font-mono text-[9px]">{{ $meet->duration_minutes }} min</span>
                        </div>
                        <strong class="block text-brand-white font-bold">{{ $meet->title }}</strong>
                        
                        <div class="flex justify-between items-center text-[10px] text-brand-gray">
                            <span>Host: {{ $meet->assignedStaff ? $meet->assignedStaff->name : 'Unassigned' }}</span>
                            <span class="text-brand-cyan font-bold font-mono">{{ $meet->scheduled_at->format('M d, H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-brand-gray">No bookings logged for this month.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
