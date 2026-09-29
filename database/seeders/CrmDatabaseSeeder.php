<?php

namespace Database\Seeders;

use App\Models\CrmLead;
use App\Models\CrmProspect;
use App\Models\CrmClient;
use App\Models\CrmCompany;
use App\Models\CrmDeal;
use App\Models\CrmTask;
use App\Models\CrmMeeting;
use App\Models\CrmMessage;
use App\Models\CrmContract;
use App\Models\CrmInvoice;
use App\Models\CrmTicket;
use App\Models\CrmCampaign;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class CrmDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = Staff::first();
        $staffId = $staff ? $staff->id : null;

        // 1. CRM Companies
        $comp1 = CrmCompany::create([
            'organization_name' => 'Vanguard Logistics Corp',
            'industry' => 'Transportation & Logistics',
            'annual_value' => 15000000.00,
            'relationship_stage' => 'Active Client',
            'corporate_contact' => 'Marcus Vance (COO)',
            'partnership_level' => 'Gold'
        ]);

        $comp2 = CrmCompany::create([
            'organization_name' => 'Apex Health Solutions',
            'industry' => 'Healthcare Tech',
            'annual_value' => 8500000.00,
            'relationship_stage' => 'Prospect',
            'corporate_contact' => 'Dr. Clara Thorne',
            'partnership_level' => 'Silver'
        ]);

        // 2. CRM Leads
        $lead1 = CrmLead::create([
            'full_name' => 'Timothy Adebayo',
            'company_name' => 'Zenith Fintech Group',
            'email' => 'tadebayo@zenithfintech.com',
            'phone' => '+2348123456789',
            'source' => 'website forms',
            'service_interest' => 'Fintech API Integration',
            'budget_range' => '₦10,000,000 - ₦20,000,000',
            'country' => 'Nigeria',
            'status' => 'New',
            'assigned_staff_id' => $staffId,
            'lead_score' => 45,
            'notes' => 'Intake from main contact form, interested in custom ledger integration.'
        ]);

        $lead2 = CrmLead::create([
            'full_name' => 'Samantha Smith',
            'company_name' => 'EduTech Solutions',
            'email' => 'samantha@edutech.io',
            'phone' => '+155501982',
            'source' => 'chatbot',
            'service_interest' => 'Custom CBT Portal',
            'budget_range' => '₦5,000,000 - ₦10,000,000',
            'country' => 'United States',
            'status' => 'Qualified',
            'assigned_staff_id' => $staffId,
            'lead_score' => 60,
            'notes' => 'Chatbot qualified lead. Urgency high.'
        ]);

        $lead3 = CrmLead::create([
            'full_name' => 'Marcus Vance',
            'company_name' => 'Vanguard Logistics Corp',
            'email' => 'marcus@vanguard.com',
            'phone' => '+2348023456789',
            'source' => 'referrals',
            'service_interest' => 'Logistics Custom Dashboard',
            'budget_range' => '₦15,000,000+',
            'country' => 'Nigeria',
            'status' => 'Won',
            'assigned_staff_id' => $staffId,
            'lead_score' => 85,
            'notes' => 'Referred by CEO board member. Extremely high probability.'
        ]);

        // 3. CRM Prospects
        CrmProspect::create([
            'crm_lead_id' => $lead2->id,
            'qualification_score' => 75,
            'service_requirement_analysis' => 'CBT Portal with remote webcam monitoring capability.',
            'budget_analysis' => 'Client verified budget limits, comfortable with Standard tiers.',
            'probability_scoring' => 70,
            'conversion_tracking' => 'In consultation phase.'
        ]);

        CrmProspect::create([
            'crm_lead_id' => $lead3->id,
            'qualification_score' => 95,
            'service_requirement_analysis' => 'Full dashboard tracking shipment logs.',
            'budget_analysis' => 'Budget approved at corporate level.',
            'probability_scoring' => 100,
            'conversion_tracking' => 'Converted to active B2B client.'
        ]);

        // 4. CRM Clients
        $client1 = CrmClient::create([
            'crm_company_id' => $comp1->id,
            'company_name' => 'Vanguard Logistics Corp',
            'contact_person' => 'Marcus Vance',
            'email' => 'marcus@vanguard.com',
            'phone' => '+2348023456789',
            'country' => 'Nigeria',
            'address' => 'Vanguard Towers, Lekki Phase 1, Lagos',
            'industry' => 'Logistics',
            'active_services' => ['Custom Dashboard', 'Cloud Deployment'],
            'account_status' => 'Active',
            'assigned_manager_id' => $staffId
        ]);

        $client2 = CrmClient::create([
            'crm_company_id' => null,
            'company_name' => null,
            'contact_person' => 'Bimbo Balogun',
            'email' => 'bimbo@baloguntech.xyz',
            'phone' => '+2349033333333',
            'country' => 'Nigeria',
            'address' => '34 Allen Avenue, Ikeja, Lagos',
            'industry' => 'Retail',
            'active_services' => ['E-Commerce App development'],
            'account_status' => 'VIP',
            'assigned_manager_id' => $staffId
        ]);

        // 5. CRM Deals
        CrmDeal::create([
            'title' => 'Vanguard Custom Dashboard',
            'crm_client_id' => $client1->id,
            'crm_lead_id' => $lead3->id,
            'service' => 'Custom Dashboard',
            'deal_value' => 15000000.00,
            'expected_close_date' => now()->addDays(30),
            'probability_percent' => 90,
            'assigned_sales_rep_id' => $staffId,
            'stage' => 'Proposal'
        ]);

        CrmDeal::create([
            'title' => 'Balogun E-Commerce App',
            'crm_client_id' => $client2->id,
            'crm_lead_id' => null,
            'service' => 'Mobile App Development',
            'deal_value' => 8500000.00,
            'expected_close_date' => now()->subDays(5),
            'probability_percent' => 100,
            'assigned_sales_rep_id' => $staffId,
            'stage' => 'Closed Won'
        ]);

        CrmDeal::create([
            'title' => 'EduTech Custom CBT Portal',
            'crm_client_id' => null,
            'crm_lead_id' => $lead2->id,
            'service' => 'CBT Portal Development',
            'deal_value' => 7000000.00,
            'expected_close_date' => now()->addDays(45),
            'probability_percent' => 50,
            'assigned_sales_rep_id' => $staffId,
            'stage' => 'Discovery'
        ]);

        // 6. CRM Tasks
        CrmTask::create([
            'title' => 'Call Timothy to clarify API limits',
            'task_type' => 'Call',
            'assigned_staff_id' => $staffId,
            'due_date' => now()->addDays(1),
            'priority' => 'Medium',
            'status' => 'Pending',
            'crm_lead_id' => $lead1->id,
            'crm_client_id' => null,
            'notes' => 'Discuss rates and limits for Zenith Fintech API integration.'
        ]);

        CrmTask::create([
            'title' => 'Send contract agreement SLA',
            'task_type' => 'Proposal',
            'assigned_staff_id' => $staffId,
            'due_date' => now()->subDays(2),
            'priority' => 'High',
            'status' => 'Overdue',
            'crm_lead_id' => $lead3->id,
            'crm_client_id' => $client1->id,
            'notes' => 'Overdue. Need corporate validation.'
        ]);

        // 7. CRM Meetings
        CrmMeeting::create([
            'title' => 'Vanguard System Integration kick-off',
            'meeting_type' => 'onboarding session',
            'scheduled_at' => now()->addDays(3)->setHour(10)->setMinute(0),
            'duration_minutes' => 60,
            'attendees' => ['marcus@vanguard.com', 'cto@diwebstechagency.website'],
            'notes' => 'Onboarding session with Vanguard management.',
            'crm_lead_id' => null,
            'crm_client_id' => $client1->id,
            'assigned_staff_id' => $staffId
        ]);

        // 8. CRM Messages
        CrmMessage::create([
            'channel' => 'WhatsApp',
            'direction' => 'inbound',
            'sender' => 'Bimbo Balogun',
            'recipient' => 'Diwebs Support',
            'content' => 'Please confirm if the payment gateway integrates Paystack and USDT.',
            'attachments' => null,
            'crm_lead_id' => null,
            'crm_client_id' => $client2->id,
            'staff_id' => $staffId
        ]);

        CrmMessage::create([
            'channel' => 'email',
            'direction' => 'outbound',
            'sender' => 'Diwebs Sales Rep',
            'recipient' => 'samantha@edutech.io',
            'content' => 'Hi Samantha, here is the requested CBT Portal quotation file.',
            'attachments' => ['/storage/quotation_edutech.pdf'],
            'crm_lead_id' => $lead2->id,
            'crm_client_id' => null,
            'staff_id' => $staffId
        ]);

        // 9. CRM Contracts
        CrmContract::create([
            'contract_number' => 'DWS-AGR-2026-001',
            'title' => 'SLA agreement Vanguard Dashboard',
            'contract_type' => 'Service Agreement',
            'crm_client_id' => $client1->id,
            'crm_company_id' => $comp1->id,
            'value' => 15000000.00,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addYear(),
            'status' => 'Active',
            'signed_at' => now()->subDays(5),
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        ]);

        // 10. CRM Invoices
        CrmInvoice::create([
            'invoice_number' => 'DWS-INV-2026-001',
            'crm_client_id' => $client1->id,
            'crm_company_id' => $comp1->id,
            'title' => 'System Design Approval Invoice',
            'description' => 'Milestone 1 documentation approval.',
            'amount' => 4500000.00,
            'tax' => 337500.00,
            'total_amount' => 4837500.00,
            'status' => 'Paid',
            'due_date' => now()->subDays(2),
            'paid_at' => now()->subDays(2),
            'is_recurring' => false
        ]);

        CrmInvoice::create([
            'invoice_number' => 'DWS-INV-2026-002',
            'crm_client_id' => $client1->id,
            'crm_company_id' => $comp1->id,
            'title' => 'Milestone 2 - Prototyping',
            'description' => 'Milestone 2 prototyping deliverables bill.',
            'amount' => 6000000.00,
            'tax' => 450000.00,
            'total_amount' => 6450000.00,
            'status' => 'Sent',
            'due_date' => now()->addDays(15),
            'paid_at' => null,
            'is_recurring' => false
        ]);

        // 11. CRM Tickets
        CrmTicket::create([
            'ticket_number' => 'DWS-TKT-2026-001',
            'crm_client_id' => $client1->id,
            'subject' => 'Vanguard server sync connection drop',
            'category' => 'Technical Issue',
            'status' => 'Open',
            'priority' => 'High',
            'message' => 'Lagos synchronizer server drops websocket connection frequently.',
            'assigned_staff_id' => $staffId
        ]);

        // 12. CRM Campaigns
        CrmCampaign::create([
            'name' => 'Lagos Tech Startup Promotion',
            'campaign_type' => 'email',
            'status' => 'Active',
            'budget' => 200000.00,
            'spent' => 120000.00,
            'target_audience' => 'Fintech and Tech Founders in Lagos State.',
            'metrics' => [
                'open_rate' => '64.2%',
                'ctr' => '18.5%',
                'conversions' => 12,
                'roi' => '240%'
            ]
        ]);
    }
}
