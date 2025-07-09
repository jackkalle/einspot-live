<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Added

class ServiceController extends Controller
{
    // === PUBLIC FACING METHODS ===

    /**
     * Display a listing of the resource. (Services Page)
     */
    public function index()
    {
        $services = Service::latest()->get();
        // return view('pages.services.index', compact('services')); // Adjusted view path
        return response()->json([
            'message' => 'Service listing (public)',
            'services' => $services
        ]); // Placeholder
    }

    /**
     * Display the specified resource. (Individual Service Page - if needed, or part of index)
     */
    public function show(Service $service) // Route model binding by slug
    {
        // Potentially fetch related services or projects
        // return view('pages.services.show', compact('service')); // Adjusted view path
        return response()->json([
            'message' => 'Service detail page (public)',
            'service' => $service
        ]); // Placeholder
    }

    // === ADMIN FACING METHODS ===

    /**
     * Display a listing of the resource for Admin.
     */
    public function adminIndex()
    {
        $services = Service::latest()->paginate(15);
        // return view('admin.services.index', compact('services'));
        return response()->json([
            'message' => 'Service listing (admin)',
            'services' => $services
        ]); // Placeholder
    }

    /**
     * Show the form for creating a new resource for Admin.
     */
    public function adminCreate()
    {
        // return view('admin.services.create');
        return response()->json([
            'message' => 'Service create form (admin)'
        ]); // Placeholder
    }

    /**
     * Store a newly created resource in storage for Admin.
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:1024', // Icon image
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Main image
            'features' => 'nullable|array', // Handled as JSON
            'features.*' => 'string',
            'whatsapp_text' => 'nullable|string',
        ]);

        $data = $request->except(['icon', 'image', 'features']);
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        if ($request->has('features') && is_array($request->features)) {
            $data['features'] = $request->features; // Already an array from form, direct assign
        } else {
            $data['features'] = null;
        }


        if ($request->hasFile('icon')) {
            $data['icon_path'] = $request->file('icon')->store('services/icons', 'public');
        }
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('services/images', 'public');
        }

        $service = Service::create($data);

        ActivityLog::record(
            action: 'created_service',
            loggable: $service,
            description: "User " . (Auth::user()->name ?? 'N/A') . " created service '{$service->name}'.",
            properties: ['attributes' => $service->toArray()]
        );

        // return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
        return response()->json([
            'message' => 'Service created (admin)',
            'service' => $service
        ], 201); // Placeholder
    }

    /**
     * Show the form for editing the specified resource for Admin.
     */
    public function adminEdit(Service $service)
    {
        // return view('admin.services.edit', compact('service'));
        return response()->json([
            'message' => 'Service edit form (admin)',
            'service' => $service
        ]); // Placeholder
    }

    /**
     * Update the specified resource in storage for Admin.
     */
    public function adminUpdate(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:1024',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'whatsapp_text' => 'nullable|string',
        ]);

        $data = $request->except(['icon', 'image', 'features', '_token', '_method']);
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        if ($request->has('features') && is_array($request->features)) {
            $data['features'] = $request->features;
        } else if ($request->exists('features') && $request->features === null) { // Allow clearing features
            $data['features'] = null;
        }


        if ($request->hasFile('icon')) {
            if ($service->icon_path) Storage::disk('public')->delete($service->icon_path);
            $data['icon_path'] = $request->file('icon')->store('services/icons', 'public');
        }
        if ($request->hasFile('image')) {
            if ($service->image_url) Storage::disk('public')->delete($service->image_url);
            $data['image_url'] = $request->file('image')->store('services/images', 'public');
        }

        $originalAttributes = $service->getOriginal();
        $service->update($data);
        $changedAttributes = $service->getChanges();

        ActivityLog::record(
            action: 'updated_service',
            loggable: $service,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated service '{$service->name}'.",
            properties: [
                'old' => array_intersect_key($originalAttributes, $changedAttributes),
                'new' => $changedAttributes
            ]
        );

        // return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
        return response()->json([
            'message' => 'Service updated (admin)',
            'service' => $service
        ]); // Placeholder
    }

    /**
     * Remove the specified resource from storage for Admin.
     */
    public function adminDestroy(Service $service)
    {
        if ($service->icon_path) Storage::disk('public')->delete($service->icon_path);
        if ($service->image_url) Storage::disk('public')->delete($service->image_url);

        $serviceName = $service->name;
        $serviceAttributes = $service->toArray();
        $service->delete();

        ActivityLog::record(
            action: 'deleted_service',
            loggable: $service, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted service '{$serviceName}'.",
            properties: ['attributes' => $serviceAttributes]
        );

        // return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
        return response()->json([
            'message' => 'Service deleted (admin)'
        ], 200); // Placeholder
    }
}
