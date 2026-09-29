<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Auto-expire stale active exam sessions older than 4 hours
Schedule::call(function () {
    \App\Models\ExamSession::where('status', 'active')
        ->where('started_at', '<', now()->subHours(4))
        ->update(['status' => 'void', 'ended_at' => now()]);
})->hourly()->name('expire-stale-exams')->withoutOverlapping();

// Purge security logs older than 90 days to manage DB size on shared hosting
Schedule::call(function () {
    \App\Models\SecurityLog::where('created_at', '<', now()->subDays(90))->delete();
})->daily()->name('purge-old-security-logs')->withoutOverlapping();

// Shared-hosting-safe cron registration reminder
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// CRM Automation: Lead auto-assignment
Schedule::call(function () {
    // Auto assign unassigned new leads to Sales staff in round-robin or randomly
    $salesStaff = \App\Models\Staff::whereHas('role', function ($query) {
        $query->where('title', 'like', '%Sales%')
              ->orWhere('title', 'like', '%Business Development%');
    })->get();

    if ($salesStaff->isNotEmpty()) {
        $unassignedLeads = \App\Models\CrmLead::whereNull('assigned_staff_id')->get();
        foreach ($unassignedLeads as $lead) {
            $staff = $salesStaff->random();
            $lead->update([
                'assigned_staff_id' => $staff->id,
                'status' => 'Contacted' // Move to contacted
            ]);
            // Log action
            \App\Models\CrmLog::create([
                'action' => 'Auto Assignment',
                'description' => "Lead {$lead->full_name} automatically assigned to {$staff->name}."
            ]);
        }
    }
})->daily()->name('crm-auto-assign-leads')->withoutOverlapping();

// CRM Automation: Follow-up reminders
Schedule::call(function () {
    // Find pending/in_progress tasks due today and log/notify
    $tasks = \App\Models\CrmTask::whereIn('status', ['Pending', 'In Progress'])
        ->whereDate('due_date', '=', now()->toDateString())
        ->get();

    foreach ($tasks as $task) {
        if ($task->assigned_staff_id) {
            // Auto log reminder
            \App\Models\CrmLog::create([
                'staff_id' => $task->assigned_staff_id,
                'action' => 'Task Reminder',
                'description' => "Reminder: Task '{$task->title}' is due today."
            ]);
        }
    }
})->daily()->name('crm-task-reminders')->withoutOverlapping();

// CRM Automation: Contract expiration alerts
Schedule::call(function () {
    // Find active contracts expiring in 30 days
    $expiringSoon = \App\Models\CrmContract::where('status', 'Active')
        ->whereDate('end_date', '=', now()->addDays(30)->toDateString())
        ->get();

    foreach ($expiringSoon as $contract) {
        $contract->update(['status' => 'Renewed']); // trigger automatic renewal process or flag
        \App\Models\CrmLog::create([
            'action' => 'Contract Expiry Alert',
            'description' => "Contract {$contract->contract_number} ({$contract->title}) will expire in 30 days."
        ]);
    }
})->daily()->name('crm-contract-expiry-alerts')->withoutOverlapping();

// CRM Automation: Overdue payment invoice reminders
Schedule::call(function () {
    // Find Sent/Draft invoices that are past due date
    $overdueInvoices = \App\Models\CrmInvoice::whereIn('status', ['Draft', 'Sent'])
        ->whereDate('due_date', '<', now()->toDateString())
        ->get();

    foreach ($overdueInvoices as $invoice) {
        $invoice->update(['status' => 'Overdue']);
        \App\Models\CrmLog::create([
            'action' => 'Invoice Overdue Alert',
            'description' => "Invoice {$invoice->invoice_number} is overdue. Amount: {$invoice->total_amount}."
        ]);
    }
})->daily()->name('crm-overdue-invoice-checks')->withoutOverlapping();

// CRM Automation: Support ticket escalation
Schedule::call(function () {
    // Escalate open/pending tickets older than 24 hours to high/urgent priority
    $staleTickets = \App\Models\CrmTicket::whereIn('status', ['Open', 'Pending'])
        ->where('created_at', '<', now()->subHours(24))
        ->where('priority', '!=', 'Urgent')
        ->get();

    foreach ($staleTickets as $ticket) {
        $ticket->update(['priority' => 'Urgent']);
        \App\Models\CrmLog::create([
            'action' => 'Ticket Escalation',
            'description' => "Support Ticket {$ticket->ticket_number} escalated to Urgent due to response delay."
        ]);
    }
})->hourly()->name('crm-ticket-escalations')->withoutOverlapping();
