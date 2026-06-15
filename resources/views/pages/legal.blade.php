@extends('layouts.app')

@section('title', $policy['title'] . ' - Diwebs Tech')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 items-start">
        
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1 space-y-3 sticky top-24">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]/40 mb-3 px-3">Legal &amp; Compliance Hub</span>
            
            <a href="{{ route('legal.show', 'privacy-policy') }}" 
               class="block rounded-xl px-4 py-3 text-xs font-semibold border transition-all duration-200 {{ request()->routeIs('legal.show') && request()->route('slug') === 'privacy-policy' ? 'bg-brand-teal/10 border-brand-teal/25 text-brand-cyan' : 'bg-[#25282D]/40 border-white/5 text-[#94A3B8]/75 hover:bg-[#25282D]/80 hover:text-brand-white' }}">
                Privacy Policy
            </a>
            
            <a href="{{ route('legal.show', 'terms-of-service') }}" 
               class="block rounded-xl px-4 py-3 text-xs font-semibold border transition-all duration-200 {{ request()->routeIs('legal.show') && request()->route('slug') === 'terms-of-service' ? 'bg-brand-teal/10 border-brand-teal/25 text-brand-cyan' : 'bg-[#25282D]/40 border-white/5 text-[#94A3B8]/75 hover:bg-[#25282D]/80 hover:text-brand-white' }}">
                Terms of Service
            </a>
            
            <a href="{{ route('legal.show', 'cookie-settings') }}" 
               class="block rounded-xl px-4 py-3 text-xs font-semibold border transition-all duration-200 {{ request()->routeIs('legal.show') && request()->route('slug') === 'cookie-settings' ? 'bg-brand-teal/10 border-brand-teal/25 text-brand-cyan' : 'bg-[#25282D]/40 border-white/5 text-[#94A3B8]/75 hover:bg-[#25282D]/80 hover:text-brand-white' }}">
                Cookie Policy &amp; Settings
            </a>
            
            <a href="{{ route('legal.show', 'platform-security') }}" 
               class="block rounded-xl px-4 py-3 text-xs font-semibold border transition-all duration-200 {{ request()->routeIs('legal.show') && request()->route('slug') === 'platform-security' ? 'bg-brand-teal/10 border-brand-teal/25 text-brand-cyan' : 'bg-[#25282D]/40 border-white/5 text-[#94A3B8]/75 hover:bg-[#25282D]/80 hover:text-brand-white' }}">
                Platform Security Standards
            </a>
        </div>

        <!-- Document Main Area -->
        <div class="lg:col-span-3">
            <div class="glass-card rounded-2xl p-8 md:p-12 relative overflow-hidden">
                <div class="absolute inset-0 bg-dot-matrix opacity-10"></div>
                
                <div class="border-b border-white/5 pb-6 mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold text-brand-white sm:text-3xl text-glow bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">
                            {{ $policy['title'] }}
                        </h1>
                        <p class="text-xs text-[#94A3B8]/50 mt-1">
                            Last updated: {{ $policy['last_updated'] }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-[10px] text-emerald-400 border border-emerald-500/20 font-semibold">
                        <span class="h-1 w-1 rounded-full bg-emerald-400"></span> Verified Compliance
                    </span>
                </div>

                <div class="space-y-8">
                    @foreach($policy['sections'] as $title => $body)
                        <div class="space-y-3">
                            <h3 class="text-sm font-bold text-brand-white tracking-wide uppercase">{{ $title }}</h3>
                            <p class="text-xs text-[#94A3B8]/70 leading-relaxed bg-[#25282D]/20 border border-white/5 p-4.5 rounded-xl">
                                {{ $body }}
                            </p>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t border-white/5 pt-8 mt-12 text-center">
                    <p class="text-[11px] text-[#94A3B8]/40 leading-relaxed mb-4">
                        Do you have legal, compliance, or security inquiries regarding our software or CBT exam sync models? You can reach us directly at <strong class="text-brand-cyan">compliance@diwebstechagency.website</strong>.
                    </p>
                    <a href="mailto:compliance@diwebstechagency.website" 
                       class="inline-flex items-center gap-2 rounded-xl bg-brand-teal/10 border border-brand-teal/25 px-4 py-2.5 text-xs font-semibold text-brand-white hover:bg-brand-teal/20 hover:border-brand-cyan/40 transition-all cursor-pointer">
                        ✉ Contact Legal &amp; Compliance Team
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
