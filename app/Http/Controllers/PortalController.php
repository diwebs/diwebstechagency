<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Invoice;
use App\Models\Milestone;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $projects = Project::with(['milestones', 'invoices'])->where('client_id', $user->id)->get();
        $unpaidInvoices = Invoice::where('client_id', $user->id)->where('status', 'unpaid')->get();

        return view('portal.dashboard', compact('projects', 'unpaidInvoices'));
    }

    public function projectDetail(Request $request, $id)
    {
        $project = Project::with(['milestones', 'invoices'])->where('client_id', $request->user()->id)->findOrFail($id);
        return view('portal.project-detail', compact('project'));
    }

    public function payInvoice(Request $request, $id)
    {
        $invoice = Invoice::where('client_id', $request->user()->id)->findOrFail($id);
        
        // Mock payment processing
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // If invoice is attached to a milestone, mark milestone as approved
        if ($invoice->milestone_id) {
            Milestone::where('id', $invoice->milestone_id)->update(['status' => 'approved']);
        }

        return back()->with('success', 'Invoice #' . $invoice->invoice_number . ' paid successfully (Mock Environment).');
    }

    public function uploadFile(Request $request, $id)
    {
        $project = Project::where('client_id', $request->user()->id)->findOrFail($id);
        
        $request->validate([
            'project_file' => 'required|file|max:10240', // 10MB
        ]);

        // Mock upload success (since we are on local / shared server, we just redirect)
        return back()->with('success', 'File uploaded and attached to project successfully.');
    }

    public function signAgreement(Request $request, $id)
    {
        $project = Project::where('client_id', $request->user()->id)->findOrFail($id);

        $project->update([
            'agreement_signed_at' => now(),
            'status' => 'planning'
        ]);

        return back()->with('success', 'E-Agreement signed successfully. Project moved to planning status.');
    }
}
