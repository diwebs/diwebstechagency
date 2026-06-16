<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'status' => 'active'
        ]);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $response = $this->post(route('portal.review.store'), [
            'rating' => 5,
            'comment' => 'This is a great review comment by a client!',
            'company_name' => 'Test Company'
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('reviews');
    }

    public function test_client_can_submit_review(): void
    {
        $response = $this->actingAs($this->client)
            ->post(route('portal.review.store'), [
                'rating' => 5,
                'comment' => 'This is a great review comment by a client!',
                'company_name' => 'Test Company'
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->client->id,
            'client_name' => 'Test Client',
            'company_name' => 'Test Company',
            'rating' => 5,
            'comment' => 'This is a great review comment by a client!',
            'status' => 'approved'
        ]);
    }

    public function test_review_validation_requires_rating_and_comment(): void
    {
        // 1. Missing rating
        $response = $this->actingAs($this->client)
            ->post(route('portal.review.store'), [
                'comment' => 'This is a great review comment by a client!'
            ]);
        $response->assertSessionHasErrors(['rating']);

        // 2. Rating out of bounds
        $response = $this->actingAs($this->client)
            ->post(route('portal.review.store'), [
                'rating' => 6,
                'comment' => 'This is a great review comment by a client!'
            ]);
        $response->assertSessionHasErrors(['rating']);

        // 3. Comment too short
        $response = $this->actingAs($this->client)
            ->post(route('portal.review.store'), [
                'rating' => 5,
                'comment' => 'Sht'
            ]);
        $response->assertSessionHasErrors(['comment']);
    }

    public function test_homepage_renders_approved_reviews(): void
    {
        $review = Review::create([
            'user_id' => $this->client->id,
            'client_name' => 'Test Client Name',
            'company_name' => 'Awesome Company Inc.',
            'rating' => 5,
            'comment' => 'Perfect project delivery experience!',
            'status' => 'approved'
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Test Client Name');
        $response->assertSee('Awesome Company Inc.');
        $response->assertSee('Perfect project delivery experience!');
    }
}
