<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added

class NewsletterSubscriptionController extends Controller
{
    /**
     * Store a newly created resource in storage. (Public facing)
     */
    public function subscribe(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255|unique:newsletter_subscriptions,email',
        ]);

        $subscription = NewsletterSubscription::create([
            'email' => $validatedData['email'],
            'is_active' => true,
        ]);

        // Optionally, send a confirmation email to the user
        // Or add to a mailing list service

        // return redirect()->back()->with('success', 'You have successfully subscribed to our newsletter!');
        return response()->json([
            'message' => 'Successfully subscribed to the newsletter!',
            'subscription' => $subscription
        ], 201); // Placeholder
    }

    // === ADMIN FACING METHODS ===

    /**
     * Display a listing of the resource for Admin.
     */
    public function adminIndex()
    {
        $subscriptions = NewsletterSubscription::latest()->paginate(20);
        // return view('admin.newsletter.index', compact('subscriptions'));
        return response()->json([
            'message' => 'Newsletter subscription listing (admin)',
            'subscriptions' => $subscriptions
        ]); // Placeholder
    }

    /**
     * Remove the specified resource from storage for Admin (unsubscribe).
     * Or toggle active status. For simplicity, direct delete for now.
     */
    public function adminDestroy(NewsletterSubscription $newsletterSubscription)
    {
        $subscriptionEmail = $newsletterSubscription->email;
        $subscriptionAttributes = $newsletterSubscription->toArray();
        $newsletterSubscription->delete();

        ActivityLog::record(
            action: 'deleted_newsletter_subscription',
            loggable: $newsletterSubscription, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted newsletter subscription for '{$subscriptionEmail}'.",
            properties: ['attributes' => $subscriptionAttributes]
        );

        // return redirect()->route('admin.newsletter.index')->with('success', 'Subscription removed successfully.');
        return response()->json([
            'message' => 'Subscription removed (admin)'
        ], 200); // Placeholder
    }

    /**
     * Toggle active status of a subscription (Admin).
     */
    public function adminToggleActive(NewsletterSubscription $newsletterSubscription)
    {
        $originalStatus = $newsletterSubscription->is_active;
        $newsletterSubscription->update(['is_active' => !$newsletterSubscription->is_active]);

        ActivityLog::record(
            action: 'toggled_newsletter_subscription_status',
            loggable: $newsletterSubscription,
            description: "User " . (Auth::user()->name ?? 'N/A') . " toggled newsletter subscription for '{$newsletterSubscription->email}' from " . ($originalStatus ? 'active' : 'inactive') . " to " . ($newsletterSubscription->is_active ? 'active' : 'inactive') . ".",
            properties: ['old_status' => $originalStatus, 'new_status' => $newsletterSubscription->is_active]
        );

        // return redirect()->route('admin.newsletter.index')->with('success', 'Subscription status updated.');
        return response()->json([
            'message' => 'Subscription status updated (admin)',
            'subscription' => $newsletterSubscription
        ]); // Placeholder
    }
}
