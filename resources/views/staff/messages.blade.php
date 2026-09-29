@extends('layouts.staff')

@section('title', 'Corporate Board - Staff Portal')

@section('staff_content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-brand-white">Corporate Announcements &amp; Board</h1>
        <p class="text-xs text-brand-gray mt-1 font-sans">Receive key strategic directions, updates, and announcements from the C-Suite and department heads.</p>
    </div>

    <!-- Announcements list -->
    <div class="space-y-6">
        @foreach($announcements as $ann)
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2 border-b border-brand-teal/10 pb-3">
                    <div>
                        <span class="inline-flex rounded-full bg-brand-cyan/10 border border-brand-cyan/35 px-2.5 py-0.5 text-[9px] font-extrabold text-brand-cyan uppercase tracking-wider font-mono">Announcement</span>
                        <h2 class="text-base font-bold text-brand-white mt-2">{{ $ann['title'] }}</h2>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-brand-gray block font-mono">Date: {{ $ann['date'] }}</span>
                        <span class="text-[10px] text-brand-cyan font-bold block">Sender: {{ $ann['sender'] }}</span>
                    </div>
                </div>

                <div class="text-xs text-brand-white leading-relaxed font-sans font-medium">
                    {!! nl2br(e($ann['content'])) !!}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
