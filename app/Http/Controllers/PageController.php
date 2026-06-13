<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function solutions()
    {
        return view('pages.solutions');
    }

    public function portfolio()
    {
        return view('pages.portfolio');
    }

    public function caseStudies()
    {
        return view('pages.case-studies');
    }

    public function blog()
    {
        return view('pages.blog');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitLead(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'service_needed' => 'required|string',
            'message' => 'required|string'
        ]);

        Lead::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your project details have been captured successfully. Our team will contact you shortly!']);
        }

        return back()->with('success', 'Your inquiry has been submitted successfully. A technology specialist will contact you shortly.');
    }
}
