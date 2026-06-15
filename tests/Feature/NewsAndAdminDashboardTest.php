<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Course;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsAndAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed some categories
        $this->categories = [
            'Technology', 
            'AI', 
            'Cybersecurity', 
            'SaaS', 
            'Software Engineering', 
            'Cloud', 
            'CBT Updates', 
            'Company News'
        ];
    }

    public function test_public_news_index_displays_published_articles()
    {
        // Create a published and a draft article
        NewsArticle::create([
            'title' => 'Published Tech Trends',
            'content' => 'Content of published tech trends.',
            'category' => 'Technology',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        NewsArticle::create([
            'title' => 'Draft AI Ideas',
            'content' => 'Content of draft AI ideas.',
            'category' => 'AI',
            'status' => 'draft',
        ]);

        $response = $this->get('/diwebs-news');
        $response->assertOk();
        $response->assertSee('Published Tech Trends');
        $response->assertDontSee('Draft AI Ideas');
    }

    public function test_public_news_show_page_increments_views()
    {
        $article = NewsArticle::create([
            'title' => 'Cybersecurity Standards',
            'content' => 'Secure all assets with Argon2id and multi-factor authorization codes.',
            'category' => 'Cybersecurity',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $this->assertEquals(0, $article->view_count);

        $response = $this->get('/diwebs-news/' . $article->slug);
        $response->assertOk();
        $response->assertSee('Cybersecurity Standards');
        
        $article->refresh();
        $this->assertEquals(1, $article->view_count);
    }

    public function test_public_news_show_page_blocks_drafts_for_guests()
    {
        $article = NewsArticle::create([
            'title' => 'Hidden Roadmap',
            'content' => 'Upcoming features.',
            'category' => 'Technology',
            'status' => 'draft',
        ]);

        $response = $this->get('/diwebs-news/' . $article->slug);
        $response->assertStatus(404);
    }

    public function test_admin_dashboard_pages_block_guests()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/projects');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/finance');
        $response->assertRedirect('/login');
    }

    public function test_admin_dashboard_pages_block_non_admins()
    {
        $user = User::create([
            'name' => 'Normal Student',
            'email' => 'student@diwebstechagency.website',
            'password' => 'SecurePassword123!',
            'role' => 'student'
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_admin_subpages()
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@diwebstechagency.website',
            'password' => 'SecurePassword123!',
            'role' => 'super_admin'
        ]);

        // Seed some dummy items so templates don't crash
        Course::create(['title' => 'Intro to CBT', 'description' => 'Test course', 'price' => 0, 'slug' => 'intro-to-cbt', 'instructor_name' => 'Test Instructor']);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/projects');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/finance');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/courses');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/ai');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/leads');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/news');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/support');
        $response->assertOk();

        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertOk();
    }

    public function test_super_admin_can_create_and_delete_news_articles()
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@diwebstechagency.website',
            'password' => 'SecurePassword123!',
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($admin)->post('/admin/news/store', [
            'title' => 'Innovative SaaS Launch',
            'content' => 'New SaaS release from Diwebs Tech.',
            'category' => 'SaaS',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/news');
        $this->assertDatabaseHas('news_articles', ['title' => 'Innovative SaaS Launch']);

        $article = NewsArticle::where('title', 'Innovative SaaS Launch')->first();
        
        $deleteResponse = $this->actingAs($admin)->post('/admin/news/' . $article->id . '/delete');
        $deleteResponse->assertRedirect('/admin/news');
        $this->assertDatabaseMissing('news_articles', ['title' => 'Innovative SaaS Launch']);
    }
}
