@extends('layouts.app')

@section('title', 'Diwebs Tech Agency - Engineering Tomorrow With AI, Software & Infrastructure')

@section('content')
<div class="relative isolate overflow-hidden">
    <!-- Hero Section -->
    <div class="mx-auto max-w-7xl px-6 pb-24 pt-10 sm:pb-32 lg:px-8 lg:pt-20">
        <div class="mx-auto max-w-3xl text-center">
            <!-- Neon Glowing Badge -->
            <div class="inline-flex items-center gap-x-2 rounded-full bg-brand-teal/10 px-4 py-1.5 text-sm font-medium text-brand-cyan border border-brand-teal/20 mb-8 animate-pulse">
                <span>Enterprise Technology Partner</span>
            </div>
            
            <h1 class="text-4xl font-extrabold tracking-tight text-brand-white sm:text-6xl text-glow bg-gradient-to-r from-brand-white via-brand-gray to-brand-cyan bg-clip-text text-transparent leading-none">
                Engineering Tomorrow With AI, Software & Digital Infrastructure
            </h1>
            <p class="mt-6 text-lg leading-8 text-brand-gray max-w-2xl mx-auto">
                Diwebs Tech Agency builds world-class software, enterprise platforms, CBT infrastructure, AI systems, and digital training ecosystems for businesses, institutions, and governments.
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="#contact-section" class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-3 text-sm font-semibold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all font-bold">Start Project</a>
                <a href="{{ route('services') }}" class="text-sm font-semibold leading-6 text-brand-white hover:text-brand-cyan transition-all flex items-center gap-1">Explore Solutions <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </div>

    <!-- Live Interactive Metrics Dashboard Section -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 mb-24">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Metric Card 1 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">Projects Delivered</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">120+</dd>
                <div class="mt-1 text-[10px] text-emerald-400">99.8% Success Rate</div>
            </div>
            <!-- Metric Card 2 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">Students Trained</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">15,000+</dd>
                <div class="mt-1 text-[10px] text-brand-cyan">Global Academy Partners</div>
            </div>
            <!-- Metric Card 3 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">CBT Centers Powered</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">450+</dd>
                <div class="mt-1 text-[10px] text-emerald-400">5M+ Exams Completed</div>
            </div>
            <!-- Metric Card 4 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">Countries Reached</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">12+</dd>
                <div class="mt-1 text-[10px] text-brand-cyan">Cross-border Deployments</div>
            </div>
        </div>
    </div>

    <!-- Interactive Solutions / Grid List -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 mb-28">
        <div class="mx-auto max-w-2xl lg:text-center mb-16">
            <h2 class="text-base font-semibold text-brand-cyan uppercase tracking-wider">Ecosystem Architecture</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-brand-white sm:text-4xl">
                Ecosystem Modules Designed for Global Scale
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="glass-card glass-card-hover rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-teal/5 rounded-full blur-xl"></div>
                <div>
                    <span class="text-2xl">⚡</span>
                    <h3 class="mt-4 text-xl font-bold text-brand-white">Enterprise Systems</h3>
                    <p class="mt-2 text-sm text-brand-gray">
                        High-availability distributed architectures, custom API layers, and multi-tenant SaaS infrastructure built on Laravel 12.
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('services') }}" class="text-xs text-brand-cyan hover:underline font-medium">Read details →</a>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="glass-card glass-card-hover rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-cyan/5 rounded-full blur-xl"></div>
                <div>
                    <span class="text-2xl">🧠</span>
                    <h3 class="mt-4 text-xl font-bold text-brand-white">AI Solutions & Agents</h3>
                    <p class="mt-2 text-sm text-brand-gray">
                        Cognitive automation pipelines, predictive analytics, intelligent chat assistance, and dynamic learning algorithms.
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('services') }}" class="text-xs text-brand-cyan hover:underline font-medium">Read details →</a>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="glass-card glass-card-hover rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-teal/5 rounded-full blur-xl"></div>
                <div>
                    <span class="text-2xl">📝</span>
                    <h3 class="mt-4 text-xl font-bold text-brand-white">CBT Exam Engine</h3>
                    <p class="mt-2 text-sm text-brand-gray">
                        Secure exam hosting with localized seat allocation, browser restriction locks, tab exit logs, and webcam facial verification.
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('cbt.dashboard') }}" class="text-xs text-brand-cyan hover:underline font-medium">Test CBT Portal →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Intake CRM Form Section -->
    <div id="contact-section" class="mx-auto max-w-3xl px-6 lg:px-8 pb-12">
        <div class="glass-card rounded-3xl p-8 md:p-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-dot-matrix opacity-40"></div>
            
            <div class="relative z-10 text-center mb-8">
                <h3 class="text-2xl font-bold text-brand-white">Initiate Your Digital Transformation</h3>
                <p class="mt-2 text-sm text-brand-gray">Provide project parameters, and our system architects will schedule an engineering workshop.</p>
            </div>

            <form action="{{ route('lead.submit') }}" method="POST" class="relative z-10 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-brand-cyan uppercase">Company Representative Name</label>
                        <input type="text" name="name" id="name" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold text-brand-cyan uppercase">Corporate Email Address</label>
                        <input type="email" name="email" id="email" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-brand-cyan uppercase">Direct Contact Number</label>
                        <input type="text" name="phone" id="phone" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all" placeholder="+234...">
                    </div>
                    <div>
                        <label for="service_needed" class="block text-xs font-semibold text-brand-cyan uppercase">Required Specialization</label>
                        <select name="service_needed" id="service_needed" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                            <option value="Enterprise Systems & Web App">Enterprise Systems & Web App</option>
                            <option value="AI Cognitive Solutions">AI Cognitive Solutions</option>
                            <option value="CBT Infrastructure Deployment">CBT Infrastructure Deployment</option>
                            <option value="Cloud Migration & DevOps">Cloud Migration & DevOps</option>
                            <option value="Training Academy Partnership">Training Academy Partnership</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-xs font-semibold text-brand-cyan uppercase">Project Objectives & Timeline</label>
                    <textarea name="message" id="message" rows="4" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all" placeholder="Briefly detail what you are looking to build..."></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="w-full sm:w-auto rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-3.5 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Submit Requirements</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
