<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('category'); // e.g. Technology, AI, Cybersecurity, SaaS, Software Engineering, Cloud, CBT Updates, Company News
            $table->string('status')->default('draft'); // draft, published, archived, scheduled
            $table->timestamp('published_at')->nullable();
            
            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Author & metrics
            $table->string('author_name')->default('Diwebs Editorial');
            $table->text('author_bio')->nullable();
            $table->string('author_avatar')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('read_time_minutes')->default(1);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
