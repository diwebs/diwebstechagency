<?php

namespace App\Http\Controllers;

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
use App\Models\CrmReport;
use App\Models\CrmLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmController extends Controller
{
    /**
     * Check if user is staff and get their role/permissions info
     */
    private function getAuthInfo()
    {
        if (auth()->guard('staff')->check()) {
            $staff = auth()->guard('staff')->user();
            return [
                'type' => 'staff',
                'user' => $staff,
                'role' => $staff->role->title ?? 'Unassigned',
                'dept' => $staff->department->code ?? 'Unassigned',
                'is_admin' => ($staff->role->title ?? '') === 'CEO' || ($staff->department->code ?? '') === 'EXEC'
            ];
        }

        return [
            'type' => 'admin',
            'user' => auth()->user(),
            'role' => 'Super Admin',
            'dept' => 'EXEC',
            'is_admin' => true
        ];
    }

    /**
     * Helper to restrict queries based on role
     */
    private function applyAccessFilter($query, $staffField = 'assigned_staff_id')
    {
        $auth = $this->getAuthInfo();
        if ($auth['type'] === 'staff' && !$auth['is_admin']) {
            // Account Managers only see assigned accounts
            if (str_contains($auth['role'], 'Account Manager')) {
                return $query->where($staffField, $auth['user']->id);
            }
        }
        return $query;
    }

    /**
     * Helper to log CRM action
     */
    private function logAction($action, $description)
    {
        $staffId = auth()->guard('staff')->check() ? auth()->guard('staff')->id() : null;
        CrmLog::create([
            'staff_id' => $staffId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * CRM Dashboard view
     */
    public function dashboard()
    {
        $auth = $this->getAuthInfo();

        // 1. Stats
        $stats = [
            'total_leads' => $this->applyAccessFilter(CrmLead::query(), 'assigned_staff_id')->count(),
            'qualified_leads' => $this->applyAccessFilter(CrmLead::query(), 'assigned_staff_id')->where('status', 'Qualified')->count(),
            'active_clients' => $this->applyAccessFilter(CrmClient::query(), 'assigned_manager_id')->where('account_status', 'Active')->count(),
            'closed_deals' => $this->applyAccessFilter(CrmDeal::query(), 'assigned_sales_rep_id')->where('stage', 'Closed Won')->count(),
            'lost_deals' => $this->applyAccessFilter(CrmDeal::query(), 'assigned_sales_rep_id')->where('stage', 'Closed Lost')->count(),
            'pending_tasks' => $this->applyAccessFilter(CrmTask::query(), 'assigned_staff_id')->whereIn('status', ['Pending', 'In Progress'])->count(),
            'revenue_generated' => $this->applyAccessFilter(CrmInvoice::query(), 'crm_client_id')->where('status', 'Paid')->sum('total_amount'),
            'open_tickets' => $this->applyAccessFilter(CrmTicket::query(), 'assigned_staff_id')->whereIn('status', ['Open', 'Pending'])->count(),
            'active_campaigns' => CrmCampaign::where('status', 'Active')->count(),
        ];

        // Conversion Rate
        $totalDeals = $this->applyAccessFilter(CrmDeal::query(), 'assigned_sales_rep_id')->count();
        $wonDeals = $stats['closed_deals'];
        $stats['conversion_rate'] = $totalDeals > 0 ? round(($wonDeals / $totalDeals) * 100, 1) . '%' : '0%';

        // 2. Widgets
        $recentLeads = $this->applyAccessFilter(CrmLead::orderBy('created_at', 'desc')->take(5), 'assigned_staff_id')->get();
        $upcomingMeetings = $this->applyAccessFilter(CrmMeeting::where('scheduled_at', '>=', now())->orderBy('scheduled_at', 'asc')->take(5), 'assigned_staff_id')->get();
        $pendingTasks = $this->applyAccessFilter(CrmTask::whereIn('status', ['Pending', 'In Progress'])->orderBy('due_date', 'asc')->take(5), 'assigned_staff_id')->get();
        $recentMessages = $this->applyAccessFilter(CrmMessage::orderBy('created_at', 'desc')->take(5), 'crm_client_id')->get();
        $contractExpirations = $this->applyAccessFilter(CrmContract::where('status', 'Active')->where('end_date', '<=', now()->addDays(60))->orderBy('end_date', 'asc')->take(5), 'crm_client_id')->get();

        // 3. Simulated/Real Graph metrics
        $leadSourceStats = CrmLead::select('source', DB::raw('count(*) as count'))->groupBy('source')->get()->toArray();
        $pipelineStats = CrmDeal::select('stage', DB::raw('count(*) as count'))->groupBy('stage')->get()->toArray();

        return view('admin.crm.dashboard', compact(
            'stats', 'recentLeads', 'upcomingMeetings', 'pendingTasks', 'recentMessages', 'contractExpirations', 'leadSourceStats', 'pipelineStats', 'auth'
        ));
    }

    /**
     * Leads Submodule
     */
    public function leads()
    {
        $auth = $this->getAuthInfo();

        // Auto-sync registered clients into CRM leads if not present
        $clients = \App\Models\User::where('role', 'client')->get();
        foreach ($clients as $clientUser) {
            CrmLead::firstOrCreate(
                ['email' => $clientUser->email],
                [
                    'full_name' => $clientUser->name,
                    'company_name' => $clientUser->company_name ?? null,
                    'email' => $clientUser->email,
                    'phone' => $clientUser->phone ?? null,
                    'country' => $clientUser->country ?? null,
                    'source' => 'Client Registration',
                    'service_interest' => 'Client Portal Account',
                    'status' => 'New',
                    'lead_score' => 25
                ]
            );
        }

        $leads = $this->applyAccessFilter(CrmLead::with('assignedStaff')->orderBy('created_at', 'desc'), 'assigned_staff_id')->paginate(15);
        $staffMembers = Staff::where('status', 'Active')->get();

        return view('admin.crm.leads', compact('leads', 'staffMembers', 'auth'));
    }

    public function storeLead(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'required|string',
            'service_interest' => 'nullable|string',
            'budget_range' => 'nullable|string',
            'country' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_staff_id' => 'nullable|exists:staff_members,id'
        ]);

        // Calculate a basic lead score
        $score = 10;
        if ($request->filled('company_name')) $score += 20;
        if ($request->filled('phone')) $score += 15;
        if ($request->filled('budget_range')) $score += 30;
        if ($request->source === 'referrals') $score += 25;
        $validated['lead_score'] = $score;

        $lead = CrmLead::create($validated);

        $this->logAction('Lead Created', "Manual entry of lead {$lead->full_name}");

        return back()->with('success', 'Lead created successfully.');
    }

    public function convertToClient(Request $request, $id)
    {
        $lead = CrmLead::findOrFail($id);

        try {
            DB::transaction(function () use ($lead) {
                // Create company if company_name exists
                $companyId = null;
                if ($lead->company_name) {
                    $company = CrmCompany::firstOrCreate([
                        'organization_name' => $lead->company_name
                    ], [
                        'relationship_stage' => 'Active Client'
                    ]);
                    $companyId = $company->id;
                }

                // Create CRM Client
                $client = CrmClient::create([
                    'crm_company_id' => $companyId,
                    'company_name' => $lead->company_name,
                    'contact_person' => $lead->full_name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'country' => $lead->country,
                    'active_services' => [$lead->service_interest ?? 'Custom Development'],
                    'account_status' => 'Active',
                    'assigned_manager_id' => $lead->assigned_staff_id
                ]);

                // Update lead status
                $lead->update(['status' => 'Won']);

                // Create a prospect entry
                CrmProspect::create([
                    'crm_lead_id' => $lead->id,
                    'qualification_score' => $lead->lead_score,
                    'service_requirement_analysis' => 'Lead converted to active client.',
                    'budget_analysis' => 'Budget agreed: ' . ($lead->budget_range ?? 'N/A'),
                    'probability_scoring' => 100,
                    'conversion_tracking' => 'Converted on ' . now()->toDateString()
                ]);

                $this->logAction('Lead Converted', "Lead {$lead->full_name} converted to Client successfully.");
            });

            return redirect()->route('admin.crm.clients')->with('success', 'Lead converted to Client successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Conversion failed: ' . $e->getMessage());
        }
    }

    public function deleteLead($id)
    {
        $lead = CrmLead::findOrFail($id);
        $lead->delete();
        $this->logAction('Lead Deleted', "Deleted lead {$lead->full_name}");
        return back()->with('success', 'Lead deleted.');
    }

    /**
     * Prospects Submodule
     */
    public function prospects()
    {
        $auth = $this->getAuthInfo();
        $prospects = CrmProspect::with('lead')
            ->whereHas('lead', function ($query) {
                $this->applyAccessFilter($query, 'assigned_staff_id');
            })
            ->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.crm.prospects', compact('prospects', 'auth'));
    }

    public function updateProspect(Request $request, $id)
    {
        $prospect = CrmProspect::findOrFail($id);
        $prospect->update($request->validate([
            'qualification_score' => 'required|integer|min:0|max:100',
            'service_requirement_analysis' => 'nullable|string',
            'budget_analysis' => 'nullable|string',
            'probability_scoring' => 'required|integer|min:0|max:100',
            'conversion_tracking' => 'nullable|string'
        ]));

        $this->logAction('Prospect Updated', "Updated prospect score for lead #{$prospect->crm_lead_id}");
        return back()->with('success', 'Prospect records updated successfully.');
    }

    /**
     * Client Management Submodule
     */
    public function clients()
    {
        $auth = $this->getAuthInfo();
        $clients = $this->applyAccessFilter(CrmClient::with(['company', 'assignedManager'])->orderBy('created_at', 'desc'), 'assigned_manager_id')->paginate(15);
        $companies = CrmCompany::all();
        $staffMembers = Staff::where('status', 'Active')->get();

        return view('admin.crm.clients', compact('clients', 'companies', 'staffMembers', 'auth'));
    }

    public function storeClient(Request $request)
    {
        $validated = $request->validate([
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:crm_clients,email',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'crm_company_id' => 'nullable|exists:crm_companies,id',
            'country' => 'nullable|string',
            'address' => 'nullable|string',
            'industry' => 'nullable|string',
            'account_status' => 'required|in:Active,Inactive,Suspended,VIP',
            'assigned_manager_id' => 'nullable|exists:staff_members,id',
            'active_services' => 'nullable|string'
        ]);

        if ($request->filled('active_services')) {
            $validated['active_services'] = array_filter(array_map('trim', explode(',', $request->active_services)));
        } else {
            $validated['active_services'] = [];
        }

        $client = CrmClient::create($validated);
        $this->logAction('Client Created', "Manual client profile setup: {$client->contact_person}");

        return back()->with('success', 'Client profile registered successfully.');
    }

    public function updateClient(Request $request, $id)
    {
        $client = CrmClient::findOrFail($id);
        $validated = $request->validate([
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:crm_clients,email,' . $id,
            'phone' => 'nullable|string|max:50',
            'crm_company_id' => 'nullable|exists:crm_companies,id',
            'country' => 'nullable|string',
            'address' => 'nullable|string',
            'industry' => 'nullable|string',
            'account_status' => 'required|in:Active,Inactive,Suspended,VIP',
            'assigned_manager_id' => 'nullable|exists:staff_members,id',
            'active_services' => 'nullable|string'
        ]);

        if ($request->filled('active_services')) {
            $validated['active_services'] = array_filter(array_map('trim', explode(',', $request->active_services)));
        }

        $client->update($validated);

        // Sync country/region to corresponding User account
        if ($client->email && !empty($validated['country'])) {
            \App\Models\User::where('email', $client->email)->update(['country' => $validated['country']]);
        }

        $this->logAction('Client Updated', "Updated client profile & region for ID #{$client->id}");

        return back()->with('success', 'Client profile and regional billing configuration updated.');
    }

    /**
     * Companies / Corporate accounts
     */
    public function companies()
    {
        $auth = $this->getAuthInfo();
        $companies = CrmCompany::withCount('clients')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.crm.companies', compact('companies', 'auth'));
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'annual_value' => 'required|numeric|min:0',
            'relationship_stage' => 'required|string',
            'corporate_contact' => 'nullable|string',
            'partnership_level' => 'nullable|string'
        ]);

        CrmCompany::create($validated);
        $this->logAction('Company Added', "Registered B2B organization {$validated['organization_name']}");

        return back()->with('success', 'B2B company record registered successfully.');
    }

    /**
     * Deals Pipeline Submodule
     */
    public function deals()
    {
        $auth = $this->getAuthInfo();
        $deals = $this->applyAccessFilter(CrmDeal::with(['client', 'lead', 'assignedSalesRep']), 'assigned_sales_rep_id')->get();
        $clients = CrmClient::all();
        $leads = CrmLead::all();
        $staffMembers = Staff::where('status', 'Active')->get();

        $pipelineStages = [
            'New Inquiry',
            'Discovery',
            'Consultation',
            'Proposal',
            'Negotiation',
            'Payment Pending',
            'Closed Won',
            'Closed Lost'
        ];

        return view('admin.crm.deals', compact('deals', 'clients', 'leads', 'staffMembers', 'pipelineStages', 'auth'));
    }

    public function storeDeal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'crm_client_id' => 'nullable|exists:crm_clients,id',
            'crm_lead_id' => 'nullable|exists:crm_leads,id',
            'service' => 'required|string',
            'deal_value' => 'required|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'probability_percent' => 'required|integer|min:0|max:100',
            'assigned_sales_rep_id' => 'nullable|exists:staff_members,id',
            'stage' => 'required|string'
        ]);

        $deal = CrmDeal::create($validated);
        $this->logAction('Deal Registered', "Registered pipeline deal: {$deal->title}");

        return back()->with('success', 'Pipeline deal registered successfully.');
    }

    public function updateDealStage(Request $request, $id)
    {
        $deal = CrmDeal::findOrFail($id);
        $request->validate(['stage' => 'required|string']);

        $oldStage = $deal->stage;
        $deal->update(['stage' => $request->stage]);

        $this->logAction('Deal Moved', "Moved Deal '{$deal->title}' from '{$oldStage}' to '{$request->stage}'");

        return response()->json(['status' => 'success', 'message' => 'Deal pipeline stage updated successfully.']);
    }

    /**
     * Tasks Submodule
     */
    public function tasks()
    {
        $auth = $this->getAuthInfo();
        $tasks = $this->applyAccessFilter(CrmTask::with(['assignedStaff', 'lead', 'client'])->orderBy('due_date', 'asc'), 'assigned_staff_id')->paginate(15);
        $staffMembers = Staff::where('status', 'Active')->get();
        $leads = CrmLead::all();
        $clients = CrmClient::all();

        return view('admin.crm.tasks', compact('tasks', 'staffMembers', 'leads', 'clients', 'auth'));
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'task_type' => 'required|string',
            'assigned_staff_id' => 'nullable|exists:staff_members,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|string',
            'crm_lead_id' => 'nullable|exists:crm_leads,id',
            'crm_client_id' => 'nullable|exists:crm_clients,id',
            'notes' => 'nullable|string'
        ]);

        CrmTask::create($validated);
        $this->logAction('Task Scheduled', "Scheduled CRM Follow-up: {$validated['title']}");

        return back()->with('success', 'Task scheduled successfully.');
    }

    public function updateTaskStatus(Request $request, $id)
    {
        $task = CrmTask::findOrFail($id);
        $request->validate(['status' => 'required|string']);
        $task->update(['status' => $request->status]);

        $this->logAction('Task Status Updated', "Task #{$task->id} status updated to {$request->status}");
        return back()->with('success', 'Task status updated.');
    }

    /**
     * Meetings Submodule
     */
    public function meetings()
    {
        $auth = $this->getAuthInfo();
        $meetings = $this->applyAccessFilter(CrmMeeting::with(['lead', 'client', 'assignedStaff'])->orderBy('scheduled_at', 'asc'), 'assigned_staff_id')->paginate(15);
        $leads = CrmLead::all();
        $clients = CrmClient::all();
        $staffMembers = Staff::where('status', 'Active')->get();

        return view('admin.crm.meetings', compact('meetings', 'leads', 'clients', 'staffMembers', 'auth'));
    }

    public function storeMeeting(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meeting_type' => 'required|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:5',
            'attendees' => 'nullable|string',
            'notes' => 'nullable|string',
            'crm_lead_id' => 'nullable|exists:crm_leads,id',
            'crm_client_id' => 'nullable|exists:crm_clients,id',
            'assigned_staff_id' => 'nullable|exists:staff_members,id'
        ]);

        if ($request->filled('attendees')) {
            $validated['attendees'] = array_filter(array_map('trim', explode(',', $request->attendees)));
        } else {
            $validated['attendees'] = [];
        }

        CrmMeeting::create($validated);
        $this->logAction('Meeting Scheduled', "Scheduled appointment: {$validated['title']}");

        return back()->with('success', 'Meeting scheduled successfully.');
    }

    /**
     * Communications Center
     */
    public function communications()
    {
        $auth = $this->getAuthInfo();
        $messages = $this->applyAccessFilter(CrmMessage::with(['lead', 'client', 'staff'])->orderBy('created_at', 'desc'), 'staff_id')->paginate(15);
        $leads = CrmLead::all();
        $clients = CrmClient::all();
        $staffMembers = Staff::where('status', 'Active')->get();

        return view('admin.crm.communications', compact('messages', 'leads', 'clients', 'staffMembers', 'auth'));
    }

    public function storeCommunication(Request $request)
    {
        $validated = $request->validate([
            'channel' => 'required|string',
            'direction' => 'required|string',
            'sender' => 'nullable|string',
            'recipient' => 'nullable|string',
            'content' => 'required|string',
            'crm_lead_id' => 'nullable|exists:crm_leads,id',
            'crm_client_id' => 'nullable|exists:crm_clients,id',
            'staff_id' => 'nullable|exists:staff_members,id'
        ]);

        CrmMessage::create($validated);
        $this->logAction('Communication Logged', "Logged conversation history under channel {$validated['channel']}");

        return back()->with('success', 'Communication log entry saved.');
    }

    /**
     * Contracts Management Submodule
     */
    public function contracts()
    {
        $auth = $this->getAuthInfo();
        $contracts = $this->applyAccessFilter(CrmContract::with(['client', 'company']), 'crm_client_id')->paginate(15);
        $clients = CrmClient::all();
        $companies = CrmCompany::all();

        return view('admin.crm.contracts', compact('contracts', 'clients', 'companies', 'auth'));
    }

    public function storeContract(Request $request)
    {
        $validated = $request->validate([
            'contract_number' => 'required|string|unique:crm_contracts,contract_number',
            'title' => 'required|string|max:255',
            'contract_type' => 'required|string',
            'crm_client_id' => 'nullable|exists:crm_clients,id',
            'crm_company_id' => 'nullable|exists:crm_companies,id',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string'
        ]);

        CrmContract::create($validated);
        $this->logAction('Contract Generated', "Created contract {$validated['contract_number']}: {$validated['title']}");

        return back()->with('success', 'Contract generated successfully.');
    }

    public function signContract(Request $request, $id)
    {
        $contract = CrmContract::findOrFail($id);
        $request->validate([
            'signature_data' => 'required|string'
        ]);

        $contract->update([
            'status' => 'Active',
            'signed_at' => now(),
            'signature_data' => $request->signature_data
        ]);

        $this->logAction('Contract Signed', "Contract {$contract->contract_number} signed electronically.");

        return back()->with('success', 'Contract successfully signed and activated.');
    }

    /**
     * Invoice Management Submodule
     */
    public function invoices()
    {
        $auth = $this->getAuthInfo();
        $invoices = $this->applyAccessFilter(CrmInvoice::with(['client', 'company']), 'crm_client_id')->paginate(15);
        $clients = CrmClient::all();
        $companies = CrmCompany::all();

        return view('admin.crm.invoices', compact('invoices', 'clients', 'companies', 'auth'));
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:crm_invoices,invoice_number',
            'crm_client_id' => 'required|exists:crm_clients,id',
            'crm_company_id' => 'nullable|exists:crm_companies,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'is_recurring' => 'nullable|boolean',
            'billing_interval' => 'nullable|string'
        ]);

        $validated['tax'] = $validated['tax'] ?? 0.00;
        $validated['total_amount'] = $validated['amount'] + $validated['tax'];
        $validated['is_recurring'] = $request->has('is_recurring');

        CrmInvoice::create($validated);
        $this->logAction('Invoice Created', "Created CRM Invoice {$validated['invoice_number']}");

        return back()->with('success', 'Invoice issued successfully.');
    }

    public function updateInvoiceStatus(Request $request, $id)
    {
        $invoice = CrmInvoice::findOrFail($id);
        $request->validate(['status' => 'required|string']);
        
        $invoice->update([
            'status' => $request->status,
            'paid_at' => $request->status === 'Paid' ? now() : null
        ]);

        $this->logAction('Invoice Status', "Updated Invoice #{$invoice->invoice_number} status to {$request->status}");
        return back()->with('success', 'Invoice status updated.');
    }

    /**
     * Support Tickets Submodule
     */
    public function tickets()
    {
        $auth = $this->getAuthInfo();
        $tickets = $this->applyAccessFilter(CrmTicket::with(['client', 'assignedStaff']), 'assigned_staff_id')->paginate(15);
        $clients = CrmClient::all();
        $staffMembers = Staff::where('status', 'Active')->get();

        return view('admin.crm.tickets', compact('tickets', 'clients', 'staffMembers', 'auth'));
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|unique:crm_tickets,ticket_number',
            'crm_client_id' => 'required|exists:crm_clients,id',
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|string',
            'message' => 'required|string',
            'assigned_staff_id' => 'nullable|exists:staff_members,id'
        ]);

        CrmTicket::create($validated);
        $this->logAction('Support Ticket Logged', "Logged support ticket: {$validated['ticket_number']}");

        return back()->with('success', 'Support ticket logged.');
    }

    public function updateTicketStatus(Request $request, $id)
    {
        $ticket = CrmTicket::findOrFail($id);
        $request->validate(['status' => 'required|string']);
        $ticket->update(['status' => $request->status]);

        $this->logAction('Ticket Status', "Ticket #{$ticket->ticket_number} status updated to {$request->status}");
        return back()->with('success', 'Ticket status updated.');
    }

    /**
     * Marketing Campaigns Submodule
     */
    public function campaigns()
    {
        $auth = $this->getAuthInfo();
        $campaigns = CrmCampaign::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.crm.campaigns', compact('campaigns', 'auth'));
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'campaign_type' => 'required|string',
            'status' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'spent' => 'required|numeric|min:0',
            'target_audience' => 'nullable|string'
        ]);

        // Default empty metrics
        $validated['metrics'] = [
            'open_rate' => '0%',
            'ctr' => '0%',
            'conversions' => 0,
            'roi' => '0%'
        ];

        CrmCampaign::create($validated);
        $this->logAction('Campaign Setup', "Set up marketing campaign: {$validated['name']}");

        return back()->with('success', 'Campaign initialized successfully.');
    }

    /**
     * Reports & Forecasting
     */
    public function reports()
    {
        $auth = $this->getAuthInfo();
        $reports = CrmReport::orderBy('created_at', 'desc')->paginate(15);
        
        // Pipeline summary counts
        $dealSum = CrmDeal::sum('deal_value');
        $leadCount = CrmLead::count();
        $clientCount = CrmClient::count();
        $invoiceSum = CrmInvoice::where('status', 'Paid')->sum('total_amount');

        return view('admin.crm.reports', compact('reports', 'dealSum', 'leadCount', 'clientCount', 'invoiceSum', 'auth'));
    }

    public function exportReport(Request $request)
    {
        $request->validate(['type' => 'required|string']);
        $type = $request->type;

        $fileName = "CRM_Report_{$type}_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');

            if ($type === 'leads') {
                fputcsv($file, ['ID', 'Full Name', 'Company', 'Email', 'Phone', 'Source', 'Service Interest', 'Score', 'Status', 'Created At']);
                $records = CrmLead::all();
                foreach ($records as $r) {
                    fputcsv($file, [$r->id, $r->full_name, $r->company_name, $r->email, $r->phone, $r->source, $r->service_interest, $r->lead_score, $r->status, $r->created_at]);
                }
            } elseif ($type === 'clients') {
                fputcsv($file, ['ID', 'Contact Person', 'Company', 'Email', 'Phone', 'Country', 'Status', 'Created At']);
                $records = CrmClient::all();
                foreach ($records as $r) {
                    fputcsv($file, [$r->id, $r->contact_person, $r->company_name, $r->email, $r->phone, $r->country, $r->account_status, $r->created_at]);
                }
            } elseif ($type === 'deals') {
                fputcsv($file, ['ID', 'Deal Title', 'Service', 'Value', 'Stage', 'Close Date', 'Probability', 'Created At']);
                $records = CrmDeal::all();
                foreach ($records as $r) {
                    fputcsv($file, [$r->id, $r->title, $r->service, $r->deal_value, $r->stage, $r->expected_close_date, $r->probability_percent, $r->created_at]);
                }
            } else {
                fputcsv($file, ['CRM General Metric Report', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Total Leads', CrmLead::count()]);
                fputcsv($file, ['Total Active Clients', CrmClient::where('account_status', 'Active')->count()]);
                fputcsv($file, ['Total Deals Volume', CrmDeal::sum('deal_value')]);
                fputcsv($file, ['Total Invoiced Revenue', CrmInvoice::where('status', 'Paid')->sum('total_amount')]);
            }

            fclose($file);
        };

        $this->logAction('Report Exported', "Exported {$type} CSV data sheet");

        // Record the report generation in reports log table
        CrmReport::create([
            'name' => "Exported sheet: " . ucfirst($type),
            'report_type' => $type,
            'parameters' => ['format' => 'csv'],
            'generated_by_id' => auth()->guard('staff')->check() ? auth()->guard('staff')->id() : auth()->id()
        ]);

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Settings
     */
    public function settings()
    {
        $auth = $this->getAuthInfo();
        
        $settings = [
            'auto_assign_leads' => cache('crm_auto_assign_leads', true),
            'min_lead_score' => cache('crm_min_lead_score', 30),
            'currency_symbol' => cache('crm_currency_symbol', '₦'),
            'ticket_escalation_hours' => cache('crm_ticket_escalation_hours', 24)
        ];

        return view('admin.crm.settings', compact('settings', 'auth'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'min_lead_score' => 'required|integer|min:0',
            'currency_symbol' => 'required|string|max:5',
            'ticket_escalation_hours' => 'required|integer|min:1'
        ]);

        cache(['crm_auto_assign_leads' => $request->has('auto_assign_leads')]);
        cache(['crm_min_lead_score' => $request->min_lead_score]);
        cache(['crm_currency_symbol' => $request->currency_symbol]);
        cache(['crm_ticket_escalation_hours' => $request->ticket_escalation_hours]);

        $this->logAction('Settings Updated', "Updated CRM Configuration Settings");

        return back()->with('success', 'CRM settings updated.');
    }
}
