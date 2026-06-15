@extends('layouts.app')

@section('title', 'Careers at Diwebs Tech - Engineer the Future')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-teal/10 px-3 py-1 text-xs text-brand-cyan border border-brand-teal/20 font-semibold mb-4">
            🚀 We Are Hiring
        </span>
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">
            Join Our Global Engineering Team
        </h1>
        <p class="mt-4 text-base text-[#94A3B8]/75">
            Build the next generation of AI integrations, secure CBT assessment engines, and scalable multi-tenant SaaS platforms.
        </p>
    </div>

    <!-- Job Listings -->
    <div class="space-y-6 max-w-4xl mx-auto mb-20">
        <h2 class="text-xl font-bold text-brand-white mb-6 flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-brand-cyan animate-pulse"></span>
            Open Opportunities
        </h2>

        @foreach($jobs as $job)
            <div class="glass-card rounded-2xl p-6 md:p-8 hover:border-brand-cyan/45 transition-all duration-300 group">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-brand-white group-hover:text-brand-cyan transition-colors">
                            {{ $job['title'] }}
                        </h3>
                        <div class="flex flex-wrap gap-2.5 mt-2">
                            <span class="text-xs text-brand-cyan bg-brand-teal/10 border border-brand-teal/20 px-2.5 py-0.5 rounded-md font-semibold">
                                💼 {{ $job['department'] }}
                            </span>
                            <span class="text-xs text-[#94A3B8]/60 bg-[#25282D]/60 border border-white/5 px-2.5 py-0.5 rounded-md font-semibold">
                                📍 {{ $job['location'] }}
                            </span>
                            <span class="text-xs text-[#94A3B8]/60 bg-[#25282D]/60 border border-white/5 px-2.5 py-0.5 rounded-md font-semibold">
                                ⏱ {{ $job['type'] }}
                            </span>
                        </div>
                    </div>
                    <button @click="document.getElementById('apply-form-section').scrollIntoView({ behavior: 'smooth' }); document.getElementById('job_interest').value = '{{ $job['title'] }}'" 
                            class="rounded-xl bg-brand-teal/10 border border-brand-teal/25 px-4 py-2.5 text-xs font-semibold text-brand-white hover:bg-brand-teal/20 hover:border-brand-cyan/40 transition-all cursor-pointer">
                        Apply Now
                    </button>
                </div>
                <p class="text-sm text-[#94A3B8]/70 leading-relaxed">
                    {{ $job['description'] }}
                </p>
            </div>
        @endforeach
    </div>

    <!-- Application Form Section -->
    <div id="apply-form-section" class="max-w-3xl mx-auto glass-card rounded-2xl p-8 md:p-12 mb-16 relative overflow-hidden">
        <div class="absolute top-0 right-0 h-40 w-40 rounded-full bg-brand-cyan/5 blur-[50px] -z-10"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-brand-teal/5 blur-[50px] -z-10"></div>

        <h3 class="text-2xl font-bold text-brand-white mb-2">Submit Your Application</h3>
        <p class="text-sm text-[#94A3B8]/75 mb-8">
            Tell us about your background and attach your details. We review all engineering applicants within 48 hours.
        </p>

        @if(session('applied'))
            <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-5 text-sm text-emerald-300 font-semibold mb-6 flex items-start gap-2.5">
                <span class="text-emerald-400">✔</span>
                <div>
                    <span class="block text-emerald-200">Application Submitted!</span>
                    <span class="block text-xs font-normal text-emerald-300/80 mt-1">Thank you for applying. Our recruiting specialists will contact you at the email provided.</span>
                </div>
            </div>
        @endif

        <form action="{{ route('lead.submit') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="service_needed" value="Career Application">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#94A3B8]/75 mb-2">Full Name</label>
                    <input type="text" name="name" required placeholder="John Doe"
                           class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-sm text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#94A3B8]/75 mb-2">Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com"
                           class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-sm text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#94A3B8]/75 mb-2">Phone Number</label>
                    <input type="text" name="phone" placeholder="+234..."
                           class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-sm text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#94A3B8]/75 mb-2">Job Role of Interest</label>
                    <select id="job_interest" name="company" required
                            class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-sm text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all">
                        <option value="">Select an opportunity...</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job['title'] }}">{{ $job['title'] }}</option>
                        @endforeach
                        <option value="General Engineering Interest">General / Open Application</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#94A3B8]/75 mb-2">GitHub / Portfolio URL & Cover Note</label>
                <textarea name="message" rows="4" required placeholder="Tell us about your technical background and paste links to your projects..."
                          class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-sm text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all"></textarea>
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-3.5 text-xs font-bold text-brand-dark-secondary hover:opacity-90 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer flex items-center justify-center gap-2">
                ✉ Submit Application
            </button>
        </form>
    </div>
</div>
@endsection
