@extends('layouts.app')

@section('title', 'Diwebs Tech - Dynamic Project Portfolio')

@section('content')
<div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center mb-16 animate-fade-in">
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow">
            Our Portfolio
        </h1>
        <p class="mt-4 text-base text-brand-gray">
            Explore the bespoke, state-of-the-art software systems, web applications, and corporate platforms we have designed, built, and deployed.
        </p>
    </div>

    @if($portfolios->isEmpty())
        <div class="glass-card rounded-2xl p-16 text-center border border-brand-teal/20 max-w-xl mx-auto">
            <div class="text-5xl mb-4 text-brand-cyan animate-pulse">📂</div>
            <h3 class="text-lg font-bold text-brand-white">Portfolio is being updated</h3>
            <p class="mt-2 text-sm text-brand-gray leading-relaxed">Our engineering teams are currently packaging our latest software deployments. Check back shortly to see our recent work!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            @foreach($portfolios as $portfolio)
                <div class="glass-card rounded-2xl overflow-hidden flex flex-col group border border-brand-teal/10 hover:border-brand-cyan/40 hover:shadow-2xl hover:shadow-brand-teal/5 transition-all duration-300 transform hover:-translate-y-1">
                    
                    <!-- 1. Mock Images of the Website (COMES FIRST) -->
                    <div class="relative w-full bg-[#181a1e] border-b border-brand-teal/10 flex flex-col">
                        <!-- Browser Bar Controls -->
                        <div class="flex items-center gap-1.5 px-4 py-3 bg-[#131518]/90">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-500/80"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500/80"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/80"></span>
                            <!-- Mock address bar showing domain -->
                            <div class="ml-4 flex-1 max-w-[60%] bg-[#1a1d21] rounded-md px-3 py-0.5 text-[10px] text-brand-gray font-mono truncate">
                                @if($portfolio->project_url)
                                    {{ parse_url($portfolio->project_url, PHP_URL_HOST) ?? $portfolio->project_url }}
                                @else
                                    diwebstechagency.website
                                @endif
                            </div>
                        </div>

                        <!-- Image Section -->
                        <div class="relative aspect-video w-full overflow-hidden bg-brand-dark-secondary flex items-center justify-center">
                            @if($portfolio->mock_image)
                                <img src="{{ asset('storage/' . $portfolio->mock_image) }}" 
                                     alt="{{ $portfolio->title }}" 
                                     class="w-full h-full object-cover object-top group-hover:scale-[1.02] transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-brand-teal/5 to-brand-cyan/5 text-brand-gray p-6">
                                    <span class="text-4xl mb-2">🌐</span>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-brand-cyan">Deployment Showcase</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Write-up and Description (COMES SECOND) -->
                    <div class="p-8 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-brand-white group-hover:text-brand-cyan transition-colors duration-300">
                                {{ $portfolio->title }}
                            </h3>
                            <div class="mt-4 text-xs text-[#94A3B8] leading-relaxed whitespace-pre-line">
                                {!! e($portfolio->description) !!}
                            </div>
                        </div>

                        <!-- 3. Link to the Project (COMES THIRD) -->
                        @if($portfolio->project_url)
                            <div class="mt-6 pt-6 border-t border-brand-teal/10">
                                <a href="{{ $portfolio->project_url }}" 
                                   target="_blank" 
                                   rel="noopener" 
                                   class="inline-flex items-center gap-2 rounded-xl bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 hover:border-brand-cyan/50 px-5 py-2.5 text-xs font-bold text-brand-cyan transition-all duration-200">
                                    <span>🔗</span> Explore Live Project <span class="group-hover:translate-x-1 transition-transform">→</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
