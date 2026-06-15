@extends('layouts.app')

@section('title', $service['title'] . ' - Diwebs Technical Specialization')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    
    <!-- Header Block -->
    <div class="mx-auto max-w-3xl text-center mb-16">
        <a href="{{ route('services') }}" class="inline-flex items-center gap-1.5 text-xs text-brand-cyan hover:text-brand-white transition-colors mb-4 font-semibold">
            ← Back to All Services
        </a>
        <div class="flex justify-center text-4xl mb-4 opacity-90">{{ $service['icon'] }}</div>
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">
            {{ $service['title'] }}
        </h1>
        <p class="mt-2 text-md text-brand-cyan font-semibold tracking-wide uppercase">
            {{ $service['subtitle'] }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start mb-16">
        
        <!-- Left 2 Columns: Details & Features -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-card rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-dot-matrix opacity-10"></div>
                <h3 class="text-xl font-bold text-brand-white mb-4">Core Overview</h3>
                <p class="text-sm text-[#94A3B8]/75 leading-relaxed">
                    {{ $service['description'] }}
                </p>
            </div>

            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-xl font-bold text-brand-white mb-6">Key Specializations & Deliverables</h3>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    @foreach($service['features'] as $feature)
                        <li class="flex items-start gap-3 bg-[#25282D]/40 border border-white/5 p-4 rounded-xl">
                            <span class="text-brand-cyan text-sm flex-shrink-0">✔</span>
                            <span class="text-[#94A3B8]/80 font-medium leading-relaxed">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Tech Stack badge grid -->
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-xl font-bold text-brand-white mb-4">Technologies & Platforms</h3>
                <div class="flex flex-wrap gap-2.5">
                    @foreach($service['tech_stack'] as $tech)
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#25282D] border border-white/10 px-3.5 py-2 text-xs text-brand-white font-medium hover:border-brand-cyan/40 hover:bg-brand-cyan/5 transition-all">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-cyan"></span>
                            {{ $tech }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Interactive Consultation Card -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-2xl p-8 border border-brand-teal/20 relative overflow-hidden sticky top-24">
                <div class="absolute top-0 right-0 h-32 w-32 rounded-full bg-brand-cyan/5 blur-[40px] -z-10"></div>
                <h3 class="text-lg font-bold text-brand-white mb-2">Request Consultation</h3>
                <p class="text-xs text-[#94A3B8]/65 mb-6">
                    Looking to integrate {{ strtolower($service['title']) }} within your infrastructure? Request a discovery call.
                </p>

                <form action="{{ route('lead.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="service_needed" value="{{ $service['title'] }}">
                    
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]/60 mb-1.5">Name</label>
                        <input type="text" name="name" required placeholder="Your name"
                               class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-3.5 py-2.5 text-xs text-brand-white placeholder-[#94A3B8]/30 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]/60 mb-1.5">Corporate Email</label>
                        <input type="email" name="email" required placeholder="you@company.com"
                               class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-3.5 py-2.5 text-xs text-brand-white placeholder-[#94A3B8]/30 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]/60 mb-1.5">Project Notes</label>
                        <textarea name="message" rows="3" required placeholder="Describe your timeline and goals..."
                                  class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-3.5 py-2.5 text-xs text-brand-white placeholder-[#94A3B8]/30 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-3 text-xs font-bold text-brand-dark-secondary hover:opacity-90 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                        Schedule Discovery Call
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
