<!DOCTYPE html>
<html lang="en" class="h-full bg-brand-dark-secondary text-brand-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Diwebs Tech Agency - Digital Ecosystem')</title>
    
    <!-- PWA Meta Elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Diwebs">
    <meta name="theme-color" content="#1E2125">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icons/icon-512x512.svg">
    <link rel="apple-touch-icon" href="/icons/icon-512x512.svg">

    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('meta')
</head>
<body x-data="globalAppState()" class="h-full font-sans antialiased selection:bg-brand-cyan selection:text-brand-dark-secondary relative overflow-x-hidden pb-safe-bottom">

    <!-- Offline Status Warning -->
    <div x-show="isOffline"
         x-transition
         class="fixed top-0 left-0 right-0 z-[100] bg-rose-950/90 border-b border-rose-500/30 px-4 py-2.5 text-center text-xs text-rose-400 font-semibold flex items-center justify-center gap-2"
         style="display: none;">
        <span class="animate-pulse h-2 w-2 rounded-full bg-rose-500"></span>
        <span>You are currently offline. Showing cached shell parameters.</span>
    </div>

    <!-- Tech Grid and Neon Blur Background Spheres -->
    <div class="fixed inset-0 tech-grid -z-10 opacity-70 pointer-events-none"></div>
    <div class="fixed top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-brand-teal/10 blur-[120px] -z-10 animate-pulse-slow pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-brand-cyan/10 blur-[120px] -z-10 animate-pulse-slow pointer-events-none"></div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 w-full border-b border-brand-teal/10 bg-brand-dark-secondary/70 backdrop-blur-md pt-safe-top">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <img src="/images/brand/diwebs-logo.svg?v=2" alt="Diwebs" class="h-9 w-9">
                        <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">Diwebs <span class="text-brand-cyan">Tech</span></span>
                    </a>
                </div>

                <!-- Main Nav Links (Desktop) -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-brand-gray">
                    <a href="{{ route('home') }}" class="hover:text-brand-cyan transition-colors">Home</a>
                    <a href="{{ route('about') }}" class="hover:text-brand-cyan transition-colors">About</a>
                    <a href="{{ route('services') }}" class="hover:text-brand-cyan transition-colors">Services</a>
                    <a href="{{ route('solutions') }}" class="hover:text-brand-cyan transition-colors">Solutions</a>
                    <a href="{{ route('portfolio') }}" class="hover:text-brand-cyan transition-colors">Portfolio</a>
                    <a href="{{ route('news.index') }}" class="hover:text-brand-cyan transition-colors">News</a>
                    <a href="{{ route('academy.dashboard') }}" class="hover:text-brand-cyan transition-colors flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-brand-cyan animate-pulse"></span>Academy</a>
                    <a href="{{ route('cbt.dashboard') }}" class="hover:text-brand-cyan transition-colors">CBT Portal</a>
                </nav>

                <!-- Auth Operations (Desktop Only) -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <div class="flex items-center gap-3">
                            <span class="hidden sm:inline text-xs text-brand-gray">Logged as: <strong class="text-brand-white">{{ auth()->user()->name }}</strong> ({{ strtoupper(auth()->user()->role) }})</span>
                            
                            @if(auth()->user()->role === 'super_admin')
                                <a href="{{ route('admin.dashboard') }}" class="rounded-md bg-brand-teal/20 px-3.5 py-1.5 text-xs font-semibold text-brand-cyan border border-brand-teal/30 hover:bg-brand-teal/30 transition-all">Admin</a>
                            @elseif(auth()->user()->role === 'client')
                                <a href="{{ route('portal.dashboard') }}" class="rounded-md bg-brand-teal/20 px-3.5 py-1.5 text-xs font-semibold text-brand-cyan border border-brand-teal/30 hover:bg-brand-teal/30 transition-all">Client Area</a>
                            @endif

                            <a href="{{ route('profile.security') }}" class="text-xs font-medium text-brand-cyan hover:text-brand-white transition-colors">Security</a>

                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-brand-gray hover:text-brand-white transition-colors">Logout</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-brand-white hover:text-brand-cyan transition-colors">Sign in</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-3.5 py-2 text-sm font-semibold text-brand-dark-secondary shadow-sm hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-cyan transition-all">Get Started</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button (Mobile Only) -->
                <div class="flex md:hidden items-center gap-2">
                    <button type="button" 
                            class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-brand-gray hover:text-brand-white"
                            @click="mobileMenuOpen = !mobileMenuOpen">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- App Feedback Messages -->
    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md bg-emerald-950/50 border border-emerald-500/30 p-4 text-sm text-emerald-400">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md bg-rose-950/50 border border-rose-500/30 p-4 text-sm text-rose-400">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @php
        $isDashboard = request()->is('admin*') || request()->is('portal*') || request()->is('academy*') || request()->is('cbt*');
        $userRole = auth()->check() ? auth()->user()->role : '';
    @endphp

    <!-- Main View Slot -->
    <main class="py-10 {{ $isDashboard ? 'pb-24 md:pb-10' : '' }}">
        @yield('content')
    </main>

    <!-- Mobile Slide-over Drawer Menu -->
    <div x-show="mobileMenuOpen" 
         class="relative z-50 md:hidden" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-brand-dark-secondary/80 backdrop-blur-sm"
             @click="mobileMenuOpen = false"></div>

        <div class="fixed inset-y-0 right-0 z-50 w-full max-w-xs overflow-y-auto glass-card border-l border-brand-teal/20 px-6 py-6 sm:ring-1 sm:ring-white/10 flex flex-col justify-between"
             x-show="mobileMenuOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            
            <div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="-m-1.5 p-1.5 flex items-center gap-2.5" @click="mobileMenuOpen = false">
                        <img src="/images/brand/diwebs-logo.svg?v=2" alt="Diwebs" class="h-9 w-9">
                        <span class="text-xl font-bold tracking-tight text-brand-white">Diwebs</span>
                    </a>
                    <button type="button" 
                            class="-m-2.5 rounded-md p-2.5 text-brand-gray hover:text-brand-white"
                            @click="mobileMenuOpen = false">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-8 flow-root">
                    <div class="-my-6 divide-y divide-brand-teal/10">
                        <div class="space-y-2 py-6">
                            <a href="{{ route('home') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-white hover:bg-brand-teal/10 transition-all" @click="mobileMenuOpen = false">Corporate Home</a>
                            <a href="{{ route('about') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-white hover:bg-brand-teal/10 transition-all" @click="mobileMenuOpen = false">About Agency</a>
                            <a href="{{ route('services') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-white hover:bg-brand-teal/10 transition-all" @click="mobileMenuOpen = false">Our Services</a>
                            <a href="{{ route('solutions') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-white hover:bg-brand-teal/10 transition-all" @click="mobileMenuOpen = false">Enterprise Solutions</a>
                            <a href="{{ route('portfolio') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-white hover:bg-brand-teal/10 transition-all" @click="mobileMenuOpen = false">Portfolio Showcase</a>
                            <a href="{{ route('news.index') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-white hover:bg-brand-teal/10 transition-all" @click="mobileMenuOpen = false">Newsroom</a>
                            <a href="{{ route('academy.dashboard') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-cyan hover:bg-brand-teal/10 transition-all flex items-center gap-2" @click="mobileMenuOpen = false">
                                <span class="h-2 w-2 rounded-full bg-brand-cyan animate-pulse"></span>
                                Academy LMS
                            </a>
                            <a href="{{ route('cbt.dashboard') }}" class="-mx-3 block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-brand-white hover:bg-brand-teal/10 transition-all" @click="mobileMenuOpen = false">CBT Assessment Portal</a>
                        </div>

                        <div class="py-6 space-y-3">
                            @auth
                                <div class="px-3 text-xs text-brand-gray mb-2">
                                    Logged as: <strong class="text-brand-white">{{ auth()->user()->name }}</strong>
                                </div>
                                @if(auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg text-center bg-brand-teal/20 py-2.5 text-sm font-semibold text-brand-cyan border border-brand-teal/30 hover:bg-brand-teal/30" @click="mobileMenuOpen = false">Admin Dashboard</a>
                                @elseif(auth()->user()->role === 'client')
                                    <a href="{{ route('portal.dashboard') }}" class="block rounded-lg text-center bg-brand-teal/20 py-2.5 text-sm font-semibold text-brand-cyan border border-brand-teal/30 hover:bg-brand-teal/30" @click="mobileMenuOpen = false">Client Workspace</a>
                                @endif
                                <a href="{{ route('profile.security') }}" class="block rounded-lg text-center border border-brand-teal/20 py-2.5 text-sm font-semibold text-brand-white hover:bg-brand-teal/10" @click="mobileMenuOpen = false">Security Settings</a>
                                <form action="{{ route('logout') }}" method="POST" class="block w-full">
                                    @csrf
                                    <button type="submit" class="w-full rounded-lg text-center bg-rose-500/10 border border-rose-500/20 py-2.5 text-sm font-semibold text-rose-400 hover:bg-rose-500/25">Logout Session</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="block rounded-lg text-center border border-brand-teal/20 py-2.5 text-sm font-semibold text-brand-white hover:bg-brand-teal/10" @click="mobileMenuOpen = false">Sign In</a>
                                <a href="{{ route('register') }}" class="block rounded-lg text-center bg-gradient-to-r from-brand-teal to-brand-cyan py-2.5 text-sm font-bold text-brand-dark-secondary" @click="mobileMenuOpen = false">Get Started</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer of Menu -->
            <div class="border-t border-brand-teal/10 pt-6">
                <div class="flex flex-col gap-2">
                    <span class="text-[10px] text-brand-gray uppercase tracking-wider font-semibold block text-center mb-1">Testing Quick Bypass</span>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('auth.dev-login', 'super_admin') }}" class="rounded bg-brand-dark-secondary border border-brand-teal/10 py-1.5 text-center text-[10px] text-brand-cyan" @click="mobileMenuOpen = false">Admin</a>
                        <a href="{{ route('auth.dev-login', 'client') }}" class="rounded bg-brand-dark-secondary border border-brand-teal/10 py-1.5 text-center text-[10px] text-brand-cyan" @click="mobileMenuOpen = false">Client</a>
                        <a href="{{ route('auth.dev-login', 'student') }}" class="rounded bg-brand-dark-secondary border border-brand-teal/10 py-1.5 text-center text-[10px] text-brand-cyan" @click="mobileMenuOpen = false">Student</a>
                        <a href="{{ route('auth.dev-login', 'candidate') }}" class="rounded bg-brand-dark-secondary border border-brand-teal/10 py-1.5 text-center text-[10px] text-brand-cyan" @click="mobileMenuOpen = false">CBT</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Dashboard Bottom Navigation -->
    @if($isDashboard && auth()->check())
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-brand-dark-secondary/80 backdrop-blur-lg border-t border-brand-teal/15 px-4 md:hidden pb-safe-bottom"
             x-description="Mobile Dashboard Bottom Navigation">
            <div class="flex h-16 items-center justify-around">
                
                @if(auth()->user()->role === 'super_admin')
                    <!-- Admin Tabs -->
                    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('admin.dashboard') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">📊</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Home</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('admin.users') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">👥</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Users</span>
                    </a>
                    <a href="{{ route('admin.exams') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('admin.exams') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">📝</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Exams</span>
                    </a>
                    <a href="{{ route('admin.centers') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('admin.centers') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">🏫</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Centers</span>
                    </a>
                    <a href="{{ route('admin.security-logs') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('admin.security-logs') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">🛡️</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Logs</span>
                    </a>
                    
                @elseif(auth()->user()->role === 'client')
                    <!-- Client Tabs -->
                    <a href="{{ route('portal.dashboard') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('portal.dashboard') && !str_contains(request()->fullUrl(), '#') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">💼</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Workspace</span>
                    </a>
                    <a href="{{ route('portal.dashboard') }}#projects" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all text-brand-gray hover:text-brand-cyan">
                        <span class="text-xl">📂</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Projects</span>
                    </a>
                    <a href="{{ route('portal.dashboard') }}#invoices" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all text-brand-gray hover:text-brand-cyan">
                        <span class="text-xl">💳</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Invoices</span>
                    </a>
                    <a href="{{ route('profile.security') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('profile.security') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">🔒</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Security</span>
                    </a>

                @elseif(auth()->user()->role === 'student')
                    <!-- Student Tabs -->
                    <a href="{{ route('academy.dashboard') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('academy.dashboard') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">🎓</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">LMS Home</span>
                    </a>
                    <a href="{{ route('cbt.dashboard') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('cbt.dashboard') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">📝</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Assessments</span>
                    </a>
                    <a href="{{ route('profile.security') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('profile.security') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">🔒</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Security</span>
                    </a>

                @elseif(auth()->user()->role === 'candidate')
                    <!-- Candidate Tabs -->
                    <a href="{{ route('cbt.dashboard') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('cbt.dashboard') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">📝</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Assessments</span>
                    </a>
                    <a href="{{ route('profile.security') }}" class="flex flex-col items-center justify-center flex-1 text-center py-1 transition-all {{ request()->routeIs('profile.security') ? 'text-brand-cyan' : 'text-brand-gray' }}">
                        <span class="text-xl">🔒</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider mt-0.5">Security</span>
                    </a>
                @endif

            </div>
        </div>
    @endif

    <!-- PWA Install Prompt popup for iOS and Android -->
    <div x-show="showInstallPrompt"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-20 left-4 right-4 z-40 glass-card rounded-2xl p-5 border border-brand-cyan/25 flex flex-col md:hidden max-w-sm mx-auto"
         style="display: none;">
        
        <div class="flex items-start gap-3">
            <div class="h-10 w-10 rounded-lg bg-brand-dark-secondary border border-brand-teal/20 flex items-center justify-center flex-shrink-0">
                <img src="/images/brand/diwebs-logo.svg?v=2" alt="Diwebs" class="h-8 w-8">
            </div>
            <div>
                <h4 class="text-sm font-bold text-brand-white">Install Diwebs App Experience</h4>
                <p class="text-[11px] text-brand-gray mt-1 leading-relaxed">Get a faster, distraction-free experience in fullscreen mode with offline access.</p>
            </div>
        </div>

        <div class="mt-4 border-t border-brand-teal/10 pt-3">
            <template x-if="isIOS">
                <div class="text-[10px] text-brand-cyan leading-relaxed flex items-center gap-1.5">
                    <span>💡</span>
                    <span>To install: Tap the <strong class="underline">Share icon</strong> in Safari, scroll and tap <strong class="underline">Add to Home Screen</strong>.</span>
                </div>
            </template>

            <div class="flex justify-end gap-3 mt-3">
                <button type="button" 
                        class="px-3 py-1.5 rounded text-xs text-brand-gray hover:text-brand-white cursor-pointer select-none"
                        @click="dismissInstallPrompt()">
                    Later
                </button>
                <button type="button" 
                        class="rounded bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-1.5 text-xs font-bold text-brand-dark-secondary cursor-pointer select-none"
                        @click="triggerInstall()">
                    <span x-text="isIOS ? 'Got It' : 'Enable Now'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- World-Class Enterprise Footer -->
    <footer x-data="{
                activeAccordion: null,
                toggleAccordion(i) { this.activeAccordion = this.activeAccordion === i ? null : i; },
                copiedSupport: false,
                copiedBusiness: false,
                email: '',
                subscribeStatus: null,
                subscribeMessage: '',
                submitting: false,
                async subscribe() {
                    if (!this.email) return;
                    this.submitting = true; this.subscribeStatus = null;
                    try {
                        const r = await fetch('{{ route('newsletter.subscribe') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ email: this.email })
                        });
                        const d = await r.json();
                        this.subscribeStatus = d.success ? 'success' : 'error';
                        this.subscribeMessage = d.message;
                        if (d.success) this.email = '';
                    } catch(e) {
                        this.subscribeStatus = 'error';
                        this.subscribeMessage = 'Request failed. Please try again.';
                    } finally { this.submitting = false; }
                },
                scrollPercent: 0,
                updateScroll() {
                    const s = window.scrollY, d = document.documentElement.scrollHeight - window.innerHeight;
                    this.scrollPercent = d > 0 ? Math.round((s / d) * 100) : 0;
                },
                scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); },
                aiOpen: false,
                aiMessages: [{ sender: 'ai', text: 'Hello! I am your Diwebs Agent. How can I help you today?' }],
                aiInput: '',
                aiSending: false,
                sendAiMessage() {
                    if (!this.aiInput.trim()) return;
                    const userText = this.aiInput;
                    this.aiMessages.push({ sender: 'user', text: userText });
                    this.aiInput = ''; this.aiSending = true;
                    setTimeout(() => {
                        const t = userText.toLowerCase();
                        let reply = 'Thanks for reaching out! Email us at info.diwebs@gmail.com for details.';
                        if (t.includes('service') || t.includes('build') || t.includes('software')) reply = 'We build Enterprise Software, Mobile Apps, AI systems, SaaS, and CBT Infrastructure. What project can we help with?';
                        else if (t.includes('academy') || t.includes('course') || t.includes('learn')) reply = 'Diwebs Academy offers bootcamps, certifications, and LMS tools. Visit the Academy portal to enroll!';
                        else if (t.includes('price') || t.includes('cost') || t.includes('quote')) reply = 'Pricing is milestone-based per project scope. Email info.diwebs@gmail.com for a full RFP.';
                        this.aiMessages.push({ sender: 'ai', text: reply });
                        this.aiSending = false;
                        this.$nextTick(() => { const c = this.$refs.chatBox; if (c) c.scrollTop = c.scrollHeight; });
                    }, 900);
                }
            }"
            x-init="window.addEventListener('scroll', () => updateScroll())"
            class="relative bg-[#1A1D21] text-[#94A3B8] border-t border-brand-teal/10 {{ $isDashboard ? 'mb-16 md:mb-0' : '' }}">

        {{-- Subtle Background Glows --}}
        <div class="absolute top-0 right-1/3 w-[600px] h-[400px] bg-brand-teal/4 rounded-full blur-[150px] pointer-events-none" aria-hidden="true"></div>
        <div class="absolute bottom-0 left-1/4 w-[400px] h-[300px] bg-brand-cyan/3 rounded-full blur-[120px] pointer-events-none" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- ══════════════════════════════════════════════════════
                 ROW 1 · Brand · Newsletter · Contact
            ══════════════════════════════════════════════════════ --}}
            <div class="pt-14 pb-12 border-b border-white/5"
                 style="display:grid; grid-template-columns: repeat(1, 1fr); gap: 2.5rem;"
                 x-init="$el.style.gridTemplateColumns = window.innerWidth >= 1024 ? '1.6fr 1.4fr 1fr 1fr' : 'repeat(1, 1fr)';
                         window.addEventListener('resize', () => { $el.style.gridTemplateColumns = window.innerWidth >= 1024 ? '1.6fr 1.4fr 1fr 1fr' : 'repeat(1, 1fr)'; })">

                {{-- Brand --}}
                <div class="space-y-5">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group w-fit">
                        <img src="/images/brand/diwebs-logo.svg?v=2" alt="Diwebs" class="h-10 w-10 group-hover:scale-105 transition-transform duration-300">
                        <div class="leading-tight">
                            <span class="block text-base font-extrabold tracking-tight text-brand-white">Diwebs <span class="text-brand-cyan">Tech</span></span>
                            <span class="block text-[9px] uppercase tracking-[0.18em] text-brand-gray font-semibold">Agency</span>
                        </div>
                    </a>
                    <p class="text-xs leading-relaxed text-[#94A3B8]/75 max-w-sm">
                        We build enterprise software, AI systems, CBT infrastructure, digital training ecosystems, and scalable solutions for institutions worldwide.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-teal/10 border border-brand-teal/20 px-3 py-1 text-[10px] text-brand-cyan font-semibold">🛡️ Secure Platform</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-teal/10 border border-brand-teal/20 px-3 py-1 text-[10px] text-brand-cyan font-semibold">☁️ Cloud Enabled</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-teal/10 border border-brand-teal/20 px-3 py-1 text-[10px] text-brand-cyan font-semibold">🤖 AI Powered</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-teal/10 border border-brand-teal/20 px-3 py-1 text-[10px] text-brand-cyan font-semibold">🏢 Enterprise Ready</span>
                    </div>
                    {{-- Social Icons --}}
                    <div>
                        <p class="text-[9px] uppercase tracking-widest font-bold text-brand-gray mb-3">Follow Our Work</p>
                        <div class="flex items-center gap-2.5">
                            @foreach([
                                ['https://www.linkedin.com/in/di-web-2203b6362?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app','LinkedIn','M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
                                ['https://x.com/infodiwebs?s=21','X / Twitter','M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 5.88zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
                                ['https://www.facebook.com/share/17ntwMjFAT/?mibextid=wwXIfr','Facebook','M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
                                ['https://www.instagram.com/diwebs_agency?igsh=MXNpbnVwYTUxeGRubA%3D%3D&utm_source=qr','Instagram','M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z'],
                                ['https://github.com/diwebs','GitHub','M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12']
                            ] as [$url, $label, $path])
                                <a href="{{ $url }}" target="_blank" rel="noopener" title="{{ $label }}"
                                   class="h-8 w-8 rounded-lg bg-[#25282D] border border-white/5 flex items-center justify-center text-brand-gray hover:text-brand-cyan hover:border-brand-teal/40 hover:bg-brand-teal/10 transition-all duration-200">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $path }}"/></svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Newsletter --}}
                <div class="space-y-5">
                    <div>
                        <h3 class="text-sm font-bold text-brand-white uppercase tracking-wider mb-1">Stay Ahead With Diwebs</h3>
                        <p class="text-xs text-[#94A3B8]/65 leading-relaxed">Industry insights, AI updates, CBT news, and training opportunities — delivered to your inbox.</p>
                    </div>
                    <form @submit.prevent="subscribe()" class="space-y-3" novalidate>
                        <input type="email" required x-model="email" placeholder="Enter your corporate email"
                               class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/25 transition-all">
                        <button type="submit" :disabled="submitting"
                                class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-3 text-xs font-bold text-brand-dark-secondary hover:opacity-90 hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2">
                            <span x-show="!submitting">📬 Subscribe to Newsletter</span>
                            <span x-show="submitting" class="flex items-center gap-2" style="display:none;">
                                <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-brand-dark-secondary border-t-transparent"></span> Subscribing...
                            </span>
                        </button>
                        <div x-show="subscribeStatus === 'success'" x-transition class="flex items-start gap-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-3 py-2.5" style="display:none;">
                            <span class="text-emerald-400 text-sm">✔</span>
                            <span class="text-[11px] text-emerald-300 font-medium leading-relaxed" x-text="subscribeMessage"></span>
                        </div>
                        <div x-show="subscribeStatus === 'error'" x-transition class="flex items-start gap-2 rounded-xl bg-rose-500/10 border border-rose-500/20 px-3 py-2.5" style="display:none;">
                            <span class="text-rose-400 text-sm">⚠</span>
                            <span class="text-[11px] text-rose-300 font-medium leading-relaxed" x-text="subscribeMessage"></span>
                        </div>
                    </form>
                    <p class="text-[10px] text-[#94A3B8]/35 leading-relaxed">🔒 Zero spam. Unsubscribe anytime. We never share your data.</p>

                    {{-- Auth CTA --}}
                    <div class="pt-2 border-t border-white/5 space-y-2">
                        <p class="text-[9px] uppercase tracking-widest font-bold text-[#94A3B8]/40">Account Access</p>
                        <a href="{{ route('login') }}"
                           class="flex items-center gap-2.5 rounded-xl bg-brand-teal/10 border border-brand-teal/25 px-4 py-2.5 text-[11px] font-semibold text-brand-white hover:bg-brand-teal/20 hover:border-brand-cyan/40 transition-all">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-cyan animate-pulse flex-shrink-0"></span>
                            Sign In / Create Account
                        </a>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-[11px] font-extrabold text-brand-white uppercase tracking-widest mb-4">Quick Links</h3>
                        <ul class="space-y-2.5 text-xs">
                            <li><a href="{{ route('home') }}"      class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Home</a></li>
                            <li><a href="{{ route('about') }}"     class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>About Us</a></li>
                            <li><a href="{{ route('services') }}"  class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Services</a></li>
                            <li><a href="{{ route('solutions') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Solutions</a></li>
                            <li><a href="{{ route('portfolio') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Portfolio</a></li>
                            <li><a href="{{ route('contact') }}"   class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Contact</a></li>
                            <li><a href="{{ route('news.index') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Newsroom</a></li>
                            <li><a href="{{ route('careers') }}"    class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Careers</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Services --}}
                <div class="space-y-4">
                    <div>
                        <h3 class="text-[11px] font-extrabold text-brand-white uppercase tracking-widest mb-4">Our Services</h3>
                        <ul class="space-y-2.5 text-xs">
                            <li><a href="{{ route('services.detail', 'web-development') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Web Development</a></li>
                            <li><a href="{{ route('services.detail', 'mobile-apps') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Mobile Apps</a></li>
                            <li><a href="{{ route('services.detail', 'enterprise-saas') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Enterprise SaaS</a></li>
                            <li><a href="{{ route('services.detail', 'ai-automation') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>AI &amp; Automation</a></li>
                            <li><a href="{{ route('services.detail', 'cbt-infrastructure') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>CBT Infrastructure</a></li>
                            <li><a href="{{ route('services.detail', 'cloud-devops') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Cloud &amp; DevOps</a></li>
                            <li><a href="{{ route('services.detail', 'cybersecurity') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Cybersecurity</a></li>
                            <li><a href="{{ route('services.detail', 'workflow-automation') }}" class="flex items-center gap-2 text-[#94A3B8]/75 hover:text-brand-cyan hover:translate-x-1 transition-all duration-200"><span class="text-brand-teal/50 font-bold">›</span>Workflow Automation</a></li>
                        </ul>
                    </div>
                </div>

            </div>{{-- /ROW 1 --}}

            {{-- ══════════════════════════════════════════════════════
                 ROW 3 · Trust Badges & Platform Stats
            ══════════════════════════════════════════════════════ --}}
            <div class="flex flex-col lg:flex-row justify-between items-center gap-6 py-8 border-b border-white/5">
                <div class="flex flex-wrap justify-center lg:justify-start gap-2.5">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#25282D]/70 border border-white/5 px-3.5 py-2 text-[10px] text-[#94A3B8]/70 font-semibold">🔒 SSL 256-Bit Encrypted</span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#25282D]/70 border border-white/5 px-3.5 py-2 text-[10px] text-[#94A3B8]/70 font-semibold">🛡️ GDPR Compliant</span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#25282D]/70 border border-white/5 px-3.5 py-2 text-[10px] text-[#94A3B8]/70 font-semibold">⚡ 99.9% Uptime SLA</span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#25282D]/70 border border-white/5 px-3.5 py-2 text-[10px] text-[#94A3B8]/70 font-semibold">🌍 ISO 27001 Aligned</span>
                </div>
                <div class="flex items-center gap-6 sm:gap-10 text-center">
                    <div><span class="block text-lg font-extrabold text-brand-cyan">50+</span><span class="block text-[9px] uppercase tracking-widest text-[#94A3B8]/40 font-semibold mt-0.5">Projects Done</span></div>
                    <div class="h-7 w-px bg-white/5"></div>
                    <div><span class="block text-lg font-extrabold text-brand-cyan">2</span><span class="block text-[9px] uppercase tracking-widest text-[#94A3B8]/40 font-semibold mt-0.5">CBT Centers</span></div>
                    <div class="h-7 w-px bg-white/5"></div>
                    <div><span class="block text-lg font-extrabold text-brand-cyan">5</span><span class="block text-[9px] uppercase tracking-widest text-[#94A3B8]/40 font-semibold mt-0.5">Countries</span></div>
                </div>
            </div>{{-- /ROW 3 --}}


            {{-- ══════════════════════════════════════════════════════
                 ROW 4 · Bottom Legal Bar
            ══════════════════════════════════════════════════════ --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 py-7 text-[11px] text-[#94A3B8]/45">
                <div class="flex items-center gap-2">
                    <img src="/images/brand/diwebs-logo.svg?v=2" alt="" class="h-3.5 w-3.5 opacity-30" aria-hidden="true">
                    <span>© {{ date('Y') }} Diwebs Tech Agency. All rights reserved.</span>
                </div>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 justify-center">
                    <a href="{{ route('legal.show', 'privacy-policy') }}" class="hover:text-brand-cyan transition-colors">Privacy Policy</a>
                    <span class="text-white/10">·</span>
                    <a href="{{ route('legal.show', 'terms-of-service') }}" class="hover:text-brand-cyan transition-colors">Terms of Service</a>
                    <span class="text-white/10">·</span>
                    <a href="{{ route('legal.show', 'cookie-settings') }}" class="hover:text-brand-cyan transition-colors">Cookie Settings</a>
                    <span class="text-white/10">·</span>
                    <a href="{{ route('legal.show', 'platform-security') }}" class="hover:text-brand-cyan transition-colors">Platform Security</a>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[#94A3B8]/25">v1.0.0</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-[10px] text-emerald-400 border border-emerald-500/20 font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span> All Systems Operational
                    </span>
                </div>
            </div>{{-- /ROW 4 --}}

        </div>{{-- /container --}}


        {{-- ══════════════════════════════════════════════════════
             FLOATING UI — Back-to-Top & AI Assistant
        ══════════════════════════════════════════════════════ --}}

        {{-- Back-to-Top with circular SVG progress ring --}}
        <button x-show="scrollPercent > 10"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75"
                @click="scrollToTop()"
                class="fixed bottom-6 right-6 z-50 flex h-12 w-12 items-center justify-center rounded-full bg-[#25282D] border border-brand-teal/30 shadow-xl shadow-black/30 hover:scale-110 hover:border-brand-cyan/60 active:scale-95 transition-all focus:outline-none cursor-pointer"
                style="display:none;" title="Back to top" aria-label="Scroll to top">
            <svg class="absolute inset-0 h-full w-full -rotate-90" viewBox="0 0 48 48">
                <circle cx="24" cy="24" r="21" stroke="rgba(255,255,255,0.05)" stroke-width="2.5" fill="none"/>
                <circle cx="24" cy="24" r="21" stroke="#00C2D1" stroke-width="2.5" fill="none"
                        stroke-dasharray="132" :stroke-dashoffset="132 - (132 * scrollPercent / 100)" stroke-linecap="round"/>
            </svg>
            <svg class="h-4 w-4 text-brand-cyan relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
            </svg>
        </button>

        {{-- Floating AI Assistant --}}
        <div class="fixed bottom-6 left-6 z-50">
            <button @click="aiOpen = !aiOpen"
                    class="relative flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-brand-teal to-brand-cyan shadow-lg shadow-brand-teal/30 hover:scale-110 active:scale-95 transition-all cursor-pointer"
                    :class="aiOpen ? 'rotate-90' : ''"
                    title="Diwebs AI Assistant" aria-label="Open AI assistant">
                <span x-show="!aiOpen" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-cyan/20 opacity-75 pointer-events-none"></span>
                <svg x-show="!aiOpen" class="h-5 w-5 text-brand-dark-secondary relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                <svg x-show="aiOpen" class="h-5 w-5 text-brand-dark-secondary relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div x-show="aiOpen"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                 class="absolute bottom-16 left-0 w-[320px] sm:w-[360px] rounded-2xl bg-[#1E2125] border border-brand-teal/25 shadow-2xl overflow-hidden"
                 style="display:none;">
                {{-- Header --}}
                <div class="flex items-center gap-3 bg-[#25282D] px-4 py-3.5 border-b border-brand-teal/10">
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-brand-teal to-brand-cyan flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-brand-dark-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-brand-white">Diwebs AI Assistant</p>
                        <p class="text-[10px] text-emerald-400 flex items-center gap-1.5 mt-0.5"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Online — Ready to help</p>
                    </div>
                </div>
                {{-- Messages --}}
                <div x-ref="chatBox" class="p-4 h-[230px] overflow-y-auto space-y-3 text-xs flex flex-col">
                    <template x-for="(msg, i) in aiMessages" :key="i">
                        <div :class="msg.sender==='user' ? 'self-end bg-brand-cyan/15 border-brand-cyan/20 text-brand-white ml-6' : 'self-start bg-[#25282D] border-white/5 text-[#94A3B8] mr-6'"
                             class="rounded-2xl border px-3.5 py-2.5 leading-relaxed" x-text="msg.text"></div>
                    </template>
                    <div x-show="aiSending" class="self-start bg-[#25282D] rounded-2xl border border-white/5 px-3.5 py-2.5 flex items-center gap-1.5" style="display:none;">
                        <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-brand-cyan" style="animation-delay:0ms"></span>
                        <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-brand-cyan" style="animation-delay:160ms"></span>
                        <span class="h-1.5 w-1.5 animate-bounce rounded-full bg-brand-cyan" style="animation-delay:320ms"></span>
                    </div>
                </div>
                {{-- Input --}}
                <form @submit.prevent="sendAiMessage()" class="p-3 bg-[#25282D] border-t border-brand-teal/10 flex gap-2">
                    <input type="text" x-model="aiInput" placeholder="Ask about services, pricing, courses..."
                           class="flex-1 rounded-xl border border-brand-teal/20 bg-[#1E2125] px-3.5 py-2 text-xs text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none transition-colors">
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary px-3.5 py-2 text-xs font-bold hover:opacity-90 transition-opacity cursor-pointer flex-shrink-0">Send</button>
                </form>
            </div>
        </div>{{-- /AI Assistant --}}

    </footer>

    <!-- Service Worker and PWA Orchestration Script -->
    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('Diwebs Service Worker registered with scope: ', reg.scope))
                    .catch((err) => console.error('Diwebs Service Worker registration failed: ', err));
            });
        }

        // Global PWA and Installer Prompt State Controller
        function globalAppState() {
            return {
                mobileMenuOpen: false,
                showInstallPrompt: false,
                installPromptEvent: null,
                isIOS: false,
                isAndroid: false,
                isOffline: !navigator.onLine,
                isPwaStandalone: false,
                
                init() {
                    const ua = navigator.userAgent.toLowerCase();
                    this.isIOS = /iphone|ipad|ipod/.test(ua);
                    this.isAndroid = /android/.test(ua);
                    this.isPwaStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

                    // Network status triggers
                    window.addEventListener('online', () => { this.isOffline = false; });
                    window.addEventListener('offline', () => { this.isOffline = true; });

                    // Chrome/Android installer hook
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.installPromptEvent = e;
                        if (!this.isPwaStandalone && !localStorage.getItem('pwa_prompt_dismissed')) {
                            setTimeout(() => {
                                this.showInstallPrompt = true;
                            }, 20000); // 20 seconds smart load display
                        }
                    });

                    // iOS Safari manual installer guide display
                    if (this.isIOS && !this.isPwaStandalone && !localStorage.getItem('pwa_prompt_dismissed')) {
                        setTimeout(() => {
                            this.showInstallPrompt = true;
                        }, 20000);
                    }

                    // Swipe Gestures Event Binding for mobile menu drawer
                    window.addEventListener('swipe-right', () => {
                        if (this.mobileMenuOpen) {
                            this.mobileMenuOpen = false;
                        }
                    });
                    window.addEventListener('swipe-left', () => {
                        if (!this.mobileMenuOpen && !window.location.pathname.includes('/cbt/session/')) {
                            // Only trigger drawer open if not in an active CBT exam interface session
                            this.mobileMenuOpen = true;
                        }
                    });
                },

                async triggerInstall() {
                    if (this.isAndroid && this.installPromptEvent) {
                        this.showInstallPrompt = false;
                        this.installPromptEvent.prompt();
                        await this.installPromptEvent.userChoice;
                        this.installPromptEvent = null;
                    } else if (this.isIOS) {
                        this.showInstallPrompt = false;
                    }
                },

                dismissInstallPrompt() {
                    this.showInstallPrompt = false;
                    localStorage.setItem('pwa_prompt_dismissed', 'true');
                }
            };
        }
    </script>
</body>
</html>
