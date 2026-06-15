@extends('layouts.cbt')

@section('title', 'Verified Credentials - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Secure Certification Credentials</h1>
            <p class="text-sm text-brand-gray mt-1">Review enterprise-validated testing certificates, secure digital signatures, and offline print templates.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($certificates as $cert)
            <div class="glass-card rounded-3xl p-6 border border-brand-teal/20 relative overflow-hidden flex flex-col justify-between gap-6">
                <!-- Dot overlay -->
                <div class="absolute inset-0 bg-dot-matrix opacity-10 pointer-events-none"></div>
                
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <span class="rounded bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[9px] font-extrabold uppercase px-2.5 py-0.5 tracking-wider">Passed</span>
                        <h4 class="text-base font-bold text-brand-white mt-3 leading-snug">{{ $cert->exam->title }}</h4>
                        <span class="block text-[10px] text-brand-gray mt-1">Grade: <strong class="text-brand-cyan">{{ $cert->grade }}%</strong></span>
                    </div>

                    <!-- Mock QR code -->
                    <div class="h-16 w-16 bg-[#F8FAFC] p-1.5 rounded-lg flex items-center justify-center flex-shrink-0 border border-brand-teal/20 shadow shadow-black">
                        <span class="text-3xl text-brand-dark-secondary select-none">🏁</span>
                    </div>
                </div>

                <div class="border-t border-brand-teal/10 pt-4 flex items-center justify-between text-xs">
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-brand-gray/60 font-bold">Credential Serial</span>
                        <span class="font-mono font-bold text-brand-white">{{ $cert->certificate_number }}</span>
                    </div>

                    <a href="{{ route('cbt.certificate.download', $cert->id) }}" class="rounded bg-brand-dark-secondary border border-brand-teal/30 hover:border-brand-teal text-brand-cyan px-4 py-2 font-bold transition-all text-[11px] block">
                        Verify &amp; Print
                    </a>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 glass-card rounded-2xl p-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20">
                You have not earned any secure certification credentials yet. Complete assessments with passing score thresholds to qualify.
            </div>
        @endforelse
    </div>
</div>
@endsection
