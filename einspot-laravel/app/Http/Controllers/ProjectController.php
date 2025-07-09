<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Added

class ProjectController extends Controller
{
    // === PUBLIC FACING METHODS ===

    /**
     * Display a listing of the resource. (Project Portfolio Page)
     */
    public function index(Request $request)
    {
        $projectsQuery = Project::latest();

        // Example: Allow filtering by type if a 'type' query param is present
        if ($request->filled('type')) {
            $projectsQuery->where('type', $request->input('type'));
        }

        $projects = $projectsQuery->paginate(9); // Paginate for project portfolio
        $projectTypes = Project::select('type')->distinct()->pluck('type')->filter()->sort(); // Get unique, non-null types

        // return view('pages.projects.index', compact('projects', 'projectTypes')); // Adjusted view path
        return response()->json([
            'message' => 'Project listing (public)',
            'projects' => $projects,
            'project_types' => $projectTypes
        ]); // Placeholder
    }

    /**
     * Display the specified resource. (Individual Project Page)
     */
    public function show(Project $project) // Route model binding by slug
    {
        // Potentially fetch related/recent projects
        $recentProjects = Project::where('id', '!=', $project->id)->latest()->take(3)->get();
        // return view('pages.projects.show', compact('project', 'recentProjects')); // Adjusted view path
        return response()->json([
            'message' => 'Project detail page (public)',
            'project' => $project,
            'recent_projects' => $recentProjects
        ]); // Placeholder
    }

    // === ADMIN FACING METHODS ===

    public function adminIndex()
    {
        $projects = Project::latest()->paginate(15);
        // return view('admin.projects.index', compact('projects'));
        return response()->json([
            'message' => 'Project listing (admin)',
            'projects' => $projects
        ]); // Placeholder
    }

    public function adminCreate()
    {
        // return view('admin.projects.create');
        return response()->json([
            'message' => 'Project create form (admin)'
        ]); // Placeholder
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'client' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Main image
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Additional images
            'brands_used' => 'nullable|array',
            'technologies' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->except(['image_url', 'images', 'brands_used', 'technologies']);
        $data['slug'] = $request->slug ?: Str::slug($request->title);

        $data['brands_used'] = $request->input('brands_used', []);
        $data['technologies'] = $request->input('technologies', []);

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->store('projects/main_images', 'public');
        }

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $imageFile) {
                $imagePaths[] = $imageFile->store('projects/additional_images', 'public');
            }
            $data['images'] = $imagePaths;
        }

        $project = Project::create($data);

        ActivityLog::record(
            action: 'created_project',
            loggable: $project,
            description: "User " . (Auth::user()->name ?? 'N/A') . " created project '{$project->title}'.",
            properties: ['attributes' => $project->toArray()]
        );

        // return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
        return response()->json([
            'message' => 'Project created (admin)',
            'project' => $project
        ], 201); // Placeholder
    }

    public function adminEdit(Project $project)
    {
        // return view('admin.projects.edit', compact('project'));
        return response()->json([
            'message' => 'Project edit form (admin)',
            'project' => $project
        ]); // Placeholder
    }

    public function adminUpdate(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug,' . $project->id,
            'client' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'brands_used' => 'nullable|array',
            'technologies' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->except(['image_url', 'images', 'brands_used', 'technologies', '_token', '_method']);
        $data['slug'] = $request->slug ?: Str::slug($request->title);

        $data['brands_used'] = $request->input('brands_used', $project->brands_used ?? []); // Keep old if not provided
        $data['technologies'] = $request->input('technologies', $project->technologies ?? []);


        if ($request->hasFile('image_url')) {
            if ($project->image_url) Storage::disk('public')->delete($project->image_url);
            $data['image_url'] = $request->file('image_url')->store('projects/main_images', 'public');
        }

        if ($request->hasFile('images')) {
            // Simple approach: clear old additional images and add new ones
            // More complex logic could allow adding/removing individual images
            if ($project->images) {
                foreach ($project->images as $oldImagePath) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $imageFile) {
                $imagePaths[] = $imageFile->store('projects/additional_images', 'public');
            }
            $data['images'] = $imagePaths;
        }

        $originalAttributes = $project->getOriginal();
        $project->update($data);
        $changedAttributes = $project->getChanges();

        ActivityLog::record(
            action: 'updated_project',
            loggable: $project,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated project '{$project->title}'.",
            properties: [
                'old' => array_intersect_key($originalAttributes, $changedAttributes),
                'new' => $changedAttributes
            ]
        );

        // return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
        return response()->json([
            'message' => 'Project updated (admin)',
            'project' => $project
        ]); // Placeholder
    }

    public function adminDestroy(Project $project)
    {
        if ($project->image_url) Storage::disk('public')->delete($project->image_url);
        if ($project->images) {
            foreach ($project->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        $projectTitle = $project->title;
        $projectAttributes = $project->toArray();
        $project->delete();

        ActivityLog::record(
            action: 'deleted_project',
            loggable: $project, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted project '{$projectTitle}'.",
            properties: ['attributes' => $projectAttributes]
        );

        // return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
        return response()->json([
            'message' => 'Project deleted (admin)'
        ], 200); // Placeholder
    }
}
