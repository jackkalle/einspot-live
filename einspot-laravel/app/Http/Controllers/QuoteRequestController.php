<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewQuoteRequestAdminMail;
use App\Models\Setting; // To get admin email
use Illuminate\Support\Facades\Auth; // Added

class QuoteRequestController extends Controller
{
    /**
     * Store a newly created resource in storage. (Public facing)
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'service_of_interest' => 'nullable|string|max:255',
            'project_description' => 'required|string',
            'estimated_budget' => 'nullable|string|max:255',
            'timeline' => 'nullable|string|max:255',
            'product_service_name' => 'nullable|string|max:255', // From WhatsApp prefill
        ]);

        $quoteRequest = QuoteRequest::create($validatedData);

        // Send email notification to admin
        $adminEmail = Setting::getValue('admin_notification_email', config('mail.admin_address')); // Fallback to a config value
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new NewQuoteRequestAdminMail($quoteRequest));
            } catch (\Exception $e) {
                // Log error, but don't fail the user's request
                \Log::error("Failed to send new quote request email to admin: " . $e->getMessage());
            }
        }

        // For API response if used by AJAX, or redirect with message for traditional forms
        // return redirect()->back()->with('success', 'Your quote request has been submitted successfully!');
        return response()->json([
            'message' => 'Quote request submitted successfully!',
            'quote_request' => $quoteRequest
        ], 201); // Placeholder
    }

    // === ADMIN FACING METHODS ===

    /**
     * Display a listing of the resource for Admin.
     */
    public function adminIndex()
    {
        $quoteRequests = QuoteRequest::latest()->paginate(15);
        // return view('admin.quotes.index', compact('quoteRequests'));
        return response()->json([
            'message' => 'Quote request listing (admin)',
            'quote_requests' => $quoteRequests
        ]); // Placeholder
    }

    /**
     * Display the specified resource for Admin.
     */
    public function adminShow(QuoteRequest $quoteRequest)
    {
        // return view('admin.quotes.show', compact('quoteRequest'));
        return response()->json([
            'message' => 'Quote request detail (admin)',
            'quote_request' => $quoteRequest
        ]); // Placeholder
    }

    /**
     * Update the status of the specified quote request (Admin).
     */
    public function adminUpdateStatus(Request $request, QuoteRequest $quoteRequest)
    {
        $request->validate(['status' => 'required|string|max:255']);

        $originalStatus = $quoteRequest->status;
        $quoteRequest->update(['status' => $request->status]);

        ActivityLog::record(
            action: 'updated_quote_request_status',
            loggable: $quoteRequest,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated quote request #{$quoteRequest->id} status from '{$originalStatus}' to '{$quoteRequest->status}'.",
            properties: ['old_status' => $originalStatus, 'new_status' => $quoteRequest->status]
        );

        // return redirect()->route('admin.quotes.index')->with('success', 'Quote request status updated.');
        return response()->json([
            'message' => 'Quote request status updated (admin)',
            'quote_request' => $quoteRequest
        ]); // Placeholder
    }


    /**
     * Remove the specified resource from storage for Admin.
     */
    public function adminDestroy(QuoteRequest $quoteRequest)
    {
        $quoteRequestId = $quoteRequest->id;
        $quoteRequestAttributes = $quoteRequest->toArray();
        $quoteRequest->delete();

        ActivityLog::record(
            action: 'deleted_quote_request',
            loggable: $quoteRequest, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted quote request #{$quoteRequestId} (Name: {$quoteRequestAttributes['name']}).",
            properties: ['attributes' => $quoteRequestAttributes]
        );

        // return redirect()->route('admin.quotes.index')->with('success', 'Quote request deleted successfully.');
        return response()->json([
            'message' => 'Quote request deleted (admin)'
        ], 200); // Placeholder
    }
}
