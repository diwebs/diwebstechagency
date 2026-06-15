@extends('layouts.academy')

@section('title', 'Session Schedule & Recordings - Diwebs Academy')

@section('academy_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Session Schedule &amp; Recordings</h1>
        <p class="text-sm text-brand-gray mt-1">Review your scheduled mentorship calls and access previous class playbacks, audio files, and AI summaries.</p>
    </div>

    <!-- Scheduled bookings -->
    <div class="space-y-6 mb-12">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">My Scheduled Mentorship Sessions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($bookings as $booking)
                <div class="glass-card rounded-2xl p-5 border border-brand-teal/10 bg-brand-dark-secondary/20 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📅</span>
                        <div>
                            <h4 class="text-sm font-bold text-brand-white leading-tight">1-on-1 {{ str_replace('_', ' ', $booking->call_type) }} Call</h4>
                            <p class="text-[10px] text-brand-gray mt-1">Mentor: <span class="text-brand-cyan">{{ $booking->teacher ? $booking->teacher->name : 'Staff Mentor' }}</span></p>
                            <p class="text-[9px] text-brand-gray mt-0.5">{{ $booking->booking_date }} at {{ $booking->start_time }} - {{ $booking->end_time }}</p>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        @if($booking->meeting_url)
                            <a href="{{ $booking->meeting_url }}" target="_blank" class="rounded bg-brand-cyan text-brand-dark-secondary text-[10px] px-3 py-1.5 font-bold hover:opacity-90 transition-all shadow block text-center">
                                Join Now
                            </a>
                        @else
                            <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[9px] font-bold uppercase text-brand-cyan tracking-wider">
                                {{ strtoupper($booking->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 rounded-xl border border-dashed border-brand-teal/20 p-8 text-center text-brand-gray text-xs">
                    You have no scheduled coaching or 1-on-1 mentorship bookings.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Live recorded sessions archive -->
    <div class="space-y-6">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Recorded Sessions &amp; Replays</h3>
        
        <div class="space-y-4">
            @forelse($recordings as $rec)
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/10 bg-brand-dark-secondary/20 flex flex-col lg:flex-row justify-between gap-6 hover:border-brand-teal/20 transition-all">
                    
                    <!-- Text Metadata -->
                    <div class="flex-1 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase bg-purple-950 text-purple-400 border border-purple-500/20">
                                Replay Available
                            </span>
                            <span class="text-[9px] text-brand-gray/50">Expiry in {{ $rec->retention_days }} days</span>
                        </div>

                        <div>
                            <h4 class="text-base font-bold text-brand-white leading-snug">{{ $rec->title }}</h4>
                            <p class="text-[10px] text-brand-gray mt-0.5">Class session source: {{ $rec->liveSession ? $rec->liveSession->title : '1-on-1 Booking' }}</p>
                        </div>

                        <!-- Notes Summary Brief -->
                        @if($rec->notes)
                            <div class="bg-brand-dark-secondary/40 p-3 rounded-lg border border-brand-teal/5 text-xs text-brand-gray/95">
                                <strong class="text-brand-cyan text-[10px] block mb-1">Classroom Notes:</strong>
                                {{ $rec->notes }}
                            </div>
                        @endif

                        <!-- AI Generated Summaries -->
                        @if($rec->ai_summary)
                            <div class="bg-brand-teal/5 p-3 rounded-lg border border-brand-teal/10 text-xs text-brand-gray/90">
                                <strong class="text-brand-cyan text-[10px] block mb-1">🤖 AI Synthesis &amp; Key Takeaways:</strong>
                                {{ $rec->ai_summary }}
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="lg:w-48 flex-shrink-0 flex flex-col justify-center gap-3">
                        @if($rec->video_url)
                            <a href="{{ $rec->video_url }}" target="_blank" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-2.5 text-xs font-bold text-brand-dark-secondary shadow text-center hover:opacity-90 active:scale-95 transition-all cursor-pointer block font-sans">
                                🎥 Watch Video Replay
                            </a>
                        @endif

                        @if($rec->audio_url)
                            <a href="{{ $rec->audio_url }}" download class="rounded-xl bg-brand-dark-secondary border border-brand-teal/20 hover:border-brand-teal py-2.5 text-xs font-bold text-brand-cyan text-center transition-all cursor-pointer block">
                                🎧 Download Audio (MP3)
                            </a>
                        @endif
                    </div>

                </div>
            @empty
                <div class="rounded-xl border border-dashed border-brand-teal/20 p-8 text-center text-brand-gray text-xs">
                    No recordings from live classrooms have been compiled or uploaded yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
