<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Added

class SettingController extends Controller
{
    // All methods in this controller are for ADMIN use.

    /**
     * Display a listing of the settings for Admin.
     * Show a form to update them.
     */
    public function adminIndex()
    {
        // Fetch all settings or a predefined list of expected settings
        $settings = Setting::all()->pluck('value', 'key');

        // Define expected settings to ensure form fields are present even if DB is empty
        $expectedSettings = [
            'website_name' => 'Einspot Solutions',
            'website_logo_path' => null,
            'website_favicon_path' => null,
            'contact_email' => 'info@einspot.com.ng',
            'contact_phone' => '+234 812 364 7982',
            'address' => 'Lagos, Nigeria',
            'social_facebook_url' => null,
            'social_twitter_url' => null,
            'social_linkedin_url' => null,
            'social_instagram_url' => null,
            'whatsapp_number' => '2348123647982', // Used for WhatsApp CTA
            'google_maps_api_key' => null,
            'google_maps_embed_url' => null, // Or use coordinates
            'hero_slider_content' => '[]', // JSON array of slides
            // Add more settings as needed
        ];

        // Merge DB settings with expected, DB values take precedence
        foreach($expectedSettings as $key => $defaultValue) {
            if (!isset($settings[$key])) {
                $settings[$key] = $defaultValue;
            }
        }

        // return view('admin.settings.index', compact('settings'));
        return response()->json([
            'message' => 'Settings form (admin)',
            'settings' => $settings
        ]); // Placeholder
    }

    /**
     * Update the specified settings in storage for Admin.
     */
    public function adminUpdate(Request $request)
    {
        // No strict validation here as keys are dynamic, but can validate specific known keys
        // Example validation for some specific keys:
        $request->validate([
            'contact_email' => 'nullable|email',
            'website_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:1024', // Logo upload
            'website_favicon' => 'nullable|image|mimes:ico,png|max:256',      // Favicon upload
            'hero_slides' => 'nullable|array', // If hero_slider_content is managed via structured data
            // Add validation for other fields as necessary
        ]);

        $settingsData = $request->except(['_token', '_method', 'website_logo', 'website_favicon', 'hero_slides']);

        foreach ($settingsData as $key => $value) {
            Setting::setValue($key, $value);
        }

        if ($request->hasFile('website_logo')) {
            $currentLogo = Setting::getValue('website_logo_path');
            if ($currentLogo) Storage::disk('public')->delete($currentLogo);
            $path = $request->file('website_logo')->store('settings/logos', 'public');
            Setting::setValue('website_logo_path', $path);
        }

        if ($request->hasFile('website_favicon')) {
            $currentFavicon = Setting::getValue('website_favicon_path');
            if ($currentFavicon) Storage::disk('public')->delete($currentFavicon);
            $path = $request->file('website_favicon')->store('settings/favicons', 'public');
            Setting::setValue('website_favicon_path', $path);
        }

        // Example for hero slider content if it's structured
        if ($request->has('hero_slides') && is_array($request->hero_slides)) {
             // Here you would process the array of slides, potentially uploading images for each slide
             // and then storing the structured data (e.g., image paths, text, links) as JSON string
             // For simplicity, let's assume hero_slides is already processed and ready to be stored as JSON
             Setting::setValue('hero_slider_content', json_encode($request->hero_slides));
        }

        // Log the update action
        // Since settings are key-value and many can be updated at once, logging individual changes can be verbose.
        // Log a general "settings updated" event or log changed keys if feasible.
        $changedKeys = array_keys($settingsData);
        if ($request->hasFile('website_logo')) $changedKeys[] = 'website_logo_path';
        if ($request->hasFile('website_favicon')) $changedKeys[] = 'website_favicon_path';
        if ($request->has('hero_slides')) $changedKeys[] = 'hero_slider_content';

        ActivityLog::record(
            action: 'updated_settings',
            loggable: new Setting(), // No specific model instance, or could log against a generic "SiteSettings" concept
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated site settings. Changed keys: " . implode(', ', $changedKeys),
            properties: ['changed_keys' => $changedKeys, 'new_values_preview' => array_slice($request->all(), 0, 5)] // Preview some values
        );

        // return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
        return response()->json([
            'message' => 'Settings updated (admin)'
        ]); // Placeholder
    }
}
