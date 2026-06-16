<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset static settings in SettingsHelper to clear in-memory state
        $reflector = new \ReflectionClass(\App\Helpers\SettingsHelper::class);
        $property = $reflector->getProperty('settings');
        $property->setAccessible(true);
        $property->setValue(null, null);

        // Clean up any settings.json file from the filesystem to ensure test isolation
        if (file_exists(storage_path('app/settings.json'))) {
            unlink(storage_path('app/settings.json'));
        }
        Cache::flush();

        // Create a super_admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@diwebstechagency.website',
            'password' => bcrypt('SecurePassword123!'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    protected function tearDown(): void
    {
        // Reset static settings in SettingsHelper to clear in-memory state
        $reflector = new \ReflectionClass(\App\Helpers\SettingsHelper::class);
        $property = $reflector->getProperty('settings');
        $property->setAccessible(true);
        $property->setValue(null, null);

        // Clean up settings.json after each test run
        if (file_exists(storage_path('app/settings.json'))) {
            unlink(storage_path('app/settings.json'));
        }
        Cache::flush();

        parent::tearDown();
    }

    public function test_non_admin_cannot_access_or_update_mail_settings()
    {
        // 1. Guest access should redirect to login
        $this->get(route('admin.settings'))->assertRedirect('/login');

        // 2. Client role should get 403 Forbidden
        $client = User::create([
            'name' => 'Client User',
            'email' => 'client@diwebstechagency.website',
            'password' => bcrypt('SecurePassword123!'),
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->actingAs($client)->get(route('admin.settings'))->assertStatus(403);
    }

    public function test_admin_can_update_mail_settings()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.mail.update'), [
            'mail_mailer' => 'smtp',
            'mail_host' => 'mail.testserver.com',
            'mail_port' => 587,
            'mail_username' => 'testuser@testserver.com',
            'mail_password' => 'supersecretpass',
            'mail_scheme' => 'tls',
            'mail_from_address' => 'sender@testserver.com',
            'mail_from_name' => 'Test Sender Name',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // Check SettingsHelper values
        $this->assertEquals('smtp', \App\Helpers\SettingsHelper::get('mail_mailer'));
        $this->assertEquals('mail.testserver.com', \App\Helpers\SettingsHelper::get('mail_host'));
        $this->assertEquals(587, \App\Helpers\SettingsHelper::get('mail_port'));
        $this->assertEquals('testuser@testserver.com', \App\Helpers\SettingsHelper::get('mail_username'));
        $this->assertEquals('supersecretpass', \App\Helpers\SettingsHelper::get('mail_password'));
        $this->assertEquals('tls', \App\Helpers\SettingsHelper::get('mail_scheme'));
        $this->assertEquals('sender@testserver.com', \App\Helpers\SettingsHelper::get('mail_from_address'));
        $this->assertEquals('Test Sender Name', \App\Helpers\SettingsHelper::get('mail_from_name'));
    }

    public function test_admin_can_send_test_email()
    {
        // Configure to use array mailer explicitly
        \App\Helpers\SettingsHelper::set('mail_mailer', 'array');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.mail.test'), [
            'test_email' => 'recipient@testdomain.com'
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // Retrieve array transport from Laravel mailer and assert message details
        $transport = Mail::getSymfonyTransport();
        $messages = $transport->messages();
        
        $this->assertCount(1, $messages);
        
        $message = $messages[0];
        $this->assertStringContainsString('Diwebs SMTP Verification Test', $message->getOriginalMessage()->toString());
        $this->assertStringContainsString('recipient@testdomain.com', $message->getOriginalMessage()->toString());
    }
}
