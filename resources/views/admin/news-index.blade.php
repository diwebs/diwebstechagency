@extends('layouts.admin')

@section('title', 'Newsroom Blog Manager - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Newsroom Publications</h1>
            <p class="text-sm text-brand-gray mt-1">Publish news updates, AI tutorials, technical releases, and SEO articles.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all flex items-center gap-1">
            ➕ Publish Article
        </a>
    </div>

    <!-- Articles Table List -->
    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Author</th>
                        <th class="px-6 py-4">Metrics</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Published At</th>
                        <th class="px-6 py-4 text-right">Controls</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($articles as $article)
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-brand-white max-w-xs">
                                <p class="line-clamp-1 hover:text-brand-cyan"><a href="{{ route('news.show', $article->slug) }}" target="_blank">{{ $article->title }}</a></p>
                                <p class="text-[10px] text-brand-gray font-mono line-clamp-1 mt-0.5">{{ $article->slug }}</p>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">
                                {{ $article->category }}
                            </td>
                            <td class="px-6 py-4 text-brand-gray">
                                {{ $article->author_name }}
                            </td>
                            <td class="px-6 py-4 text-brand-gray">
                                <span class="block">👁️ {{ number_format($article->view_count) }} views</span>
                                <span class="block text-[10px]">⏱️ {{ $article->read_time_minutes }} min read</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase
                                    @if($article->status === 'published') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                    @elseif($article->status === 'draft') bg-brand-dark-secondary text-brand-gray border border-brand-teal/15
                                    @else bg-rose-950 text-rose-400 border border-rose-500/20
                                    @endif">
                                    {{ $article->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">
                                {{ $article->published_at ? $article->published_at->format('M d, Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('admin.news.edit', $article->id) }}" class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2.5 py-1.5 text-[10px] font-bold text-brand-cyan hover:bg-brand-teal/20">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.news.delete', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded bg-rose-950/20 border border-rose-500/20 px-2.5 py-1.5 text-[10px] font-bold text-rose-400 hover:bg-rose-900/20 cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-brand-gray">No news publications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-brand-teal/10">
            {{ $articles->links() }}
        </div>
    </div>
</div>
@endsection
