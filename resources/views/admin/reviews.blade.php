@extends('layouts.admin')

@section('title', 'Review Moderation - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Customer Reviews Moderation</h1>
            <p class="text-sm text-brand-gray mt-1">Review, approve, or remove client satisfaction testimonials shown on the public landing page.</p>
        </div>
        <span class="text-xs text-brand-gray">{{ $reviews->count() }} total reviews</span>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">Client Info</th>
                        <th class="px-6 py-4">Rating</th>
                        <th class="px-6 py-4">Comment</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Submitted</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-brand-teal to-brand-cyan flex items-center justify-center text-brand-dark-secondary text-xs font-bold">
                                        {{ strtoupper(substr($review->client_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-brand-white">{{ $review->client_name }}</p>
                                        <p class="text-brand-gray/70 text-[10px]">{{ $review->company_name ?? 'Individual' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex text-amber-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            ⭐
                                        @else
                                            <span class="opacity-30">⭐</span>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-sm">
                                <p class="text-brand-white leading-relaxed line-clamp-3" title="{{ $review->comment }}">
                                    {{ $review->comment }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2.5 py-0.5 text-[9px] font-bold uppercase
                                    @if($review->status === 'approved') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @else bg-amber-500/10 text-amber-400 border border-amber-500/20
                                    @endif">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">{{ $review->created_at ? $review->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($review->status !== 'approved')
                                    <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="rounded px-3 py-1.5 text-[10px] font-bold uppercase transition-all cursor-pointer bg-emerald-950 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-900/30">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="inline ml-1.5" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
                                    @csrf
                                    <button type="submit" 
                                            class="rounded px-3 py-1.5 text-[10px] font-bold uppercase transition-all cursor-pointer bg-red-950 text-red-400 border border-red-500/20 hover:bg-red-900/35">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-brand-gray text-xs">
                                No customer reviews submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
