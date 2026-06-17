<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Milestone;
use App\Models\Contract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTrackingTest extends TestCase
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

    public function test_client_can_create_project_proposal_request(): void
    {
        $response = $this->actingAs($this->client)
            ->post(route('portal.project.store'), [
                'title' => 'Web App Build',
                'description' => 'A custom Laravel application.',
                'budget' => 12000,
                'service_type' => 'Website Development'
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('projects', [
            'client_id' => $this->client->id,
            'title' => 'Web App Build',
            'service_type' => 'Website Development',
            'budget' => 12000,
            'is_validated' => false,
            'success_rate' => 0
        ]);
    }

    public function test_admin_can_validate_project(): void
    {
        $project = Project::create([
            'client_id' => $this->client->id,
            'title' => 'Web App Build',
            'description' => 'A custom Laravel application.',
            'budget' => 12000,
            'service_type' => 'Website Development',
            'status' => 'initiated',
            'is_validated' => false
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.validate', $project->id));

        $response->assertStatus(302);

        $project->refresh();
        $this->assertTrue($project->is_validated);
        $this->assertEquals('planning', $project->status);

        // Verify contract, milestone, and invoice were created
        $this->assertDatabaseHas('contracts', [
            'project_id' => $project->id,
            'client_id' => $this->client->id,
            'status' => 'pending_signature'
        ]);

        $this->assertDatabaseHas('milestones', [
            'project_id' => $project->id,
            'title' => 'Initial Project Kickoff',
            'status' => 'pending',
            'amount' => 12000
        ]);

        $this->assertDatabaseHas('invoices', [
            'project_id' => $project->id,
            'client_id' => $this->client->id,
            'amount' => 12000,
            'status' => 'unpaid'
        ]);
    }

    public function test_project_tracking_locked_until_validation_and_payment(): void
    {
        $project = Project::create([
            'client_id' => $this->client->id,
            'title' => 'Web App Build',
            'description' => 'A custom Laravel application.',
            'budget' => 12000,
            'service_type' => 'Website Development',
            'status' => 'initiated',
            'is_validated' => false
        ]);

        // 1. Not validated, not paid: details locked
        $response = $this->actingAs($this->client)
            ->get(route('portal.project', $project->id));
        $response->assertStatus(200);
        $response->assertSee('Project Sprints Locked');
        $response->assertSee('awaiting administrator validation');

        // Validate project
        $this->actingAs($this->admin)
            ->post(route('admin.projects.validate', $project->id));

        // 2. Validated, not paid: details locked
        $response = $this->actingAs($this->client)
            ->get(route('portal.project', $project->id));
        $response->assertStatus(200);
        $response->assertSee('Project Sprints Locked');
        $response->assertSee('initial payment has not been made');

        // Pay the invoice
        $invoice = Invoice::where('project_id', $project->id)->first();
        $this->actingAs($this->client)
            ->post(route('portal.invoice.pay', $invoice->id), [
                'payment_type' => 'full'
            ]);

        // 3. Validated and paid: details unlocked
        $response = $this->actingAs($this->client)
            ->get(route('portal.project', $project->id));
        $response->assertStatus(200);
        $response->assertDontSee('Project Sprints Locked');
        $response->assertSee('Initial Project Kickoff');
    }

    public function test_admin_can_update_success_rate_and_client_sees_it(): void
    {
        $project = Project::create([
            'client_id' => $this->client->id,
            'title' => 'Web App Build',
            'description' => 'A custom Laravel application.',
            'budget' => 12000,
            'service_type' => 'Website Development',
            'status' => 'active',
            'is_validated' => true,
            'success_rate' => 0
        ]);

        // Create paid invoice to unlock tracking
        Invoice::create([
            'project_id' => $project->id,
            'client_id' => $this->client->id,
            'amount' => 12000,
            'invoice_number' => 'INV-TEST-1',
            'status' => 'paid',
            'due_date' => now()
        ]);

        // Update success rate
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.success-rate', $project->id), [
                'success_rate' => 85
            ]);

        $response->assertStatus(302);

        $project->refresh();
        $this->assertEquals(85, $project->success_rate);

        // Client views project detail, asserts success rate displays
        $response = $this->actingAs($this->client)
            ->get(route('portal.project', $project->id));
        $response->assertStatus(200);
        $response->assertSee('Web Development Project Success Rate');
        $response->assertSee('85%');
    }

    public function test_admin_can_create_standalone_invoice(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.finance.invoice.store'), [
                'client_id' => $this->client->id,
                'invoice_number' => 'INV-TEST-STANDALONE',
                'title' => 'Custom API Integration Fee',
                'description' => 'Billing for custom REST integration.',
                'amount' => 1500.00,
                'due_date' => now()->addDays(5)->format('Y-m-d')
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'invoice_number' => 'INV-TEST-STANDALONE',
            'title' => 'Custom API Integration Fee',
            'description' => 'Billing for custom REST integration.',
            'amount' => 1500.00,
            'status' => 'unpaid'
        ]);
    }

    public function test_client_sees_standalone_invoice_on_billing(): void
    {
        Invoice::create([
            'client_id' => $this->client->id,
            'invoice_number' => 'INV-TEST-STANDALONE-2',
            'title' => 'Custom Landing Page Design',
            'description' => 'Refined UI layouts per contract section 4.',
            'amount' => 950.00,
            'status' => 'unpaid',
            'due_date' => now()->addDays(10)
        ]);

        $response = $this->actingAs($this->client)
            ->get(route('portal.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Custom Landing Page Design');
        $response->assertSee('Refined UI layouts per contract section 4.');
        $response->assertSee('INV-TEST-STANDALONE-2');
    }
}
