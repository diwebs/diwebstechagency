<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ], 422);
        }

        // Simulating email registration in CRM / newsletter list
        // You could also log this to dynamic mail drivers or newsletter models
        \App\Models\AdminNotification::create([
            'type' => 'newsletter_subscribe',
            'title' => 'New Newsletter Subscription',
            'details' => ['email' => $request->email]
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing! Welcome to the future of technology.'
        ]);
    }
}
