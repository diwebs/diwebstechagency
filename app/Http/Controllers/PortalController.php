<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Invoice;
use App\Models\Milestone;
use App\Models\Ticket;
use App\Models\Lead;
use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\Contract;
use App\Models\ProjectFile;
use App\Models\Message;
use App\Models\TeamAccess;
use App\Models\MilestoneLog;
use App\Models\PartnershipRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        // 1. Fetch related project items
        $projects = Project::with(['milestones.logs', 'invoices', 'client', 'assignments.staff'])->where('client_id', $user->id)->get();
        $unpaidInvoices = Invoice::where('client_id', $user->id)->whereIn('status', ['unpaid', 'pending_partial'])->get();
        $invoiceHistory = Invoice::where('client_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        // 2. Fetch service requests
        $serviceRequests = ServiceRequest::where('client_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        // 3. Fetch support tickets
        $tickets = Ticket::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        // 4. Fetch contracts
        $contracts = Contract::where('client_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        // 5. Fetch team access invited members
        $teamMembers = TeamAccess::where('client_id', $user->id)->get();
        
        // 6. Security telemetry logs & trusted devices
        $devices = \App\Models\UserDevice::where('user_id', $user->id)->get();
        $auditLogs = \App\Models\AuditLog::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(10)->get();

        // 7. Calculate Project Analytics Metrics
        $totalBudget = $projects->sum('budget');
        
        // Total spent so far
        $totalPaid = Invoice::where('client_id', $user->id)->where('status', 'paid')->sum('amount');
        
        // Task completion percentage
        $totalMilestones = 0;
        $approvedMilestones = 0;
        foreach ($projects as $project) {
            $totalMilestones += $project->milestones->count();
            $approvedMilestones += $project->milestones->where('status', 'approved')->count();
        }
        $taskCompletionRate = $totalMilestones > 0 ? round(($approvedMilestones / $totalMilestones) * 100) : 0;
        
        // Project files
        $projectFiles = ProjectFile::whereIn('project_id', $projects->pluck('id'))->orderBy('created_at', 'desc')->get();

        // 8. Fetch user reviews
        $reviews = \App\Models\Review::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // 9. Fetch client referrals
        $referrals = \App\Models\Referral::with('referee')->where('referrer_id', $user->id)->orderBy('created_at', 'desc')->get();
        $totalBonusEarned = \App\Models\Referral::where('referrer_id', $user->id)->where('status', 'paid')->sum('bonus_amount');
        $pendingBonus = \App\Models\Referral::where('referrer_id', $user->id)->whereIn('status', ['pending', 'approved'])->sum('bonus_amount');

        // 10. Fetch user notifications
        $userNotifications = \App\Models\UserNotification::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        \App\Models\UserNotification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);

        // 11. Fetch client partnership request
        $partnershipRequest = PartnershipRequest::where('user_id', $user->id)->first();

        return view('portal.dashboard', compact(
            'projects',
            'unpaidInvoices',
            'invoiceHistory',
            'serviceRequests',
            'tickets',
            'contracts',
            'teamMembers',
            'devices',
            'auditLogs',
            'totalBudget',
            'totalPaid',
            'taskCompletionRate',
            'projectFiles',
            'reviews',
            'referrals',
            'totalBonusEarned',
            'pendingBonus',
            'userNotifications',
            'partnershipRequest'
        ));
    }

    public function projectDetail(Request $request, $id)
    {
        $project = Project::with(['milestones.logs', 'invoices'])->where('client_id', $request->user()->id)->findOrFail($id);
        $projectFiles = ProjectFile::where('project_id', $project->id)->orderBy('created_at', 'desc')->get();
        return view('portal.project-detail', compact('project', 'projectFiles'));
    }

    public function payInvoice(Request $request, $id)
    {
        $invoice = Invoice::where('client_id', $request->user()->id)->findOrFail($id);
        $gateway = \App\Helpers\PaymentHelper::activeGateway();
        
        $request->validate([
            'payment_type' => 'required|in:full,installment,partial',
            'partial_amount' => 'nullable|numeric|min:1'
        ]);

        $paymentType = $request->input('payment_type');
        $amountToPay = $invoice->amount;

        if ($paymentType === 'installment') {
            $amountToPay = round($invoice->amount / 2, 2);
        } elseif ($paymentType === 'partial' && $request->filled('partial_amount')) {
            $amountToPay = round(min($invoice->amount, $request->input('partial_amount')), 2);
        }

        $isFullPayment = ($amountToPay >= $invoice->amount);
        
        if (in_array($gateway, ['bank_transfer', 'crypto'])) {
            $newStatus = $isFullPayment ? 'pending' : 'pending_partial';
            
            $invoice->update([
                'status' => $newStatus,
                'paid_at' => null
            ]);
            
            // Log security / financial log
            \App\Models\AuditLog::create([
                'user_id' => $request->user()->id,
                'event_type' => 'payment_submitted',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode(['invoice_id' => $invoice->id, 'amount' => $amountToPay, 'gateway' => $gateway, 'type' => $paymentType])
            ]);

            \App\Models\UserNotification::create([
                'user_id' => $request->user()->id,
                'title' => 'Invoice Payment Submitted',
                'message' => 'Your payment of ' . \App\Helpers\PaymentHelper::format($amountToPay) . ' for Invoice #' . $invoice->invoice_number . ' has been submitted and is pending verification.',
                'type' => 'invoice',
                'is_read' => false
            ]);

            $methodName = $gateway === 'bank_transfer' ? 'Bank Wire Transfer' : 'Cryptocurrency';
            return back()->with('success', 'Payment confirmation of ' . \App\Helpers\PaymentHelper::format($amountToPay) . ' submitted for ' . $methodName . '. Our finance team will verify the transaction and update your account shortly.');
        } else {
            if ($isFullPayment) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);

                if ($invoice->milestone_id) {
                    Milestone::where('id', $invoice->milestone_id)->update(['status' => 'approved']);
                }
            } else {
                $remainingBalance = $invoice->amount - $amountToPay;
                $invoice->update([
                    'amount' => $remainingBalance,
                    'status' => $remainingBalance <= 0 ? 'paid' : 'unpaid',
                    'paid_at' => $remainingBalance <= 0 ? now() : null
                ]);
            }

            \App\Models\AuditLog::create([
                'user_id' => $request->user()->id,
                'event_type' => 'payment_success',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode(['invoice_id' => $invoice->id, 'amount' => $amountToPay, 'gateway' => $gateway])
            ]);

            \App\Models\UserNotification::create([
                'user_id' => $request->user()->id,
                'title' => 'Invoice Paid Successfully',
                'message' => 'Your payment of ' . \App\Helpers\PaymentHelper::format($amountToPay) . ' for Invoice #' . $invoice->invoice_number . ' was processed successfully.',
                'type' => 'invoice',
                'is_read' => false
            ]);

            $gatewayLabel = ucfirst(str_replace('_', ' ', $gateway));
            return back()->with('success', 'Invoice #' . $invoice->invoice_number . ' payment of ' . \App\Helpers\PaymentHelper::format($amountToPay) . ' processed successfully via ' . $gatewayLabel . ' (Mock Integration).');
        }
    }

    public function uploadFile(Request $request, $id)
    {
        $project = Project::where('client_id', $request->user()->id)->findOrFail($id);
        
        $request->validate([
            'project_file' => 'required|file|max:15360', // 15MB
            'folder' => 'required|string|in:contracts,assets,deliverables,reports,backups',
        ]);

        if ($request->hasFile('project_file')) {
            $file = $request->file('project_file');
            $filename = $file->getClientOriginalName();
            
            $destinationPath = public_path('uploads');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $filepath = 'uploads/' . time() . '_' . $filename;
            $file->move($destinationPath, time() . '_' . $filename);

            // Version control logic
            $existingFile = ProjectFile::where('project_id', $project->id)
                ->where('filename', $filename)
                ->orderBy('version', 'desc')
                ->first();
                
            $version = $existingFile ? $existingFile->version + 1 : 1;

            ProjectFile::create([
                'project_id' => $project->id,
                'uploaded_by' => $request->user()->id,
                'filename' => $filename,
                'filepath' => $filepath,
                'file_size' => $file->getSize() ?? 0,
                'folder' => $request->input('folder'),
                'version' => $version,
            ]);

            return back()->with('success', 'File "' . $filename . '" (v' . $version . ') uploaded and verified successfully.');
        }

        return back()->with('error', 'File failed validation.');
    }

    public function downloadFile(Request $request, $id)
    {
        $projectFile = ProjectFile::findOrFail($id);
        
        // Security check
        Project::where('client_id', $request->user()->id)->findOrFail($projectFile->project_id);
        
        $projectFile->increment('download_count');
        
        $fullPath = public_path($projectFile->filepath);
        if (file_exists($fullPath)) {
            return response()->download($fullPath, $projectFile->filename);
        }
        
        return back()->with('error', 'The file does not exist on our servers.');
    }

    public function signAgreement(Request $request, $id)
    {
        $contract = Contract::where('client_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'signature_name' => 'required|string|max:150',
        ]);

        $contract->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_data' => $request->input('signature_name'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Update corresponding project status
        if ($contract->project_id) {
            $project = Project::find($contract->project_id);
            if ($project) {
                $project->update([
                    'agreement_signed_at' => now(),
                    'status' => 'planning'
                ]);
            }
        }

        // Add to security log
        \App\Models\AuditLog::create([
            'user_id' => $request->user()->id,
            'event_type' => 'passkey_registered', // using role success
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => json_encode(['contract_id' => $contract->id, 'action' => 'e_signed'])
        ]);

        \App\Models\UserNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Digital Contract Signed',
            'message' => 'You have successfully signed the Service Agreement: "' . $contract->title . '".',
            'type' => 'project',
            'is_read' => false
        ]);

        return back()->with('success', 'Digital Agreement E-Signed successfully. Progress state logged.');
    }

    public function storeServiceRequest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'service_type' => 'required|string',
            'description' => 'required|string',
            'budget_range' => 'required|string',
            'deadline' => 'required|date|after:today',
        ]);

        $serviceType = $request->input('service_type');
        $budgetRange = $request->input('budget_range');
        
        // Generate AI Recommendations
        $aiPromptText = "### Diwebs Intelligent Proposal System\n\n";
        $aiPromptText .= "Based on your project request for **{$serviceType}**, here is our automatic scope estimation:\n\n";
        $aiPromptText .= "**1. Recommended Core Architecture:**\n";
        switch ($serviceType) {
            case 'Website Development':
                $aiPromptText .= "- **Tech Stack:** Laravel 11 MVC + Blade Templates + Tailwind CSS + Alpine.js\n";
                $aiPromptText .= "- **Hosting & Deployment:** AWS Elastic Beanstalk + Cloudflare Edge CDN\n";
                break;
            case 'Mobile App Development':
                $aiPromptText .= "- **Tech Stack:** Flutter / Dart Hybrid SDK (Targeting Android SDK 34 & iOS Swift targets)\n";
                $aiPromptText .= "- **Backend API Integration:** RESTful endpoints built on Laravel API resource controllers\n";
                break;
            case 'SaaS Platform':
                $aiPromptText .= "- **Tech Stack:** Vite React SPA + Node.js Microservices + PostgreSQL\n";
                $aiPromptText .= "- **Cloud Infrastructure:** Kubernetes (EKS) container cluster auto-scaling\n";
                break;
            case 'AI Automation':
                $aiPromptText .= "- **Tech Stack:** Python + FastAPI + LangChain + Gemini-Pro / GPT-4o\n";
                break;
            default:
                $aiPromptText .= "- **Tech Stack:** Tailwind CSS + Vanilla JS Frontend + SQLite/MySQL DB\n";
        }
        $aiPromptText .= "\n**2. Proposed Delivery Timeline:** 6 to 10 weeks divided across Agile sprints.\n\n";
        $aiPromptText .= "**3. Critical Security Enhancements:**\n";
        $aiPromptText .= "- Automatic 2FA/MFA authentication setups\n";
        $aiPromptText .= "- CSRF, SQLi filtration shields, and daily database rollbacks\n\n";
        $aiPromptText .= "**4. Recommended Upgrades:** We highly advise subscribing to the *Diwebs Elite Care SLA Plan* for 99.98% runtime monitoring and weekly threat patch updates.";

        $serviceRequest = ServiceRequest::create([
            'client_id' => $request->user()->id,
            'title' => $request->input('title'),
            'service_type' => $serviceType,
            'description' => $request->input('description'),
            'budget_range' => $budgetRange,
            'deadline' => $request->input('deadline'),
            'status' => 'submitted',
            'ai_recommendations' => $aiPromptText,
        ]);

        \App\Models\AdminNotification::create([
            'type' => 'service_request',
            'title' => 'New Service Request: ' . $request->input('title'),
            'details' => [
                'client_name' => $request->user()->name,
                'client_email' => $request->user()->email,
                'title' => $request->input('title'),
                'service_type' => $serviceType,
                'budget_range' => $budgetRange,
                'deadline' => $request->input('deadline'),
                'description' => $request->input('description'),
            ]
        ]);

        // Auto lead registration in CRM
        Lead::create([
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'phone' => '+2348000000000',
            'company' => 'Enterprise Workspace',
            'service_needed' => $serviceType,
            'message' => '[Auto-CRM Lead] New service requested from client portal dashboard. Budget range: ' . $budgetRange . '. Description: ' . $request->input('description'),
            'status' => 'new'
        ]);

        // Create support ticket as well
        Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => '[Service Request Alert] ' . $request->input('title'),
            'message' => 'Service request submitted for review: ' . $serviceType . ' (Budget: ' . $budgetRange . '). Proposal pending review.',
            'status' => 'open',
            'priority' => 'low'
        ]);

        \App\Models\UserNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Service Request Submitted',
            'message' => 'Your service request for "' . $request->input('title') . '" has been submitted. Technical scope estimation has been generated.',
            'type' => 'project',
            'is_read' => false
        ]);

        return redirect()->route('portal.checkout', $serviceRequest->id)->with('success', 'Service request submitted successfully. Please select your payment method to kickstart your project!');
    }

    public function milestoneAction(Request $request, $id)
    {
        $milestone = Milestone::findOrFail($id);
        Project::where('client_id', $request->user()->id)->findOrFail($milestone->project_id);

        $request->validate([
            'action' => 'required|in:approved,rejected,revision_requested',
            'comments' => 'nullable|string'
        ]);

        $action = $request->input('action');
        $milestone->update([
            'status' => $action === 'approved' ? 'approved' : ($action === 'rejected' ? 'pending' : 'working')
        ]);

        MilestoneLog::create([
            'milestone_id' => $milestone->id,
            'user_id' => $request->user()->id,
            'action' => $action,
            'comments' => $request->input('comments')
        ]);

        return back()->with('success', 'Milestone action ' . ucfirst(str_replace('_', ' ', $action)) . ' has been recorded and logged.');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'project_id' => 'nullable|uuid',
            'message' => 'nullable|string',
            'department' => 'required|string|in:pm,support,finance,technical',
            'file_attachment' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('uploads/chat');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $filePath = 'uploads/chat/' . $filename;
        }

        $message = Message::create([
            'project_id' => $request->input('project_id'),
            'sender_id' => $request->user()->id,
            'message' => $request->input('message'),
            'file_path' => $filePath,
            'department' => $request->input('department'),
            'is_read' => false
        ]);

        // Auto simulation of support replies
        if ($request->filled('message')) {
            $text = strtolower($request->input('message'));
            $reply = "";

            if (str_contains($text, 'hello') || str_contains($text, 'hi')) {
                $reply = "Hello! Diwebs " . strtoupper($request->input('department')) . " Desk is online. We have received your query.";
            } elseif (str_contains($text, 'invoice') || str_contains($text, 'pay') || str_contains($text, 'billing')) {
                $reply = "For payment and billing issues, our accounting team generally verifies transactions within 1-2 hours. Outstanding items can be paid in the Payments panel.";
            } elseif (str_contains($text, 'progress') || str_contains($text, 'update') || str_contains($text, 'status')) {
                $reply = "I will check with the development team lead regarding the current status and update you shortly.";
            } elseif (str_contains($text, 'thank') || str_contains($text, 'ok')) {
                $reply = "You are welcome! Let us know if you need anything else.";
            }

            if ($reply) {
                $adminUser = User::where('role', 'super_admin')->first();
                Message::create([
                    'project_id' => $request->input('project_id'),
                    'sender_id' => $adminUser ? $adminUser->id : 1,
                    'message' => $reply,
                    'department' => $request->input('department'),
                    'is_read' => false
                ]);
            }
        }

        return back()->with('success', 'Message posted.');
    }

    public function getMessages(Request $request)
    {
        $request->validate([
            'project_id' => 'nullable|uuid',
            'department' => 'required|string',
        ]);

        $messages = Message::with('sender')
            ->where('department', $request->input('department'))
            ->where(function($q) use ($request) {
                if ($request->filled('project_id')) {
                    $q->where('project_id', $request->input('project_id'));
                }
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sender_name' => $msg->sender->name,
                    'is_client' => $msg->sender->role === 'client',
                    'message' => $msg->message,
                    'file_path' => $msg->file_path ? '/' . $msg->file_path : null,
                    'file_name' => $msg->file_path ? basename($msg->file_path) : null,
                    'created_at' => $msg->created_at->format('M d, H:i')
                ];
            });

        return response()->json($messages);
    }

    public function inviteTeamMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:team_access,email',
            'role' => 'required|string|in:manager,reviewer,finance_viewer',
            'permissions' => 'nullable|array'
        ]);

        TeamAccess::create([
            'client_id' => $request->user()->id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'project_permissions' => $request->input('permissions') ?? [],
        ]);

        return back()->with('success', 'Team workspace invite successfully created.');
    }

    public function removeTeamMember(Request $request, $id)
    {
        $team = TeamAccess::where('client_id', $request->user()->id)->findOrFail($id);
        $team->delete();
        return back()->with('success', 'Invited team member access has been revoked.');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'notification_channels' => 'nullable|array'
        ]);

        $user = $request->user();
        $user->update([
            'name' => $request->input('name'),
        ]);

        cache(['client_company_' . $user->id => $request->input('company_name')]);
        cache(['client_notifications_' . $user->id => $request->input('notification_channels')]);

        return back()->with('success', 'Client profile and workspace settings updated.');
    }

    public function createTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical'
        ]);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'priority' => $request->input('priority'),
            'status' => 'open'
        ]);

        \App\Models\AdminNotification::create([
            'type' => 'support_ticket',
            'title' => 'New Support Ticket: ' . $request->input('subject'),
            'details' => [
                'client_name' => $request->user()->name,
                'client_email' => $request->user()->email,
                'subject' => $request->input('subject'),
                'message' => $request->input('message'),
                'priority' => $request->input('priority'),
            ]
        ]);

        \App\Models\UserNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Support Ticket Opened',
            'message' => 'Ticket #' . $ticket->id . ' ("' . $request->input('subject') . '") has been successfully submitted to the Help Desk.',
            'type' => 'system',
            'is_read' => false
        ]);

        return back()->with('success', 'Support ticket submitted to support help desk.');
    }

    public function askAiAssistant(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        
        $query = strtolower($request->input('message'));
        $user = $request->user();

        // Database context telemetry
        $projects = Project::with(['milestones', 'invoices'])->where('client_id', $user->id)->get();
        $tickets = Ticket::where('user_id', $user->id)->get();
        $unpaidInvoices = Invoice::where('client_id', $user->id)->where('status', 'unpaid')->get();

        if (str_contains($query, 'progress') || str_contains($query, 'project') || str_contains($query, 'status')) {
            if ($projects->isEmpty()) {
                $response = "You currently do not have any active software development projects with Diwebs. Head over to the 'Service Requests' tab to initiate a proposal.";
            } else {
                $response = "### Project Telemetry Analysis:\n\n";
                foreach ($projects as $project) {
                    $total = $project->milestones->count();
                    $approved = $project->milestones->where('status', 'approved')->count();
                    $pct = $total > 0 ? round(($approved / $total) * 100) : 0;
                    $response .= "• **{$project->title}**: Current phase is **" . strtoupper($project->status) . "**. Task completion is **{$pct}%** ({$approved} of {$total} milestones signed off).\n";
                    $workingMilestones = $project->milestones->where('status', 'working');
                    if ($workingMilestones->isNotEmpty()) {
                        $response .= "  - In Active Sprint: *" . implode(', ', $workingMilestones->pluck('title')->toArray()) . "*\n";
                    }
                }
            }
        } elseif (str_contains($query, 'invoice') || str_contains($query, 'payment') || str_contains($query, 'billing') || str_contains($query, 'cost')) {
            if ($unpaidInvoices->isEmpty()) {
                $response = "All invoices for your projects are settled. You have zero outstanding balances.";
            } else {
                $totalUnpaid = $unpaidInvoices->sum('amount');
                $response = "You have **" . $unpaidInvoices->count() . "** pending invoice(s) totaling **" . \App\Helpers\PaymentHelper::format($totalUnpaid) . "**:\n\n";
                foreach ($unpaidInvoices as $inv) {
                    $response .= "- **Invoice #{$inv->invoice_number}**: " . \App\Helpers\PaymentHelper::format($inv->amount) . " (Due: " . $inv->due_date->format('M d, Y') . ")\n";
                }
                $response .= "\nYou can process full, installment, or partial payments under the *Invoices & Payments* tab.";
            }
        } elseif (str_contains($query, 'ticket') || str_contains($query, 'support') || str_contains($query, 'bug') || str_contains($query, 'help')) {
            if ($tickets->isEmpty()) {
                $response = "You have no active technical support tickets.";
            } else {
                $response = "Here is the status of your tickets:\n\n";
                foreach ($tickets as $ticket) {
                    $response .= "• **[Ticket #{$ticket->id}]** *{$ticket->subject}* — Status: **" . strtoupper($ticket->status) . "** (Priority: " . strtoupper($ticket->priority) . ")\n";
                }
            }
        } elseif (str_contains($query, 'recommend') || str_contains($query, 'upgrade') || str_contains($query, 'service')) {
            $response = "Based on your technical telemetry, here are recommended enhancements:\n\n" .
                         "1. **Fast Edge CDN & Image Optimization Cache:** Speeds up asset loading globally.\n" .
                         "2. **Cybersecurity Penetration Audit:** Highly suggested for institutional portals.\n" .
                         "3. **Automatic Cloud Database Multi-Zone Backups:** Safe redundancy replication.\n\n" .
                         "Submit service requests under the *Service Requests* tab.";
        } else {
            $response = "Hello! I am your **Diwebs Client AI Assistant**. I analyze your live project telemetry to answer questions instantly.\n\n" .
                         "Try asking me:\n" .
                         "- *How is my project progressing?*\n" .
                         "- *Do I have any pending invoices or balances?*\n" .
                         "- *What is the status of my support tickets?*\n" .
                         "- *Can you recommend any upgrades?*";
        }

        return response()->json([
            'message' => $response
        ]);
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:1',
            'service_type' => 'required|string'
        ]);

        Project::create([
            'client_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'budget' => $request->budget,
            'service_type' => $request->service_type,
            'status' => 'initiated',
            'is_validated' => false,
            'success_rate' => 0
        ]);

        \App\Models\AdminNotification::create([
            'type' => 'project_create',
            'title' => 'New Client Project Proposal: ' . $request->title,
            'details' => [
                'client_name' => $request->user()->name,
                'client_email' => $request->user()->email,
                'title' => $request->title,
                'service_type' => $request->service_type,
                'budget' => $request->budget,
                'description' => $request->description,
            ]
        ]);

        \App\Models\UserNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Project Proposal Initiated',
            'message' => 'Your project proposal "' . $request->title . '" has been received and submitted for validation.',
            'type' => 'project',
            'is_read' => false
        ]);

        return back()->with('success', 'Project request created successfully and submitted for admin validation.');
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
            'company_name' => 'nullable|string|max:255'
        ]);

        $user = $request->user();

        // Check if there is cached or saved company name
        $companyName = $request->input('company_name');
        if (empty($companyName)) {
            $companyName = cache('client_company_' . $user->id);
        }

        \App\Models\Review::create([
            'user_id' => $user->id,
            'client_name' => $user->name,
            'company_name' => $companyName,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'status' => 'pending'
        ]);

        \App\Models\AdminNotification::create([
            'type' => 'customer_review',
            'title' => 'New Customer Review Submitted by ' . $user->name,
            'details' => [
                'client_name' => $user->name,
                'email' => $user->email,
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
                'company_name' => $companyName
            ]
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted and is pending administrator moderation.');
    }

    public function submitPartnershipRequest(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'partnership_type' => 'required|string|in:Technology Partner,Co-Marketing Partner,Referral Partner,Reseller Partner,Strategic Alliance,Other',
            'business_description' => 'required|string|min:10',
            'synergy_goals' => 'required|string|min:10',
            'expected_contribution' => 'required|string|min:10',
            'signed_name' => 'required|string|max:150',
            'agree_terms' => 'required|accepted'
        ]);

        $user = $request->user();

        // Check if there is already a partnership request
        $existing = PartnershipRequest::where('user_id', $user->id)->first();
        if ($existing) {
            return back()->with('error', 'You have already submitted a partnership request.');
        }

        // Generate the PDF
        $date = now()->format('F d, Y');
        $company_name = $request->input('company_name');
        $website = $request->input('website');
        $partnership_type = $request->input('partnership_type');
        $signed_name = $request->input('signed_name');

        // Compile HTML to PDF using Dompdf
        $pdf = Pdf::loadView('pdf.partnership_agreement', [
            'date' => $date,
            'company_name' => $company_name,
            'website' => $website,
            'partnership_type' => $partnership_type,
            'signed_name' => $signed_name,
        ]);

        $pdfOutput = $pdf->output();

        // Save PDF to public folder
        $filename = 'partnership_' . $user->id . '_' . time() . '.pdf';
        $destinationPath = public_path('uploads/partnerships');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $filePath = 'uploads/partnerships/' . $filename;
        file_put_contents(public_path($filePath), $pdfOutput);

        // Store request in database
        $partnershipRequest = PartnershipRequest::create([
            'user_id' => $user->id,
            'company_name' => $company_name,
            'website' => $website,
            'partnership_type' => $partnership_type,
            'business_description' => $request->input('business_description'),
            'synergy_goals' => $request->input('synergy_goals'),
            'expected_contribution' => $request->input('expected_contribution'),
            'signed_name' => $signed_name,
            'signed_at' => now(),
            'status' => 'pending',
            'pdf_path' => $filePath,
        ]);

        // Create notification for admin
        \App\Models\AdminNotification::create([
            'type' => 'partnership_request',
            'title' => 'New Partnership Proposal from ' . $company_name,
            'details' => [
                'client_name' => $user->name,
                'client_email' => $user->email,
                'company_name' => $company_name,
                'partnership_type' => $partnership_type,
                'signed_name' => $signed_name,
                'pdf_path' => $filePath,
            ]
        ]);

        // Add to security/audit logs
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'event_type' => 'partnership_requested',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => json_encode(['partnership_id' => $partnershipRequest->id, 'company_name' => $company_name])
        ]);

        // Email PDF to the client
        try {
            $toEmail = $user->email;
            $subject = 'Your Diwebs Tech Agency Partnership Agreement';
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($toEmail, $subject, $pdfOutput, $company_name) {
                $message->to($toEmail)
                    ->subject($subject)
                    ->html(
                        "<div style='font-family:sans-serif;max-width:600px;margin:auto;padding:25px;border:1px solid #1E2125;background-color:#1E2125;color:#ffffff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.3);'>" .
                        "<div style='text-align:center;margin-bottom:20px;'><img src='https://diwebstechagency.website/images/brand/diwebs-logo.svg' alt='Diwebs Logo' style='height:45px;' /></div>" .
                        "<h2 style='color:#06b6d4;border-bottom:1px solid #0d9488;padding-bottom:10px;text-align:center;margin-top:0;'>Partnership Request Submitted Successfully</h2>" .
                        "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Hello,</p>" .
                        "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Thank you for submitting a strategic partnership proposal to <strong>Diwebs Tech Agency</strong> for <strong>{$company_name}</strong>.</p>" .
                        "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>We have received your request and have attached a digitally compiled copy of your signed **Terms of Partnership Agreement** to this email. You can download and print it for your records.</p>" .
                        "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Our Director of Partner Relations will review your proposal and get back to you with the next steps shortly.</p>" .
                        "<p style='font-size:11px;color:#94a3b8;margin-top:40px;border-top:1px solid #334155;padding-top:15px;text-align:center;'>This is an automated operational email from the Diwebs Partner Program.</p>" .
                        "</div>"
                    )
                    ->attachData($pdfOutput, 'Diwebs_Partnership_Agreement_' . str_replace(' ', '_', $company_name) . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });
        } catch (\Exception $e) {
            logger()->error("Failed to email partnership agreement PDF to user: " . $e->getMessage());
        }

        return back()->with('success', 'Your partnership request has been submitted successfully! The signed agreement PDF was sent to your email address and is pending review.');
    }

    public function showCheckout(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::where('client_id', $request->user()->id)->findOrFail($id);

        // Extract budget amount
        $budgetRange = $serviceRequest->budget_range;
        preg_match('/\d[\d,.]*/', $budgetRange, $matches);
        $amount = isset($matches[0]) ? (float)str_replace(',', '', $matches[0]) : 500.00;

        // Gateways enabled check
        $gateways = [
            'stripe' => [
                'name' => 'Stripe',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_stripe_enabled', true),
                'icon' => '/images/brand/stripe.svg'
            ],
            'paystack' => [
                'name' => 'Paystack',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_paystack_enabled', false),
                'icon' => '/images/brand/paystack.svg'
            ],
            'flutterwave' => [
                'name' => 'Flutterwave',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_flw_enabled', false),
                'icon' => '/images/brand/flutterwave.svg'
            ],
            'paypal' => [
                'name' => 'PayPal',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_paypal_enabled', false),
                'icon' => '/images/brand/paypal.svg'
            ],
            'razorpay' => [
                'name' => 'Razorpay',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_razorpay_enabled', false),
                'icon' => '/images/brand/razorpay.svg'
            ],
            'coinbase' => [
                'name' => 'Coinbase Commerce',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_coinbase_enabled', false),
                'icon' => '/images/brand/coinbase.svg'
            ],
            'bank_transfer' => [
                'name' => 'Bank Wire Transfer',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_bank_enabled', false),
                'icon' => '/images/brand/bank.svg'
            ],
            'crypto' => [
                'name' => 'Bitcoin / USDT',
                'enabled' => \App\Helpers\SettingsHelper::get('payment_crypto_enabled', false),
                'icon' => '/images/brand/bitcoin.svg'
            ]
        ];

        return view('portal.checkout', compact('serviceRequest', 'amount', 'gateways'));
    }

    public function processCheckoutPay(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::where('client_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'payment_method' => 'required|in:stripe,paystack,flutterwave,paypal,razorpay,coinbase,bank_transfer,crypto',
            'payment_proof' => 'required_if:payment_method,crypto,bank_transfer|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
            'payment_txid' => 'required_if:payment_method,crypto,bank_transfer|string|max:255'
        ]);

        // Parse amount
        $budgetRange = $serviceRequest->budget_range;
        preg_match('/\d[\d,.]*/', $budgetRange, $matches);
        $amount = isset($matches[0]) ? (float)str_replace(',', '', $matches[0]) : 500.00;

        $method = $request->input('payment_method');

        if (in_array($method, ['crypto', 'bank_transfer'])) {
            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = time() . '_' . $file->getClientOriginalName();
                $destination = public_path('uploads/proofs');
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }
                $file->move($destination, $filename);
                $proofPath = 'uploads/proofs/' . $filename;
            }

            $serviceRequest->update([
                'payment_method' => $method,
                'payment_status' => 'pending',
                'payment_amount' => $amount,
                'payment_proof' => $proofPath,
                'payment_txid' => $request->input('payment_txid'),
                'status' => 'pending_verification'
            ]);

            \App\Models\AuditLog::create([
                'user_id' => $request->user()->id,
                'event_type' => 'payment_submitted',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode(['service_request_id' => $serviceRequest->id, 'amount' => $amount, 'method' => $method])
            ]);

            \App\Models\UserNotification::create([
                'user_id' => $request->user()->id,
                'title' => 'Payment Proof Submitted',
                'message' => 'Your payment proof for "' . $serviceRequest->title . '" has been submitted for verification.',
                'type' => 'invoice',
                'is_read' => false
            ]);

            \App\Models\AdminNotification::create([
                'type' => 'payment_verification',
                'title' => 'New Payment Proof: ' . $serviceRequest->title,
                'details' => [
                    'client_name' => $request->user()->name,
                    'service_request_title' => $serviceRequest->title,
                    'payment_method' => $method,
                    'amount' => $amount,
                    'txid' => $request->input('payment_txid')
                ]
            ]);

            return redirect()->route('portal.dashboard')->with('success', 'Your payment proof has been submitted successfully. Our billing team will verify it and activate your project shortly!');
        } else {
            $serviceRequest->update([
                'payment_method' => $method,
                'payment_status' => 'paid',
                'payment_amount' => $amount,
                'status' => 'approved'
            ]);

            $project = Project::create([
                'id' => (string) Str::uuid(),
                'client_id' => $serviceRequest->client_id,
                'title' => $serviceRequest->title,
                'description' => $serviceRequest->description,
                'status' => 'planning',
                'budget' => $amount,
                'is_validated' => true,
                'agreement_signed_at' => null
            ]);

            $milestone = Milestone::create([
                'project_id' => $project->id,
                'title' => 'Initial Project Kickoff',
                'description' => 'Initial sprint milestone set up on project checkout.',
                'due_date' => now()->addDays(15),
                'status' => 'pending',
                'amount' => $amount
            ]);

            $invoice = Invoice::create([
                'project_id' => $project->id,
                'milestone_id' => $milestone->id,
                'client_id' => $serviceRequest->client_id,
                'amount' => $amount,
                'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(Str::random(5)),
                'status' => 'paid',
                'due_date' => now()->addDays(15),
                'paid_at' => now()
            ]);

            Contract::create([
                'project_id' => $project->id,
                'client_id' => $serviceRequest->client_id,
                'title' => 'Service Agreement: ' . $project->title,
                'content' => "This Service Agreement is entered into between Diwebs Tech Agency and the client. Project title: {$project->title}. Budget: " . \App\Helpers\PaymentHelper::format($project->budget) . "\n\nScope Description:\n{$project->description}",
                'status' => 'pending_signature'
            ]);

            \App\Models\AuditLog::create([
                'user_id' => $request->user()->id,
                'event_type' => 'payment_success',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode(['service_request_id' => $serviceRequest->id, 'project_id' => $project->id, 'amount' => $amount, 'method' => $method])
            ]);

            \App\Models\UserNotification::create([
                'user_id' => $request->user()->id,
                'title' => 'Payment Completed & Project Activated',
                'message' => 'Your payment for "' . $serviceRequest->title . '" was successful! Project is now activated.',
                'type' => 'project',
                'is_read' => false
            ]);

            return redirect()->route('portal.dashboard')->with('success', 'Payment successful! Your project has been initialized. Please head to the "Digital Contracts" tab to sign your Service Agreement contract.');
        }
    }
}
