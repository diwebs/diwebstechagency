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
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        // 1. Fetch related project items
        $projects = Project::with(['milestones.logs', 'invoices', 'client'])->where('client_id', $user->id)->get();
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
            'pendingBonus'
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

        return back()->with('success', 'Service request submitted successfully. Automated proposal details loaded in requests log.');
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

        Ticket::create([
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
            'status' => 'approved' // auto-approved as per implementation plan
        ]);

        return back()->with('success', 'Thank you! Your review and trust rating have been submitted and are now live on the homepage.');
    }
}
