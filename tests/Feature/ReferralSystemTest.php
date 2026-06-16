<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ReferralSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::create([
            'name' => 'Sarah Referrer',
            'email' => 'sarah@referrer.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'status' => 'active',
            'referral_code' => 'REF-SARAH'
        ]);
    }

    public function test_user_creation_generates_unique_referral_code(): void
    {
        $newUser = User::create([
            'name' => 'New Client',
            'email' => 'newclient@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'status' => 'active'
        ]);

        $this->assertNotEmpty($newUser->referral_code);
        $this->assertStringStartsWith('REF-', $newUser->referral_code);
    }

    public function test_registration_with_valid_referral_code(): void
    {
        // Setup session verified email (required by AuthController)
        session(['validated_register_email' => 'referredclient@test.com']);

        $response = $this->post(route('register'), [
            'name' => 'Referred Client',
            'email' => 'referredclient@test.com',
            'password' => 'SecurePassword123!',
            'role' => 'client',
            'referral_code' => 'REF-SARAH'
        ]);

        $response->assertStatus(302);

        $newClient = User::where('email', 'referredclient@test.com')->first();
        $this->assertNotNull($newClient);
        $this->assertEquals($this->client->id, $newClient->referred_by);

        // Assert Referral record was created
        $this->assertDatabaseHas('referrals', [
            'referrer_id' => $this->client->id,
            'referee_id' => $newClient->id,
            'bonus_amount' => 50.00,
            'status' => 'pending'
        ]);
    }

    public function test_registration_with_invalid_referral_code_fails(): void
    {
        session(['validated_register_email' => 'referredclient@test.com']);

        $response = $this->post(route('register'), [
            'name' => 'Referred Client',
            'email' => 'referredclient@test.com',
            'password' => 'SecurePassword123!',
            'role' => 'client',
            'referral_code' => 'REF-INVALID'
        ]);

        $response->assertSessionHasErrors(['referral_code']);
    }

    public function test_admin_settings_updates_referral_bonus_amount(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.settings.update'), [
                'app_name' => 'New Brand Name',
                'referral_bonus_amount' => 75.00
            ]);

        $response->assertStatus(302);
        $this->assertEquals(75.00, cache('referral_bonus_amount'));
    }

    public function test_admin_can_update_referral_status_to_paid(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        $referee = User::create([
            'name' => 'Referee Client',
            'email' => 'referee@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'status' => 'active'
        ]);

        $referral = Referral::create([
            'referrer_id' => $this->client->id,
            'referee_id' => $referee->id,
            'bonus_amount' => 50.00,
            'status' => 'pending'
        ]);

        // Pay bonus
        $response = $this->actingAs($admin)
            ->post(route('admin.referrals.pay', $referral->id));

        $response->assertStatus(302);

        $referral->refresh();
        $this->assertEquals('paid', $referral->status);
        $this->assertNotNull($referral->paid_at);
    }
}
