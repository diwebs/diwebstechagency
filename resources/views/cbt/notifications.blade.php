@extends('layouts.cbt')

@section('title', 'CBT Notifications - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Notifications &amp; Schedule Reminders</h1>
            <p class="text-sm text-brand-gray mt-1">Updates concerning your examination seat bookings, grades releases, and registration parameters.</p>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <!-- Notification 1 -->
        <div class="flex items-start gap-4 p-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="text-2xl">🏆</span>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-brand-white">Certificate Credentials Released</span>
                    <span class="rounded bg-brand-teal/15 text-brand-cyan text-[8px] font-extrabold uppercase px-1.5 py-0.5">Scoring Grader</span>
                </div>
                <p class="text-xs text-brand-gray/80 leading-relaxed">Your credentials file for AWS Cloud Practitioner Mock Test has been cryptographically signed. Download the offline print sheet from the Certificates tab.</p>
                <span class="block text-[9px] text-brand-gray/50">2 hours ago</span>
            </div>
        </div>

        <!-- Notification 2 -->
        <div class="flex items-start gap-4 p-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20">
            <span class="text-2xl">🏫</span>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-brand-white">Seat Allocation Schedule Confirmed</span>
                    <span class="rounded bg-brand-teal/15 text-brand-cyan text-[8px] font-extrabold uppercase px-1.5 py-0.5">Center Hub</span>
                </div>
                <p class="text-xs text-brand-gray/80 leading-relaxed">Seat number SEAT-004 has been mapped for your National IT Scholarship exam at Lagos Central Center. Arrive 30 minutes before launch check-in.</p>
                <span class="block text-[9px] text-brand-gray/50">1 day ago</span>
            </div>
        </div>
    </div>
</div>
@endsection
