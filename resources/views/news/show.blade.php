@extends('layouts.app')

@section('title', $article->meta_title ?? ($article->title . ' - Diwebs Insights'))

@section('meta')
    <meta name="description" content="{{ $article->meta_description ?? $article->excerpt }}">
    <meta name="keywords" content="{{ $article->meta_keywords ?? 'diwebs, technology, ' . $article->category }}">
@endsection

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    
    <!-- Navigation back -->
    <div class="mb-6">
        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-cyan hover:underline">
            <span>←</span> Back to newsroom
        </a>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Article Body -->
        <div class="lg:col-span-2 space-y-6">
            
            <article class="glass-card rounded-3xl overflow-hidden border border-brand-teal/20 p-6 sm:p-8 relative">
                <!-- Decorative Top Border Glow -->
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-brand-teal to-brand-cyan"></div>
                
                <!-- Category and Read Time Info -->
                <div class="flex flex-wrap items-center gap-3 text-xs text-brand-gray mb-4">
                    <span class="px-2.5 py-0.5 rounded-full bg-brand-cyan/15 text-brand-cyan font-bold uppercase tracking-wider text-[10px] border border-brand-cyan/20">
                        {{ $article->category }}
                    </span>
                    <span>•</span>
                    <span>{{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}</span>
                    <span>•</span>
                    <span>⏱️ {{ $article->read_time_minutes }} min read</span>
                    <span>•</span>
                    <span>👁️ {{ number_format($article->view_count) }} views</span>
                </div>

                <!-- Title -->
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-brand-white leading-tight">
                    {{ $article->title }}
                </h1>

                <!-- Author block -->
                <div class="flex items-center gap-3 border-y border-brand-teal/10 py-4 my-6">
                    @if($article->author_avatar)
                        <img src="{{ $article->author_avatar }}" alt="{{ $article->author_name }}" class="h-10 w-10 rounded-full border border-brand-teal/20 object-cover">
                    @else
                        <div class="h-10 w-10 rounded-full bg-brand-teal/20 flex items-center justify-center text-xs font-bold text-brand-cyan border border-brand-teal/20">D</div>
                    @endif
                    <div>
                        <span class="block text-sm font-bold text-brand-white">{{ $article->author_name }}</span>
                        <span class="block text-[11px] text-brand-gray">Published in Diwebs Insights</span>
                    </div>
                </div>

                <!-- Featured Image -->
                @if($article->featured_image)
                    <div class="rounded-2xl overflow-hidden mb-8 border border-brand-teal/15 max-h-[400px]">
                        <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Article Content -->
                <div class="prose prose-invert prose-cyan max-w-none text-brand-gray/95 text-sm sm:text-base leading-relaxed space-y-5">
                    {!! nl2br(e($article->content)) !!}
                </div>

                <!-- Share Trigger Block -->
                <div class="border-t border-brand-teal/10 pt-6 mt-8 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="text-xs text-brand-gray">
                        Enjoyed this publication? Share with your network.
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.open('https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(request()->url()) }}', '_blank')" 
                                class="rounded-lg bg-brand-dark-secondary/80 border border-brand-teal/15 hover:border-brand-cyan/40 px-3.5 py-2 text-xs font-semibold text-brand-white transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>𝕏</span> Share
                        </button>
                        <button onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}', '_blank')" 
                                class="rounded-lg bg-brand-dark-secondary/80 border border-brand-teal/15 hover:border-brand-cyan/40 px-3.5 py-2 text-xs font-semibold text-brand-white transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>💼</span> LinkedIn
                        </button>
                        <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied to clipboard!');" 
                                class="rounded-lg bg-brand-dark-secondary/80 border border-brand-teal/15 hover:border-brand-cyan/40 px-3.5 py-2 text-xs font-semibold text-brand-cyan transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>🔗</span> Copy Link
                        </button>
                    </div>
                </div>
            </article>

            <!-- Author Bio Card -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex items-start gap-4">
                @if($article->author_avatar)
                    <img src="{{ $article->author_avatar }}" alt="{{ $article->author_name }}" class="h-12 w-12 rounded-full border border-brand-teal/20 object-cover flex-shrink-0">
                @else
                    <div class="h-12 w-12 rounded-full bg-brand-teal/20 flex items-center justify-center text-xs font-bold text-brand-cyan border border-brand-teal/20 flex-shrink-0">D</div>
                @endif
                <div>
                    <h4 class="text-sm font-bold text-brand-white">About the Author: {{ $article->author_name }}</h4>
                    <p class="text-xs text-brand-gray/80 mt-1.5 leading-relaxed">
                        {{ $article->author_bio ?? 'Diwebs technical analyst specializing in enterprise web architecture, CBT center logistics, high-performance computing, and cybersecurity audits.' }}
                    </p>
                </div>
            </div>

        </div>

        <!-- Sidebar (Related articles, CTA) -->
        <div class="space-y-6">
            
            <!-- Related Articles -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/15 pb-3">
                    Related Publications
                </h3>
                <div class="mt-4 space-y-4">
                    @forelse($related as $rel)
                        <div class="py-2 border-b border-brand-teal/5 last:border-0 last:pb-0">
                            <span class="block text-[9px] uppercase font-bold text-brand-cyan mb-1">{{ $rel->category }}</span>
                            <h4 class="font-bold text-xs text-brand-white hover:text-brand-cyan transition-all line-clamp-2">
                                <a href="{{ route('news.show', $rel->slug) }}">{{ $rel->title }}</a>
                            </h4>
                            <span class="block text-[10px] text-brand-gray mt-1">⏱️ {{ $rel->read_time_minutes }} min read</span>
                        </div>
                    @empty
                        <p class="text-xs text-brand-gray">No related publications found in this category.</p>
                    @endforelse
                </div>
            </div>

            <!-- LMS Promo -->
            <div class="glass-card rounded-2xl p-6 border border-brand-cyan/25 bg-gradient-to-br from-brand-dark-secondary to-brand-teal/10">
                <h4 class="text-sm font-bold text-brand-white">Need Customized Software?</h4>
                <p class="text-xs text-brand-gray mt-2 leading-relaxed">
                    Our team of software engineering experts, AI researchers, and DevOps engineers are ready to build your next-gen tech product.
                </p>
                <a href="{{ route('contact') }}" class="mt-4 w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary text-center block transition-all shadow hover:opacity-90">
                    Consult our team
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
