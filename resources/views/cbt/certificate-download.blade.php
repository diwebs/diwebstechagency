@extends('layouts.app')

@section('title', 'Validate Certificate - Diwebs CBT')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">
    <div class="mb-6 flex justify-between items-center text-xs">
        <a href="{{ route('cbt.certificates') }}" class="text-brand-cyan hover:underline">← Back to Panel</a>
        <button onclick="window.print()" class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 font-bold text-brand-dark-secondary cursor-pointer shadow">
            🖨️ Print Certificate
        </button>
    </div>

    <!-- Certificate Border Frame -->
    <div class="bg-[#1A1D21] border-8 border-brand-teal/40 rounded-3xl p-12 relative overflow-hidden text-center shadow-2xl">
        <!-- Secure seal background glow -->
        <div class="absolute inset-0 bg-dot-matrix opacity-10 pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-brand-teal/5 rounded-full blur-[100px] pointer-events-none"></div>

        <!-- Header -->
        <div class="space-y-4 mb-8">
            <span class="text-4xl">🏆</span>
            <h2 class="text-xs font-black uppercase text-brand-cyan tracking-[0.25em]">Diwebs Testing Services</h2>
            <h1 class="text-3xl font-extrabold tracking-tight text-brand-white font-sans mt-2">CERTIFICATE OF COMPETENCY</h1>
        </div>

        <p class="text-sm text-brand-gray/80 leading-relaxed max-w-lg mx-auto">
            This certifies that candidate <strong class="text-brand-white">{{ $certificate->user->name }}</strong> has successfully passed the secure timed examination and demonstrated proficiency in standard industry specifications.
        </p>

        <!-- Course / Exam Title -->
        <div class="my-8 py-4 border-y border-brand-teal/10 max-w-xl mx-auto">
            <span class="block text-xs uppercase text-brand-cyan font-bold tracking-widest">Certified Curriculum</span>
            <strong class="block text-xl font-bold text-brand-white mt-1.5">{{ $certificate->exam->title }}</strong>
        </div>

        <!-- Grade and serial details -->
        <div class="grid grid-cols-3 gap-6 max-w-md mx-auto text-xs text-brand-gray mb-10">
            <div>
                <span class="block uppercase font-bold text-[9px] tracking-wider">Scored Grade</span>
                <strong class="text-brand-white text-sm font-mono mt-1 block">{{ $certificate->grade }}%</strong>
            </div>
            <div>
                <span class="block uppercase font-bold text-[9px] tracking-wider">Validation Date</span>
                <strong class="text-brand-white text-sm font-mono mt-1 block">{{ $certificate->issue_date->format('M d, Y') }}</strong>
            </div>
            <div>
                <span class="block uppercase font-bold text-[9px] tracking-wider">Serial Verification</span>
                <strong class="text-brand-white text-sm font-mono mt-1 block">{{ $certificate->certificate_number }}</strong>
            </div>
        </div>

        <!-- Signatures & Verification Row -->
        <div class="border-t border-brand-teal/5 pt-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center max-w-2xl mx-auto">
            <!-- Signature -->
            <div class="space-y-1 text-center md:text-left">
                <span class="font-serif italic text-brand-cyan block text-lg">Diwebs Systems Director</span>
                <div class="h-0.5 w-32 bg-brand-teal/30 mx-auto md:mx-0"></div>
                <span class="text-[9px] uppercase tracking-wider text-brand-gray/60 mt-1 block">Validated Digital Signature</span>
            </div>

            <!-- QR code verification check -->
            <div class="flex items-center justify-center md:justify-end gap-4">
                <div class="h-16 w-16 bg-white p-1 rounded-lg">
                    <!-- Placeholder QR code -->
                    <span class="text-3xl text-brand-dark-secondary select-none">🏁</span>
                </div>
                <div class="text-left text-[9px] text-brand-gray max-w-[150px]">
                    <span class="block font-bold text-brand-white">Scan to Verify</span>
                    Validate offline certificates against our secure digital registry via unique hashing tokens.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
