<!DOCTYPE html>
<html lang="en" class="h-full bg-brand-dark-secondary text-brand-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Diwebs Tech Agency - Digital Ecosystem')</title>
    
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased selection:bg-brand-cyan selection:text-brand-dark-secondary relative overflow-x-hidden">

    <!-- Tech Grid and Neon Blur Background Spheres -->
    <div class="fixed inset-0 tech-grid -z-10 opacity-70 pointer-events-none"></div>
    <div class="fixed top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-brand-teal/10 blur-[120px] -z-10 animate-pulse-slow pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-brand-cyan/10 blur-[120px] -z-10 animate-pulse-slow pointer-events-none"></div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 w-full border-b border-brand-teal/10 bg-brand-dark-secondary/70 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="h-8 w-8 rounded-lg bg-gradient-to-tr from-brand-teal to-brand-cyan flex items-center justify-center font-bold text-brand-dark-secondary text-lg">D</span>
                        <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">Diwebs <span class="text-brand-cyan">Tech</span></span>
                    </a>
                </div>

                <!-- Main Nav Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-brand-gray">
                    <a href="{{ route('home') }}" class="hover:text-brand-cyan transition-colors">Home</a>
                    <a href="{{ route('about') }}" class="hover:text-brand-cyan transition-colors">About</a>
                    <a href="{{ route('services') }}" class="hover:text-brand-cyan transition-colors">Services</a>
                    <a href="{{ route('solutions') }}" class="hover:text-brand-cyan transition-colors">Solutions</a>
                    <a href="{{ route('portfolio') }}" class="hover:text-brand-cyan transition-colors">Portfolio</a>
                    <a href="{{ route('academy.dashboard') }}" class="hover:text-brand-cyan transition-colors flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-brand-cyan animate-pulse"></span>Academy</a>
                    <a href="{{ route('cbt.dashboard') }}" class="hover:text-brand-cyan transition-colors">CBT Portal</a>
                </nav>

                <!-- Auth Operations -->
                <div class="flex items-center gap-4">
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

    <!-- Main View Slot -->
    <main class="py-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-brand-teal/10 bg-brand-dark-secondary/50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Info block -->
                <div class="md:col-span-2">
                    <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent flex items-center gap-2">
                        <span class="h-6 w-6 rounded bg-brand-teal flex items-center justify-center text-brand-dark-secondary text-xs">D</span> Diwebs Tech
                    </span>
                    <p class="mt-4 text-sm text-brand-gray max-w-sm">
                        Building premium digital infrastructure, AI solutions, CBT systems, and enterprise business automation software for global impact.
                    </p>
                    <p class="mt-6 text-xs text-brand-gray">
                        © 2026 Diwebs Tech Agency. All rights reserved.
                    </p>
                </div>
                <!-- Links block -->
                <div>
                    <h3 class="text-sm font-semibold text-brand-white tracking-wider uppercase">Solutions</h3>
                    <ul class="mt-4 space-y-2 text-sm text-brand-gray">
                        <li><a href="#" class="hover:text-brand-cyan">Enterprise SaaS</a></li>
                        <li><a href="#" class="hover:text-brand-cyan">AI Agents & Automations</a></li>
                        <li><a href="#" class="hover:text-brand-cyan">National CBT Infrastructure</a></li>
                        <li><a href="#" class="hover:text-brand-cyan">Cloud Solutions</a></li>
                    </ul>
                </div>
                <!-- Dev Login portal -->
                <div>
                    <h3 class="text-sm font-semibold text-brand-white tracking-wider uppercase">Dev Quick Logins</h3>
                    <div class="mt-4 flex flex-col gap-2">
                        <a href="{{ route('auth.dev-login', 'super_admin') }}" class="text-xs text-brand-cyan hover:underline flex items-center gap-1">⚡ Super Admin Login</a>
                        <a href="{{ route('auth.dev-login', 'client') }}" class="text-xs text-brand-cyan hover:underline flex items-center gap-1">⚡ Client Login</a>
                        <a href="{{ route('auth.dev-login', 'student') }}" class="text-xs text-brand-cyan hover:underline flex items-center gap-1">⚡ Student Login</a>
                        <a href="{{ route('auth.dev-login', 'candidate') }}" class="text-xs text-brand-cyan hover:underline flex items-center gap-1">⚡ Candidate Login</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
