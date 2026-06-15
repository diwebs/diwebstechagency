<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsArticle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'author_name',
        'author_bio',
        'author_avatar',
        'view_count',
        'read_time_minutes'
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            // Generate unique slug
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }

            // Estimate reading time (average 200 words per minute)
            $wordCount = str_word_count(strip_tags($article->content));
            $article->read_time_minutes = max(1, (int) ceil($wordCount / 200));
            
            // Auto truncate excerpt if missing
            if (empty($article->excerpt)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 150);
            }
        });
    }
}
