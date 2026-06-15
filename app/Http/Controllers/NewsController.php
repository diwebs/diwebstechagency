<?php
 
namespace App\Http\Controllers;
 
use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsArticle::where('status', 'published')
            ->where('published_at', '<=', now());

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(9);

        // Featured post (latest published)
        $featured = NewsArticle::where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->first();

        // Trending posts (top views)
        $trending = NewsArticle::where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        $categories = [
            'Technology', 
            'AI', 
            'Cybersecurity', 
            'SaaS', 
            'Software Engineering', 
            'Cloud', 
            'CBT Updates', 
            'Company News'
        ];

        return view('news.index', compact('articles', 'featured', 'trending', 'categories'));
    }

    public function show($slug)
    {
        $article = NewsArticle::where('slug', $slug)->firstOrFail();

        // Check view permissions
        if ($article->status !== 'published' && !auth()->check()) {
            abort(404);
        }

        // Increment views
        $article->increment('view_count');

        // Fetch related posts (same category, different ID)
        $related = NewsArticle::where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('news.show', compact('article', 'related'));
    }
}
