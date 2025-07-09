<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewContactSubmissionAdminMail;
use App\Models\Setting; // To get admin email
use Illuminate\Support\Facades\Auth; // Added

class ContactSubmissionController extends Controller
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
            'service' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $submission = ContactSubmission::create($validatedData);

        // Send email notification to admin
        $adminEmail = Setting::getValue('admin_notification_email', config('mail.admin_address'));
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new NewContactSubmissionAdminMail($submission));
            } catch (\Exception $e) {
                \Log::error("Failed to send new contact submission email to admin: " . $e->getMessage());
            }
        }

        // For API response or redirect
        // return redirect()->back()->with('success', 'Your message has been sent successfully!');
        return response()->json([
            'message' => 'Contact form submitted successfully!',
            'submission' => $submission
        ], 201); // Placeholder
    }

    // === ADMIN FACING METHODS ===

    /**
     * Display a listing of the resource for Admin.
     */
    public function adminIndex()
    {
        $submissions = ContactSubmission::latest()->paginate(15);
        // return view('admin.contacts.index', compact('submissions'));
        return response()->json([
            'message' => 'Contact submission listing (admin)',
            'submissions' => $submissions
        ]); // Placeholder
    }

    /**
     * Display the specified resource for Admin.
     */
    public function adminShow(ContactSubmission $contactSubmission)
    {
        // return view('admin.contacts.show', compact('contactSubmission'));
        return response()->json([
            'message' => 'Contact submission detail (admin)',
            'submission' => $contactSubmission
        ]); // Placeholder
    }

    /**
     * Remove the specified resource from storage for Admin.
     */
    public function adminDestroy(ContactSubmission $contactSubmission)
    {
        $submissionId = $contactSubmission->id;
        $submissionAttributes = $contactSubmission->toArray();
        $contactSubmission->delete();

        ActivityLog::record(
            action: 'deleted_contact_submission',
            loggable: $contactSubmission, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted contact submission #{$submissionId} (From: {$submissionAttributes['name']}).",
            properties: ['attributes' => $submissionAttributes]
        );

        // return redirect()->route('admin.contacts.index')->with('success', 'Contact submission deleted successfully.');
        return response()->json([
            'message' => 'Contact submission deleted (admin)'
        ], 200); // Placeholder
    }
}
