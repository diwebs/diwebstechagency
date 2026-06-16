@extends('layouts.admin')

@section('title', 'Portfolio Project Showcase - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Portfolio Projects Showcase</h1>
            <p class="text-sm text-brand-gray mt-1">Manage external deploy projects, mockup screenshots, and project urls presented on the public showcase page.</p>
        </div>
        <a href="{{ route('admin.portfolios.create') }}" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all flex items-center gap-1">
            ➕ Add Portfolio Project
        </a>
    </div>

    <!-- Portfolio Table List -->
    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Mockup Image</th>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Description (Write-up)</th>
                        <th class="px-6 py-4">Live Website URL</th>
                        <th class="px-6 py-4">Display Order</th>
                        <th class="px-6 py-4 text-right">Controls</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($portfolios as $portfolio)
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4">
                                @if($portfolio->mock_image)
                                    <div class="relative w-24 aspect-video rounded-lg overflow-hidden border border-brand-teal/20 bg-brand-dark-secondary">
                                        <img src="{{ asset('storage/' . $portfolio->mock_image) }}" 
                                             alt="{{ $portfolio->title }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-24 aspect-video rounded-lg border border-brand-teal/10 bg-brand-teal/5 flex items-center justify-center text-brand-gray text-[10px] font-bold">
                                        No Image
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-brand-white">
                                {{ $portfolio->title }}
                            </td>
                            <td class="px-6 py-4 text-brand-gray max-w-sm">
                                <p class="line-clamp-2">{{ strip_tags($portfolio->description) }}</p>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">
                                @if($portfolio->project_url)
                                    <a href="{{ $portfolio->project_url }}" target="_blank" class="text-brand-cyan hover:underline truncate max-w-xs block">
                                        {{ $portfolio->project_url }}
                                    </a>
                                @else
                                    <span class="text-brand-gray/50">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-brand-white font-semibold">
                                {{ $portfolio->order }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('admin.portfolios.edit', $portfolio->id) }}" class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2.5 py-1.5 text-[10px] font-bold text-brand-cyan hover:bg-brand-teal/20">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.portfolios.delete', $portfolio->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this portfolio project? This action cannot be undone.');" class="inline">
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
                            <td colspan="6" class="px-6 py-12 text-center text-brand-gray">No portfolio projects found. Click the button above to add one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($portfolios->hasPages())
            <div class="px-6 py-4 border-t border-brand-teal/10">
                {{ $portfolios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
