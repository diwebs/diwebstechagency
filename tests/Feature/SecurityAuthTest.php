<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_password_complexity_checks()
    {
        // 1. Weak password registration should fail standard validations if checked
        // Min length 12
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@diwebstechagency.website',
            'password' => 'weakpass',
            'role' => 'student'
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_without_otp_fails()
    {
        // Registration without validating OTP code session first
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@diwebstechagency.website',
            'password' => 'SecurePassword123!',
            'role' => 'student'
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_otp_dispatch_and_verification()
    {
        $email = 'test@diwebstechagency.website';

        // 1. Request OTP Code
        $sendResponse = $this->postJson('/register/otp/send', ['email' => $email]);
        $sendResponse->assertOk();

        // 2. Fetch Code from Database
        $otp = OtpCode::where('email_or_phone', $email)->first();
        $this->assertNotNull($otp);

        // 3. Verify Code
        $verifyResponse = $this->postJson('/register/otp/verify', [
            'email' => $email,
            'code' => $otp->code
        ]);
        $verifyResponse->assertOk();

        // 4. Session now should contain validated email flag
        $this->assertEquals($email, session('validated_register_email'));
    }

    public function test_admin_blocked_on_standard_login_gate()
    {
        // Seed an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@diwebstechagency.website',
            'password' => 'SecurePassword123!',
            'role' => 'super_admin'
        ]);

        // Attempt login via standard gate (session does not have admin_gate_accessed)
        $response = $this->postJson('/login', [
            'email' => $admin->email,
            'password' => 'SecurePassword123!'
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Admin credentials must authenticate using the designated secure gateway.'
        ]);
    }

    public function test_admin_login_allowed_via_hidden_gate()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@diwebstechagency.website',
            'password' => 'SecurePassword123!',
            'role' => 'super_admin'
        ]);

        $deviceUuid = 'test-device-uuid-123';
        \App\Models\UserDevice::create([
            'user_id' => $admin->id,
            'device_uuid' => $deviceUuid,
            'browser' => 'Unknown Browser',
            'os' => 'Unknown OS',
            'ip_address' => '127.0.0.1',
            'is_trusted' => true,
            'last_active_at' => now()
        ]);

        // Hit custom hidden route first to set session flag
        $gateResponse = $this->get('/secure-gate-admin');
        $gateResponse->assertOk();

        // Attempt login
        $response = $this->withCookie('diwebs_device_uuid', $deviceUuid)->postJson('/login', [
            'email' => $admin->email,
            'password' => 'SecurePassword123!'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['redirect', 'device_uuid']);
    }

    public function test_rate_limiting_cooldown_triggers()
    {
        // Setup rate limit hit
        $email = 'bruteforce@diwebstechagency.website';

        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/login', [
                'email' => $email,
                'password' => 'wrongpass'
            ]);
        }

        // The 6th attempt should return 429 Too Many Requests
        $response->assertStatus(429);
        $response->assertJsonStructure(['message']);
    }

    public function test_registration_for_all_roles_redirects_to_respective_dashboard()
    {
        \Illuminate\Support\Facades\Http::fake();

        $rolesWithRedirects = [
            'student' => '/academy/dashboard',
            'instructor' => '/academy/dashboard',
            'candidate' => '/cbt',
            'partner' => '/cbt/partner/dashboard',
            'client' => '/portal'
        ];

        foreach ($rolesWithRedirects as $role => $expectedRedirectUrl) {
            $email = "new_{$role}@diwebstechagency.website";

            // 1. Send OTP
            $sendResponse = $this->postJson('/register/otp/send', ['email' => $email]);
            $sendResponse->assertOk();

            $otp = OtpCode::where('email_or_phone', $email)->first();
            $this->assertNotNull($otp);

            // 2. Verify OTP
            $verifyResponse = $this->postJson('/register/otp/verify', [
                'email' => $email,
                'code' => $otp->code
            ]);
            $verifyResponse->assertOk();

            // 3. Complete Registration
            $response = $this->post('/register', [
                'name' => "Test " . ucfirst($role),
                'email' => $email,
                'password' => 'SecurePassword123!',
                'password_confirmation' => 'SecurePassword123!',
                'role' => $role,
                'country' => 'Nigeria'
            ]);

            // Assert redirect to the correct role dashboard
            $response->assertRedirect($expectedRedirectUrl);

            // Follow redirect and assert 200 OK
            $this->get($expectedRedirectUrl)->assertStatus(200);

            // Logout user for next iteration
            $this->post('/logout');
        }
    }
}
