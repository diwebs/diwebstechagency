<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AdminNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $client;
    protected User $admin;

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

        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);
    }

    public function test_contact_lead_form_submission_creates_notification(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'phone' => '+2348011223344',
            'company' => 'Doe Inc.',
            'service_needed' => 'Website Development',
            'message' => 'Hello, I want a custom enterprise website.'
        ];

        $response = $this->post(route('lead.submit'), $payload);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'contact_form',
            'title' => 'New Contact Lead: John Doe',
        ]);

        $notification = AdminNotification::where('type', 'contact_form')->first();
        $this->assertNotNull($notification);
        $this->assertEquals($payload, $notification->details);
        $this->assertFalse($notification->is_read);
    }

    public function test_newsletter_subscription_creates_notification(): void
    {
        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'subscribe@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'newsletter_subscribe',
            'title' => 'New Newsletter Subscription'
        ]);

        $notification = AdminNotification::where('type', 'newsletter_subscribe')->first();
        $this->assertNotNull($notification);
        $this->assertEquals(['email' => 'subscribe@example.com'], $notification->details);
    }

    public function test_client_portal_project_proposal_creates_notification(): void
    {
        $response = $this->actingAs($this->client)
            ->post(route('portal.project.store'), [
                'title' => 'E-Commerce Platform',
                'description' => 'A multi-vendor online shop.',
                'budget' => 25000,
                'service_type' => 'Website Development'
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'project_create',
            'title' => 'New Client Project Proposal: E-Commerce Platform'
        ]);

        $notification = AdminNotification::where('type', 'project_create')->first();
        $this->assertNotNull($notification);
        $this->assertEquals('Test Client', $notification->details['client_name']);
        $this->assertEquals('client@test.com', $notification->details['client_email']);
    }

    public function test_client_portal_service_request_creates_notification(): void
    {
        $response = $this->actingAs($this->client)
            ->post(route('portal.service-request.store'), [
                'title' => 'Custom API Design',
                'service_type' => 'SaaS Platform',
                'description' => 'Microservice framework building.',
                'budget_range' => '$10,000 - $25,000',
                'deadline' => now()->addDays(30)->format('Y-m-d')
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'service_request',
            'title' => 'New Service Request: Custom API Design'
        ]);

        $notification = AdminNotification::where('type', 'service_request')->first();
        $this->assertNotNull($notification);
        $this->assertEquals('Test Client', $notification->details['client_name']);
        $this->assertEquals('client@test.com', $notification->details['client_email']);
        $this->assertEquals('Custom API Design', $notification->details['title']);
    }

    public function test_client_portal_support_ticket_creates_notification(): void
    {
        $response = $this->actingAs($this->client)
            ->post(route('portal.ticket.create'), [
                'subject' => 'Database Sync Latency',
                'message' => 'We are experiencing 2s latency during backup cron job.',
                'priority' => 'critical'
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'support_ticket',
            'title' => 'New Support Ticket: Database Sync Latency'
        ]);

        $notification = AdminNotification::where('type', 'support_ticket')->first();
        $this->assertNotNull($notification);
        $this->assertEquals('Test Client', $notification->details['client_name']);
        $this->assertEquals('Database Sync Latency', $notification->details['subject']);
        $this->assertEquals('critical', $notification->details['priority']);
    }

    public function test_admin_can_view_and_manage_notifications(): void
    {
        $notification = AdminNotification::create([
            'type' => 'contact_form',
            'title' => 'Test Contact Submission',
            'details' => ['name' => 'Alice', 'email' => 'alice@test.com']
        ]);

        // Non-admin cannot access
        $response = $this->actingAs($this->client)->get(route('admin.notifications'));
        $response->assertStatus(403); // assuming middleware protects it

        // Admin can access
        $response = $this->actingAs($this->admin)->get(route('admin.notifications'));
        $response->assertStatus(200);
        $response->assertSee('Test Contact Submission');
        $response->assertSee('Alice');

        // Mark as read
        $response = $this->actingAs($this->admin)->post(route('admin.notifications.read', $notification->id));
        $response->assertStatus(302);
        
        $notification->refresh();
        $this->assertTrue($notification->is_read);

        // Mark all as read
        $notification2 = AdminNotification::create([
            'type' => 'newsletter_subscribe',
            'title' => 'Test Newsletter 2',
            'details' => ['email' => 'bob@test.com'],
            'is_read' => false
        ]);
        $this->assertFalse($notification2->is_read);

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.read-all'));
        $response->assertStatus(302);

        $notification2->refresh();
        $this->assertTrue($notification2->is_read);
    }
}
