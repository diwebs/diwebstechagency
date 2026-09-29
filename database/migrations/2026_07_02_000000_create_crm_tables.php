<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. CRM Companies
        Schema::create('crm_companies', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name');
            $table->string('industry')->nullable();
            $table->decimal('annual_value', 15, 2)->default(0.00);
            $table->string('relationship_stage')->default('Prospect'); // Lead, Prospect, Active Client, Partner
            $table->string('corporate_contact')->nullable();
            $table->string('partnership_level')->nullable(); // Standard, Silver, Gold, Platinum
            $table->timestamps();
        });

        // 2. CRM Leads
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('source'); // website forms, contact page, newsletter, chatbot, API webhook, manual entry, referrals, social media campaigns
            $table->string('service_interest')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('country')->nullable();
            $table->string('status')->default('New'); // New, Contacted, Qualified, Negotiation, Proposal Sent, Won, Lost
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->integer('lead_score')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. CRM Prospects
        Schema::create('crm_prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_lead_id')->constrained('crm_leads')->onDelete('cascade');
            $table->integer('qualification_score')->default(0);
            $table->text('service_requirement_analysis')->nullable();
            $table->text('budget_analysis')->nullable();
            $table->integer('probability_scoring')->default(0);
            $table->text('conversion_tracking')->nullable();
            $table->timestamps();
        });

        // 4. CRM Clients
        Schema::create('crm_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_company_id')->nullable()->constrained('crm_companies')->onDelete('set null');
            $table->string('company_name')->nullable();
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->string('industry')->nullable();
            $table->text('active_services')->nullable(); // JSON structure
            $table->string('account_status')->default('Active'); // Active, Inactive, Suspended, VIP
            $table->foreignId('assigned_manager_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->timestamps();
        });

        // 5. CRM Deals
        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('crm_client_id')->nullable()->constrained('crm_clients')->onDelete('cascade');
            $table->foreignId('crm_lead_id')->nullable()->constrained('crm_leads')->onDelete('set null');
            $table->string('service');
            $table->decimal('deal_value', 15, 2);
            $table->date('expected_close_date')->nullable();
            $table->integer('probability_percent')->default(50);
            $table->foreignId('assigned_sales_rep_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->string('stage')->default('New Inquiry'); // New Inquiry, Discovery, Consultation, Proposal, Negotiation, Payment Pending, Closed Won, Closed Lost
            $table->timestamps();
        });

        // 6. CRM Tasks
        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('task_type'); // Call, Email, Meeting, Proposal, Demo, Payment reminder
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->string('priority')->default('Medium'); // Low, Medium, High, Urgent
            $table->string('status')->default('Pending'); // Pending, In Progress, Completed, Overdue
            $table->foreignId('crm_lead_id')->nullable()->constrained('crm_leads')->onDelete('cascade');
            $table->foreignId('crm_client_id')->nullable()->constrained('crm_clients')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. CRM Meetings
        Schema::create('crm_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meeting_type'); // consultation call, sales meeting, demo, onboarding session
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->text('attendees')->nullable(); // JSON formatted list
            $table->text('notes')->nullable();
            $table->foreignId('crm_lead_id')->nullable()->constrained('crm_leads')->onDelete('cascade');
            $table->foreignId('crm_client_id')->nullable()->constrained('crm_clients')->onDelete('cascade');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->timestamps();
        });

        // 8. CRM Messages
        Schema::create('crm_messages', function (Blueprint $table) {
            $table->id();
            $table->string('channel'); // email, WhatsApp, SMS, call_note, internal_note
            $table->string('direction'); // inbound, outbound, internal
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->text('content');
            $table->text('attachments')->nullable(); // JSON array
            $table->foreignId('crm_lead_id')->nullable()->constrained('crm_leads')->onDelete('cascade');
            $table->foreignId('crm_client_id')->nullable()->constrained('crm_clients')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->timestamps();
        });

        // 9. CRM Contracts
        Schema::create('crm_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->string('contract_type'); // NDA, Service Agreement, Partnership Agreement, Contractor Agreement, Employee Agreement
            $table->foreignId('crm_client_id')->nullable()->constrained('crm_clients')->onDelete('cascade');
            $table->foreignId('crm_company_id')->nullable()->constrained('crm_companies')->onDelete('set null');
            $table->decimal('value', 15, 2)->default(0.00);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('Draft'); // Draft, Active, Expired, Terminated, Renewed
            $table->dateTime('signed_at')->nullable();
            $table->string('signed_copy_path')->nullable();
            $table->text('signature_data')->nullable(); // Capture e-signature base64
            $table->timestamps();
        });

        // 10. CRM Invoices
        Schema::create('crm_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('crm_client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->foreignId('crm_company_id')->nullable()->constrained('crm_companies')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('Draft'); // Draft, Sent, Paid, Partial, Overdue, Cancelled
            $table->date('due_date');
            $table->dateTime('paid_at')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('billing_interval')->nullable(); // monthly, yearly
            $table->timestamps();
        });

        // 11. CRM Tickets
        Schema::create('crm_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('crm_client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->string('subject');
            $table->string('category'); // Technical Issue, Billing, Complaint, Inquiry, Project Support
            $table->string('status')->default('Open'); // Open, Pending, Resolved, Closed
            $table->string('priority')->default('Medium'); // Low, Medium, High, Urgent
            $table->text('message');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->timestamps();
        });

        // 12. CRM Campaigns
        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('campaign_type'); // email, ad, referral, promotion
            $table->string('status')->default('Planning'); // Planning, Active, Paused, Completed
            $table->decimal('budget', 15, 2)->default(0.00);
            $table->decimal('spent', 15, 2)->default(0.00);
            $table->string('target_audience')->nullable();
            $table->text('metrics')->nullable(); // JSON statistics (open_rate, ctr, etc)
            $table->timestamps();
        });

        // 13. CRM Reports
        Schema::create('crm_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('report_type'); // sales performance, lead conversion, etc.
            $table->text('parameters')->nullable(); // JSON criteria
            $table->unsignedBigInteger('generated_by_id')->nullable(); // references user or staff
            $table->timestamps();
        });

        // 14. CRM Logs
        Schema::create('crm_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->string('action');
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_logs');
        Schema::dropIfExists('crm_reports');
        Schema::dropIfExists('crm_campaigns');
        Schema::dropIfExists('crm_tickets');
        Schema::dropIfExists('crm_invoices');
        Schema::dropIfExists('crm_contracts');
        Schema::dropIfExists('crm_messages');
        Schema::dropIfExists('crm_meetings');
        Schema::dropIfExists('crm_tasks');
        Schema::dropIfExists('crm_deals');
        Schema::dropIfExists('crm_clients');
        Schema::dropIfExists('crm_prospects');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_companies');
    }
};
