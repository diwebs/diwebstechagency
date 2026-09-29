@extends('layouts.app')

@section('title', 'Diwebs Tech - Custom Product Solutions & Regional Plans')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8 py-6">
    <div class="mx-auto max-w-4xl text-center mb-16">
        <span class="inline-flex items-center gap-2 rounded-full bg-brand-teal/10 border border-brand-teal/20 px-4 py-1.5 text-xs sm:text-sm text-brand-cyan font-semibold uppercase tracking-wider mb-4">
            Enterprise Solutions Architecture
        </span>
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl lg:text-6xl text-glow bg-gradient-to-r from-brand-white to-brand-gray bg-clip-text text-transparent">
            Custom Software &amp; Regional Pricing
        </h1>
        <p class="mt-4 text-base sm:text-lg text-brand-gray leading-relaxed">
            Turnkey technology software suites custom built to address operational bottlenecks with localized multi-currency billing.
        </p>
    </div>

    <!-- Product Solutions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
        <!-- Solution 1 -->
        <div class="glass-card rounded-3xl p-8 md:p-10 border-l-4 border-l-brand-cyan hover:border-brand-cyan transition-all duration-300">
            <div class="h-12 w-12 rounded-2xl bg-brand-cyan/15 border border-brand-cyan/30 flex items-center justify-center text-brand-cyan mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147L12 14.6l7.74-4.453a.75.75 0 000-1.294L12 4.4 4.26 8.853a.75.75 0 000 1.294zM12 14.6v5.4M6.75 11.6v3.9c0 1.65 2.35 3 5.25 3s5.25-1.35 5.25-3v-3.9"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-brand-white mb-3">Diwebs Academy (LMS)</h3>
            <p class="text-sm sm:text-base text-brand-gray leading-relaxed">
                A modern learning management platform complete with video playlists, course tracking progress, automated grading quizzes, and verified PDF certificates.
            </p>
        </div>
        <!-- Solution 2 -->
        <div class="glass-card rounded-3xl p-8 md:p-10 border-l-4 border-l-brand-teal hover:border-brand-teal transition-all duration-300">
            <div class="h-12 w-12 rounded-2xl bg-brand-teal/15 border border-brand-teal/30 flex items-center justify-center text-brand-teal mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-brand-white mb-3">Diwebs CBT Platform</h3>
            <p class="text-sm sm:text-base text-brand-gray leading-relaxed">
                A robust examination system incorporating candidate verification protocols, webcam surveillance checks, tab locks, and database analytics for high-volume exam environments.
            </p>
        </div>
    </div>

    <!-- Regional Payment Plans Matrix -->
    @php
        $userCountry = auth()->check() ? auth()->user()->country : request('region', 'Nigeria');
        $regionalPlansData = \App\Helpers\PaymentHelper::getRegionalPlans($userCountry);
        $activeRegionInfo = $regionalPlansData['region_info'];
        $plans = $regionalPlansData['plans'];
    @endphp

    <div class="glass-card rounded-3xl p-8 md:p-12 mb-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-dot-matrix opacity-20"></div>

        <div class="relative z-10 text-center max-w-3xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-brand-teal/10 border border-brand-teal/20 px-4 py-1.5 text-xs sm:text-sm text-brand-cyan font-semibold mb-4 uppercase tracking-wider">
                <svg class="w-4 h-4 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V7a2 2 0 00-2-2h-2c-.5 0-1-.2-1.4-.6L12 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Multi-Currency Regional Billing
            </div>
            <h2 class="text-3xl font-bold text-brand-white sm:text-4xl">Flexible Regional Subscription Plans</h2>
            <p class="text-sm sm:text-base text-brand-gray mt-3">Prices automatically adjust according to your country and region with localized payment gateways.</p>
            
            <!-- Region Selector Tabs -->
            <div class="mt-8 flex flex-wrap justify-center gap-2.5">
                <a href="{{ route('solutions', ['region' => 'South Africa']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['africa_south']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇿🇦</span> <span class="font-extrabold">SOUTH AFRICA</span> (ZAR R)
                </a>
                <a href="{{ route('solutions', ['region' => 'Nigeria']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['africa_west']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇳🇬</span> <span class="font-extrabold">NIGERIA</span> (NGN ₦)
                </a>
                <a href="{{ route('solutions', ['region' => 'Kenya']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['africa_east']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇰🇪</span> <span class="font-extrabold">KENYA</span> (KES KSh)
                </a>
                <a href="{{ route('solutions', ['region' => 'Ghana']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['africa_ghana']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇬🇭</span> <span class="font-extrabold">GHANA</span> (GHS GH₵)
                </a>
                <a href="{{ route('solutions', ['region' => 'Egypt']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['africa_north']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇪🇬</span> <span class="font-extrabold">EGYPT</span> (EGP E£)
                </a>
                <a href="{{ route('solutions', ['region' => 'United States']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['global']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇺🇸</span> <span class="font-extrabold">GLOBAL / US</span> (USD $)
                </a>
                <a href="{{ route('solutions', ['region' => 'United Kingdom']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['uk']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇬🇧</span> <span class="font-extrabold">UK</span> (GBP £)
                </a>
                <a href="{{ route('solutions', ['region' => 'Germany']) }}" 
                   class="rounded-xl px-4 py-2 text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 {{ in_array($activeRegionInfo['region'], ['europe']) ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/20 hover:text-brand-white' }}">
                    <span>🇪🇺</span> <span class="font-extrabold">EUROPE</span> (EUR €)
                </a>
            </div>
        </div>

        <!-- Plans Cards Grid -->
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $key => $plan)
                <div class="glass-card rounded-3xl p-8 border flex flex-col justify-between relative transition-all duration-300 {{ $key === 'professional' ? 'border-brand-cyan bg-brand-teal/10 shadow-[0_0_20px_rgba(0,194,209,0.15)] scale-[1.02]' : 'border-brand-teal/15 hover:border-brand-teal/30' }}">
                    @if($key === 'professional')
                        <div class="absolute -top-3.5 right-6 bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-extrabold text-xs uppercase px-4 py-1 rounded-full tracking-wider shadow">
                            Most Popular Choice
                        </div>
                    @endif

                    <div>
                        <h4 class="text-xl font-bold text-brand-white mb-2">{{ $plan['name'] }}</h4>
                        <p class="text-xs sm:text-sm text-brand-gray min-h-[44px] leading-relaxed">{{ $plan['description'] }}</p>
                        
                        <div class="my-6 border-y border-brand-teal/10 py-5">
                            <div class="text-4xl font-extrabold text-brand-cyan tracking-tight">
                                {{ $plan['price_formatted'] }}
                            </div>
                            <div class="text-xs text-brand-gray uppercase tracking-wider font-semibold mt-1.5">
                                {{ $plan['billing_period'] }} ({{ $activeRegionInfo['currency'] }})
                            </div>
                        </div>

                        <ul class="space-y-3 mb-8 text-xs sm:text-sm text-brand-gray">
                            @foreach($plan['features'] as $feat)
                                <li class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-brand-cyan flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-brand-white font-medium">{{ $feat }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="{{ route('register') }}?ref=client&country={{ urlencode($userCountry) }}" 
                       class="w-full text-center rounded-xl py-3 text-xs sm:text-sm font-bold transition-all cursor-pointer flex items-center justify-center gap-2 {{ $key === 'professional' ? 'bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary shadow-md hover:opacity-90' : 'bg-brand-dark-secondary border border-brand-teal/20 text-brand-white hover:bg-brand-teal/20' }}">
                        <span>Select {{ $plan['name'] }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
