<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Added

class CategoryController extends Controller
{
    // These methods are primarily for ADMIN use.
    // Public display of categories will often be part of other controllers (e.g., ProductController, BlogController)

    /**
     * Display a listing of the resource for Admin.
     */
    public function adminIndex(Request $request)
    {
        $type = $request->query('type'); // Filter by type e.g. 'product' or 'blog'
        $categoriesQuery = Category::latest();

        if ($type) {
            $categoriesQuery->where('type', $type);
        }

        $categories = $categoriesQuery->paginate(15);

        // return view('admin.categories.index', compact('categories', 'type'));
        return response()->json([
            'message' => 'Category listing (admin)',
            'categories' => $categories,
            'type_filter' => $type
        ]); // Placeholder
    }

    /**
     * Show the form for creating a new resource for Admin.
     */
    public function adminCreate(Request $request)
    {
        $type = $request->query('type', 'product'); // Default to 'product' or allow selection
        // return view('admin.categories.create', compact('type'));
         return response()->json([
            'message' => 'Category create form (admin)',
            'type' => $type
        ]); // Placeholder
    }

    /**
     * Store a newly created resource in storage for Admin.
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'type' => 'required|string|in:product,blog', // Enforce type
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        $category = Category::create($data);

        ActivityLog::record(
            action: 'created_category',
            loggable: $category,
            description: "User " . (Auth::user()->name ?? 'N/A') . " created category '{$category->name}' of type '{$category->type}'.",
            properties: ['attributes' => $category->toArray()]
        );

        // return redirect()->route('admin.categories.index', ['type' => $category->type])->with('success', 'Category created successfully.');
        return response()->json([
            'message' => 'Category created (admin)',
            'category' => $category
        ], 201); // Placeholder
    }

    /**
     * Display the specified resource for Admin (not typically needed for categories).
     */
    // public function adminShow(Category $category)
    // {
    //     // return view('admin.categories.show', compact('category'));
    // }

    /**
     * Show the form for editing the specified resource for Admin.
     */
    public function adminEdit(Category $category)
    {
        // return view('admin.categories.edit', compact('category'));
        return response()->json([
            'message' => 'Category edit form (admin)',
            'category' => $category
        ]); // Placeholder
    }

    /**
     * Update the specified resource in storage for Admin.
     */
    public function adminUpdate(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'type' => 'required|string|in:product,blog',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        $originalAttributes = $category->getOriginal();
        $category->update($data);
        $changedAttributes = $category->getChanges();

        ActivityLog::record(
            action: 'updated_category',
            loggable: $category,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated category '{$category->name}'.",
            properties: [
                'old' => array_intersect_key($originalAttributes, $changedAttributes),
                'new' => $changedAttributes
            ]
        );

        // return redirect()->route('admin.categories.index', ['type' => $category->type])->with('success', 'Category updated successfully.');
        return response()->json([
            'message' => 'Category updated (admin)',
            'category' => $category
        ]); // Placeholder
    }

    /**
     * Remove the specified resource from storage for Admin.
     */
    public function adminDestroy(Category $category)
    {
        // Consider what happens to products/blogs associated with this category.
        // Migration sets category_id to null on delete.
        $categoryType = $category->type;
        $categoryName = $category->name;
        $categoryAttributes = $category->toArray();
        $category->delete();

        ActivityLog::record(
            action: 'deleted_category',
            loggable: $category, // For soft deletes, $category model is fine. For hard, use $categoryAttributes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted category '{$categoryName}' of type '{$categoryType}'.",
            properties: ['attributes' => $categoryAttributes]
        );

        // return redirect()->route('admin.categories.index', ['type' => $categoryType])->with('success', 'Category deleted successfully.');
        return response()->json([
            'message' => 'Category deleted (admin)'
        ], 200); // Placeholder for 204
    }
}
