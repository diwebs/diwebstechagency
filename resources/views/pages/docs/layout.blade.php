@extends('layouts.app')

@section('title', 'Documentation - Diwebs Tech')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" x-data="{
    searchQuery: '',
    mobileSidebarOpen: false,
    sections: [
        @foreach($sections as $slug => $sec)
        { slug: '{{ $slug }}', title: '{{ $sec['title'] }}', icon: '{{ $sec['icon'] }}' },
        @endforeach
    ],
    get filteredSections() {
        if (!this.searchQuery) return this.sections;
        return this.sections.filter(s => s.title.toLowerCase().includes(this.searchQuery.toLowerCase()));
    }
}">
    <!-- Top breadcrumb and Mobile Toggle Bar -->
    <div class="flex items-center justify-between border-b border-brand-teal/15 pb-4 mb-6 md:hidden">
        <div class="flex items-center gap-2 text-xs font-semibold text-brand-cyan">
            <span>📚 Docs</span>
            <span class="text-[#94A3B8]/30">/</span>
            <span class="text-brand-white truncate max-w-[200px]">{{ $currentSection['title'] }}</span>
        </div>
        <button type="button" 
                @click="mobileSidebarOpen = !mobileSidebarOpen" 
                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-teal/10 border border-brand-teal/20 px-3.5 py-2 text-xs font-bold text-brand-cyan hover:bg-brand-teal/20 transition-all select-none">
            <span>📖 Table of Contents</span>
        </button>
    </div>

    <div class="flex flex-col md:flex-row gap-8 relative items-start">
        
        <!-- 1. Sidebar Navigation (Left) -->
        <aside class="w-full md:w-64 flex-shrink-0 sticky top-20 z-10 hidden md:block">
            <div class="glass-card rounded-2xl border border-brand-teal/15 p-5 space-y-6">
                <div>
                    <h3 class="text-xs font-extrabold text-brand-cyan uppercase tracking-widest mb-1.5">Documentation</h3>
                    <p class="text-[10px] text-[#94A3B8]/50 leading-normal">Guides, architectures, and engineering principles.</p>
                </div>

                <!-- Live Client Search -->
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Filter articles..." 
                           class="w-full rounded-xl border border-brand-teal/15 bg-brand-dark-secondary/60 px-3.5 py-2.5 text-xs text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/20 transition-all">
                    <span class="absolute right-3.5 top-3 text-[10px] opacity-35">🔍</span>
                </div>

                <!-- Sidebar Nav Tree -->
                <nav class="space-y-1.5">
                    <template x-for="item in filteredSections" :key="item.slug">
                        <a :href="'/docs/' + item.slug" 
                           class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold border transition-all duration-200"
                           :class="item.slug === '{{ $currentSlug }}' 
                                ? 'bg-brand-teal/10 border-brand-teal/30 text-brand-cyan font-bold shadow-lg shadow-brand-teal/5' 
                                : 'bg-transparent border-transparent text-[#94A3B8]/75 hover:bg-[#25282D]/40 hover:text-brand-white hover:border-white/5'">
                            <span x-text="item.icon"></span>
                            <span x-text="item.title"></span>
                        </a>
                    </template>
                    <div x-show="filteredSections.length === 0" class="text-center py-6 text-xs text-[#94A3B8]/40" style="display: none;">
                        No articles match filters
                    </div>
                </nav>

                <div class="pt-4 border-t border-brand-teal/10 flex items-center justify-between text-[10px] text-[#94A3B8]/35">
                    <span>v1.2.0 API Specs</span>
                    <a href="{{ route('home') }}" class="hover:text-brand-cyan transition-colors">Corporate Site →</a>
                </div>
            </div>
        </aside>

        <!-- Mobile Drawer Navigation -->
        <div x-show="mobileSidebarOpen" 
             class="fixed inset-0 z-50 md:hidden flex justify-end"
             x-description="Mobile Table of Contents Drawer"
             style="display: none;">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-brand-dark-secondary/85 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
            <!-- Drawer Body -->
            <div class="relative w-80 max-w-sm glass-card border-l border-brand-teal/20 p-6 overflow-y-auto flex flex-col gap-6"
                 x-show="mobileSidebarOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <div class="flex items-center justify-between border-b border-white/5 pb-3">
                    <h3 class="text-sm font-extrabold text-brand-white uppercase tracking-widest">📚 Table of Contents</h3>
                    <button type="button" @click="mobileSidebarOpen = false" class="text-brand-gray hover:text-brand-white text-base">✕</button>
                </div>

                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Filter articles..." 
                           class="w-full rounded-xl border border-brand-teal/15 bg-brand-dark-secondary/60 px-3.5 py-2.5 text-xs text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan/20 transition-all">
                    <span class="absolute right-3.5 top-3 text-[10px] opacity-35">🔍</span>
                </div>

                <!-- Links list -->
                <nav class="space-y-1.5 flex-1">
                    <template x-for="item in filteredSections" :key="item.slug">
                        <a :href="'/docs/' + item.slug" 
                           @click="mobileSidebarOpen = false"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-semibold border transition-all duration-200"
                           :class="item.slug === '{{ $currentSlug }}' 
                                ? 'bg-brand-teal/10 border-brand-teal/30 text-brand-cyan font-bold' 
                                : 'bg-transparent border-transparent text-[#94A3B8]/75 hover:bg-[#25282D]/40 hover:text-brand-white hover:border-white/5'">
                            <span x-text="item.icon"></span>
                            <span x-text="item.title"></span>
                        </a>
                    </template>
                </nav>

                <div class="pt-4 border-t border-brand-teal/10 text-center text-[10px] text-[#94A3B8]/30">
                    Diwebs Tech Docs · Confidential Internal Guidelines
                </div>
            </div>
        </div>

        <!-- 2. Documentation Content Area (Right/Center) -->
        <div class="flex-1 w-full glass-card rounded-2xl border border-brand-teal/15 p-6 md:p-10 flex flex-col justify-between min-h-[500px]">
            
            <div class="space-y-6">
                <!-- Breadcrumbs (Desktop Only) -->
                <div class="hidden md:flex items-center gap-2 text-xs font-semibold text-brand-cyan mb-2">
                    <a href="/docs" class="hover:underline">Documentation</a>
                    <span class="text-[#94A3B8]/30">/</span>
                    <span class="text-brand-white">{{ $currentSection['title'] }}</span>
                </div>

                <!-- Include active section contents -->
                <article class="prose prose-invert max-w-none">
                    @include($currentSection['view'])
                </article>
            </div>

            <!-- 3. Bottom GitBook-style Pagination Links -->
            <div class="mt-12 pt-8 border-t border-brand-teal/10 flex items-center justify-between gap-4">
                @php
                    $keys = array_keys($sections);
                    $index = array_search($currentSlug, $keys);
                    
                    $prevKey = $index > 0 ? $keys[$index - 1] : null;
                    $nextKey = $index < count($keys) - 1 ? $keys[$index + 1] : null;
                @endphp

                @if($prevKey)
                    <a href="/docs/{{ $prevKey }}" class="flex flex-col items-start gap-1 rounded-xl bg-brand-teal/5 border border-brand-teal/10 px-5 py-3 hover:bg-brand-teal/10 hover:border-brand-cyan/40 transition-all group">
                        <span class="text-[9px] uppercase tracking-wider text-[#94A3B8]/40 font-bold">← Previous Page</span>
                        <span class="text-xs font-bold text-brand-white group-hover:text-brand-cyan transition-colors">{{ $sections[$prevKey]['title'] }}</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextKey)
                    <a href="/docs/{{ $nextKey }}" class="flex flex-col items-end gap-1 rounded-xl bg-brand-teal/5 border border-brand-teal/10 px-5 py-3 hover:bg-brand-teal/10 hover:border-brand-cyan/40 transition-all group text-right">
                        <span class="text-[9px] uppercase tracking-wider text-[#94A3B8]/40 font-bold">Next Page →</span>
                        <span class="text-xs font-bold text-brand-white group-hover:text-brand-cyan transition-colors">{{ $sections[$nextKey]['title'] }}</span>
                    </a>
                @else
                    <div></div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
