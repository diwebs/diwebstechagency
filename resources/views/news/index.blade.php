@extends('layouts.app')

@section('title', 'Diwebs News - Technology, AI, CBT and Enterprise Software Insights')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    
    <!-- Hero / Headline Section -->
    <div class="mb-12 text-center md:text-left">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-teal/15 px-3 py-1 text-xs font-semibold text-brand-cyan border border-brand-teal/20 mb-4">
            <span class="h-1.5 w-1.5 rounded-full bg-brand-cyan animate-pulse"></span>
            DIWEBS KNOWLEDGE &amp; NEWSROOM
        </span>
        <h1 class="text-3xl font-extrabold tracking-tight text-brand-white sm:text-5xl text-glow">
            Ecosystem <span class="bg-gradient-to-r from-brand-teal to-brand-cyan bg-clip-text text-transparent">Insights &amp; Updates</span>
        </h1>
        <p class="mt-4 text-base text-brand-gray max-w-2xl">
            Explore original research, enterprise CBT announcements, AI prompt guides, and technical tutorials from the Diwebs engineering division.
        </p>
    </div>

    <!-- Toolbar: Search and Category Filtering -->
    <div class="glass-card rounded-2xl p-4 mb-10 border border-brand-teal/15 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Categories list -->
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <a href="{{ route('news.index') }}" 
               class="px-4 py-2 text-xs font-bold rounded-lg border transition-all duration-300 {{ !request('category') ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/15 hover:text-brand-cyan hover:border-brand-cyan/45' }}">
                All Categories
            </a>
            @foreach($categories as $category)
                <a href="{{ route('news.index', ['category' => $category, 'search' => request('search')]) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-lg border transition-all duration-300 {{ request('category') === $category ? 'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-md' : 'bg-brand-dark-secondary/60 text-brand-gray border-brand-teal/15 hover:text-brand-cyan hover:border-brand-cyan/45' }}">
                    {{ $category }}
                </a>
            @endforeach
        </div>

        <!-- Search box -->
        <form action="{{ route('news.index') }}" method="GET" class="w-full md:w-80 flex gap-2">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative w-full">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search insights..." 
                       class="w-full rounded-lg bg-brand-dark-secondary/80 border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/60 focus:border-brand-cyan/60 focus:outline-none transition-all">
                @if(request('search'))
                    <a href="{{ route('news.index', ['category' => request('category')]) }}" class="absolute right-3 top-3.5 text-brand-gray hover:text-brand-cyan text-xs">Clear</a>
                @endif
            </div>
            <button type="submit" class="rounded-lg bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 hover:border-brand-cyan/40 px-4 py-2.5 text-xs font-bold text-brand-cyan transition-all">
                Search
            </button>
        </form>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Articles Feed list -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Featured Post Panel -->
            @if($featured && !request('category') && !request('search') && $articles->currentPage() === 1)
                <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/20 relative group hover:border-brand-cyan/40 transition-all duration-500">
                    <div class="absolute top-0 right-0 bg-brand-cyan text-brand-dark-secondary text-[10px] uppercase font-bold tracking-wider px-3.5 py-1.5 rounded-bl-xl shadow-md z-10">
                        Featured Publication
                    </div>
                    
                    @if($featured->featured_image)
                        <div class="h-64 sm:h-80 w-full overflow-hidden relative">
                            <img src="{{ $featured->featured_image }}" alt="{{ $featured->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-dark-secondary via-brand-dark-secondary/40 to-transparent"></div>
                        </div>
                    @endif

                    <div class="p-6 sm:p-8 relative {{ !$featured->featured_image ? 'bg-gradient-to-br from-brand-dark-secondary via-brand-dark-secondary to-brand-teal/5' : '' }}">
                        <div class="flex items-center gap-3 text-xs text-brand-gray mb-3.5">
                            <span class="text-brand-cyan font-bold uppercase tracking-wider">{{ $featured->category }}</span>
                            <span class="text-brand-gray/40">•</span>
                            <span>{{ $featured->published_at ? $featured->published_at->format('M d, Y') : $featured->created_at->format('M d, Y') }}</span>
                            <span class="text-brand-gray/40">•</span>
                            <span>⏱️ {{ $featured->read_time_minutes }} min read</span>
                        </div>
                        
                        <h2 class="text-xl sm:text-2xl font-bold text-brand-white group-hover:text-brand-cyan transition-colors line-clamp-2">
                            <a href="{{ route('news.show', $featured->slug) }}">{{ $featured->title }}</a>
                        </h2>
                        
                        <p class="mt-3.5 text-xs sm:text-sm text-brand-gray leading-relaxed line-clamp-3">
                            {{ $featured->excerpt }}
                        </p>

                        <div class="mt-6 flex items-center justify-between border-t border-brand-teal/10 pt-5">
                            <div class="flex items-center gap-2.5">
                                @if($featured->author_avatar)
                                    <img src="{{ $featured->author_avatar }}" alt="{{ $featured->author_name }}" class="h-8 w-8 rounded-full border border-brand-teal/20 object-cover">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-brand-teal/20 flex items-center justify-center text-[10px] font-bold text-brand-cyan border border-brand-teal/20">D</div>
                                @endif
                                <div>
                                    <span class="block text-xs font-semibold text-brand-white">{{ $featured->author_name }}</span>
                                    <span class="block text-[10px] text-brand-gray">Diwebs Staff</span>
                                </div>
                            </div>

                            <a href="{{ route('news.show', $featured->slug) }}" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-4 py-2 text-xs font-bold hover:opacity-90 transition-all flex items-center gap-1">
                                Read Article <span class="text-[10px]">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Regular Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($articles as $article)
                    <!-- Skip the featured post in regular list on page 1 if no filters applied -->
                    @if($featured && !request('category') && !request('search') && $articles->currentPage() === 1 && $article->id === $featured->id)
                        @continue
                    @endif

                    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15 hover:border-brand-cyan/30 flex flex-col justify-between group hover:translate-y-[-2px] transition-all duration-300">
                        <div>
                            @if($article->featured_image)
                                <div class="h-44 w-full overflow-hidden relative">
                                    <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark-secondary/90 via-transparent to-transparent"></div>
                                </div>
                            @endif

                            <div class="p-5">
                                <div class="flex items-center gap-2 text-[10px] text-brand-gray mb-2.5">
                                    <span class="text-brand-cyan font-bold uppercase tracking-wider">{{ $article->category }}</span>
                                    <span>•</span>
                                    <span>{{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}</span>
                                </div>
                                <h3 class="text-sm sm:text-base font-bold text-brand-white group-hover:text-brand-cyan transition-colors line-clamp-2">
                                    <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                                </h3>
                                <p class="mt-2.5 text-xs text-brand-gray/80 leading-relaxed line-clamp-3">
                                    {{ $article->excerpt }}
                                </p>
                            </div>
                        </div>

                        <div class="px-5 pb-5 pt-3 border-t border-brand-teal/10 flex items-center justify-between text-[11px] text-brand-gray">
                            <span class="flex items-center gap-1">⏱️ {{ $article->read_time_minutes }} min read</span>
                            <span class="flex items-center gap-1">👁️ {{ number_format($article->view_count) }} views</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full glass-card rounded-2xl p-12 text-center border border-brand-teal/15">
                        <span class="text-3xl">🔎</span>
                        <h4 class="mt-3.5 text-base font-bold text-brand-white">No articles matched your criteria</h4>
                        <p class="text-xs text-brand-gray mt-1">Try resetting your search query or choosing another category category.</p>
                        <a href="{{ route('news.index') }}" class="mt-5 inline-flex items-center gap-1 rounded-lg bg-brand-cyan/20 px-4 py-2 text-xs font-bold text-brand-cyan border border-brand-teal/30 hover:bg-brand-teal/30 transition-all">
                            Reset Filters
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            <div class="pt-6">
                {{ $articles->appends(request()->query())->links() }}
            </div>

        </div>

        <!-- Sidebar (Trending, Newsletters, etc) -->
        <div class="space-y-8">
            
            <!-- Trending Articles -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/15 pb-3 flex items-center gap-2">
                    <span>🔥</span> Trending Publications
                </h3>
                <div class="mt-5 space-y-4">
                    @forelse($trending as $idx => $trend)
                        <div class="flex gap-3 text-xs py-1 border-b border-brand-teal/5 last:border-0 last:pb-0">
                            <span class="font-mono font-bold text-base text-brand-cyan/40 w-5 flex-shrink-0">0{{ $idx + 1 }}</span>
                            <div class="flex-1">
                                <h4 class="font-bold text-brand-white hover:text-brand-cyan transition-all line-clamp-2">
                                    <a href="{{ route('news.show', $trend->slug) }}">{{ $trend->title }}</a>
                                </h4>
                                <div class="flex items-center gap-2 text-[10px] text-brand-gray mt-1">
                                    <span class="uppercase font-semibold">{{ $trend->category }}</span>
                                    <span>•</span>
                                    <span>👁️ {{ number_format($trend->view_count) }} views</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-brand-gray">No trending publications registered yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Custom Premium Advertising Card -->
            <div class="glass-card rounded-2xl p-6 border border-brand-cyan/25 relative overflow-hidden bg-gradient-to-br from-brand-dark-secondary via-brand-dark-secondary to-brand-teal/15">
                <div class="absolute -right-10 -bottom-10 h-28 w-28 rounded-full bg-brand-cyan/10 blur-xl"></div>
                <span class="inline-flex rounded bg-brand-cyan/20 px-2 py-0.5 text-[9px] uppercase font-bold tracking-wider text-brand-cyan mb-3">Enterprise LMS</span>
                <h4 class="text-base font-bold text-brand-white leading-snug">Boost Your Skills with Diwebs Academy</h4>
                <p class="text-xs text-brand-gray mt-2 leading-relaxed">Join live bootcamps and learn Full-Stack development, AI prompting architecture, and Cybersecurity from industry leaders.</p>
                <a href="{{ route('academy.dashboard') }}" class="mt-5 w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary text-center block transition-all shadow hover:opacity-90">
                    Get Free Enrollment
                </a>
            </div>

            <!-- Email Newsletter Card -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan flex items-center gap-2">
                    <span>✉️</span> Newsroom Newsletter
                </h3>
                <p class="text-xs text-brand-gray mt-2 leading-relaxed">Receive developer updates, security advisories, and ecosystem logs directly in your inbox.</p>
                <form action="#" method="POST" class="mt-4 space-y-2">
                    @csrf
                    <input type="email" placeholder="name@company.com" required class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2.5 text-xs text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan/60 focus:outline-none transition-all">
                    <button type="submit" class="w-full rounded-lg bg-brand-teal/20 border border-brand-teal/35 hover:bg-brand-teal/30 text-brand-cyan text-xs font-bold py-2.5 transition-all">
                        Subscribe Now
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
