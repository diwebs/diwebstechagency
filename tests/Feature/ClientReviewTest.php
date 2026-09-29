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
            'status' => 'pending'
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

    public function test_admin_can_approve_review(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@diwebstechagency.website',
            'password' => bcrypt('SecurePassword123!'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        $review = Review::create([
            'user_id' => $this->client->id,
            'client_name' => 'Test Client',
            'rating' => 4,
            'comment' => 'This is a pending comment for test.',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.reviews.approve', $review->id));

        $response->assertStatus(302);
        $review->refresh();
        $this->assertEquals('approved', $review->status);
    }

    public function test_admin_can_delete_review(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@diwebstechagency.website',
            'password' => bcrypt('SecurePassword123!'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        $review = Review::create([
            'user_id' => $this->client->id,
            'client_name' => 'Test Client',
            'rating' => 4,
            'comment' => 'This is a comment that will be deleted.',
            'status' => 'approved'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.reviews.delete', $review->id));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_payment_settings_persist_across_cache_clear(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@diwebstechagency.website',
            'password' => bcrypt('SecurePassword123!'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        // Save payment settings
        $response = $this->actingAs($admin)
            ->post(route('admin.payment-settings.update'), [
                'active_gateway'    => 'crypto',
                'default_currency'  => 'NGN',
                'currency_symbol'   => '₦',
                'currency_position' => 'before',
                'invoice_prefix'    => 'DIW',
                'tax_rate'          => 7.5,
                'tax_label'         => 'VAT',
                'crypto_wallet_btc' => 'test-btc-address',
                'crypto_wallet_usdt'=> 'test-usdt-address',
                'crypto_enabled'    => 'on'
            ]);

        $response->assertStatus(302);

        // Verify values are in SettingsHelper JSON storage
        $this->assertEquals('crypto', \App\Helpers\SettingsHelper::get('payment_active_gateway'));

        // Clear the cache
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        // Verify values still persist from JSON store
        $this->assertEquals('crypto', \App\Helpers\SettingsHelper::get('payment_active_gateway'));
        $this->assertEquals('NGN', \App\Helpers\SettingsHelper::get('payment_default_currency'));
        $this->assertEquals('₦', \App\Helpers\SettingsHelper::get('payment_currency_symbol'));
        $this->assertEquals('test-btc-address', \App\Helpers\SettingsHelper::get('payment_crypto_wallet_btc'));
    }
}
