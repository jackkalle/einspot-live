<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Added

class TagController extends Controller
{
    // Tags are often managed via relationships on other models (Product, Blog)
    // However, providing dedicated admin CRUD for tags can be useful.

    /**
     * Display a listing of the resource for Admin.
     */
    public function adminIndex()
    {
        $tags = Tag::latest()->paginate(15);
        // return view('admin.tags.index', compact('tags'));
        return response()->json([
            'message' => 'Tag listing (admin)',
            'tags' => $tags
        ]); // Placeholder
    }

    /**
     * Show the form for creating a new resource for Admin.
     */
    public function adminCreate()
    {
        // return view('admin.tags.create');
        return response()->json([
            'message' => 'Tag create form (admin)'
        ]); // Placeholder
    }

    /**
     * Store a newly created resource in storage for Admin.
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'slug' => 'nullable|string|max:255|unique:tags,slug',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        $tag = Tag::create($data);

        ActivityLog::record(
            action: 'created_tag',
            loggable: $tag,
            description: "User " . (Auth::user()->name ?? 'N/A') . " created tag '{$tag->name}'.",
            properties: ['attributes' => $tag->toArray()]
        );

        // return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully.');
        return response()->json([
            'message' => 'Tag created (admin)',
            'tag' => $tag
        ], 201); // Placeholder
    }

    /**
     * Show the form for editing the specified resource for Admin.
     */
    public function adminEdit(Tag $tag)
    {
        // return view('admin.tags.edit', compact('tag'));
        return response()->json([
            'message' => 'Tag edit form (admin)',
            'tag' => $tag
        ]); // Placeholder
    }

    /**
     * Update the specified resource in storage for Admin.
     */
    public function adminUpdate(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $tag->id,
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        $originalAttributes = $tag->getOriginal();
        $tag->update($data);
        $changedAttributes = $tag->getChanges();

        ActivityLog::record(
            action: 'updated_tag',
            loggable: $tag,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated tag '{$tag->name}'.",
            properties: [
                'old' => array_intersect_key($originalAttributes, $changedAttributes),
                'new' => $changedAttributes
            ]
        );

        // return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully.');
        return response()->json([
            'message' => 'Tag updated (admin)',
            'tag' => $tag
        ]); // Placeholder
    }

    /**
     * Remove the specified resource from storage for Admin.
     */
    public function adminDestroy(Tag $tag)
    {
        // Tags will be detached from products/blogs via cascade on FK or model events if setup.
        $tagName = $tag->name;
        $tagAttributes = $tag->toArray();
        $tag->delete();

        ActivityLog::record(
            action: 'deleted_tag',
            loggable: $tag, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted tag '{$tagName}'.",
            properties: ['attributes' => $tagAttributes]
        );

        // return redirect()->route('admin.tags.index')->with('success', 'Tag deleted successfully.');
        return response()->json([
            'message' => 'Tag deleted (admin)'
        ], 200); // Placeholder for 204
    }


    // API endpoint for tag suggestions (e.g., for typeaheads)
    public function suggestions(Request $request)
    {
        $query = $request->input('q');
        $tags = Tag::where('name', 'LIKE', "%{$query}%")->limit(10)->get(['id', 'name']);
        return response()->json($tags);
    }
}
