<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ActivityLog; // Added ActivityLog model
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // For file uploads
use Illuminate\Support\Facades\Auth; // For getting authenticated user

class ProductController extends Controller
{
    // === PUBLIC FACING METHODS ===

    /**
     * Display a listing of the resource. (Product Catalog Page)
     */
    public function index(Request $request)
    {
        // TODO: Implement filtering by category, tags, search query
        // For now, simple pagination
        $productsQuery = Product::with('category', 'tags')->whereHas('category', function ($q) {
            $q->where('type', 'product');
        });

        if ($request->filled('category')) {
            $productsQuery->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('tag')) {
            $productsQuery->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->input('tag'));
            });
        }
        // TODO: Add search query integration from $request->input('q') if needed on this page

        $products = $productsQuery->latest()->paginate(12);
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        // $tags = Tag::has('products')->orderBy('name')->get(); // Get only tags used by products of type 'product'

        // For now, returning JSON. Will switch to view() later.
        // return view('products.index', compact('products', 'categories', 'tags'));
        return response()->json([
            'message' => 'Product listing (public)',
            'products' => $products,
            'categories' => $categories,
            // 'tags' => $tags
        ]);
    }

    /**
     * Display the specified resource. (Individual Product Page)
     */
    public function show(Product $product) // Route model binding by slug
    {
        // Ensure the product's category is of type 'product' if categories are shared
        if ($product->category && $product->category->type !== 'product') {
            // Or handle as a 404 if categories are strictly typed and this shouldn't happen
            // For now, we assume route model binding gets the correct product.
            // Add explicit check if needed: abort_if($product->category->type !== 'product', 404);
        }

        $product->load('category', 'tags');

        // Fetch related products (example: same category, excluding self)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereHas('category', function ($q) {
                $q->where('type', 'product');
            })
            ->take(4)
            ->get();

        // return view('products.show', compact('product', 'relatedProducts'));
        return response()->json([
            'message' => 'Product detail page (public)',
            'product' => $product,
            'related_products' => $relatedProducts
        ]);
    }

    /**
     * Search products.
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('q');
        $categorySlug = $request->input('category'); // Category slug from request
        // TODO: Implement other filters like tags, price range if needed.

        $productsQuery = Product::with(['category', 'tags'])
            ->whereHas('category', function ($q) { // Ensure products are from 'product' categories
                $q->where('type', 'product');
            });

        if ($searchQuery) {
            $productsQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('description', 'like', "%{$searchQuery}%")
                  // Future: Could search in tag names too if complex search needed
                  ->orWhereHas('tags', function ($tagQuery) use ($searchQuery) {
                      $tagQuery->where('name', 'like', "%{$searchQuery}%");
                  });
            });
        }

        if ($categorySlug) {
            $productsQuery->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug)->where('type', 'product');
            });
        }

        $results = $productsQuery->latest()->paginate(10);
        $results->appends($request->all()); // Append query parameters to pagination links

        $categories = Category::where('type', 'product')->orderBy('name')->get();

        // This will be a public search results view
        // return view('products.search', compact('results', 'searchQuery', 'categorySlug', 'categories'));
         return response()->json([
            'message' => 'Product search results (public)',
            'search_query' => $searchQuery,
            'category_slug' => $categorySlug,
            'results' => $results,
            'categories' => $categories
        ]); // Placeholder
    }


    // === ADMIN FACING METHODS === (To be prefixed with /admin and middleware protected)

    /**
     * Display a listing of the resource for Admin.
     */
    public function adminIndex()
    {
        $products = Product::with('category')->latest()->paginate(15);
        // return view('admin.products.index', compact('products'));
        return response()->json([
            'message' => 'Product listing (admin)',
            'products' => $products
        ]); // Placeholder
    }

    /**
     * Show the form for creating a new resource for Admin.
     */
    public function adminCreate()
    {
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        // return view('admin.products.create', compact('categories', 'tags'));
        return response()->json([
            'message' => 'Product create form (admin)',
            'categories' => $categories,
            'tags' => $tags
        ]); // Placeholder
    }

    /**
     * Store a newly created resource in storage for Admin.
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Multiple images
            'pdf_manual' => 'nullable|file|mimes:pdf|max:5120', // PDF manual
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->except(['images', 'pdf_manual', 'tags']);
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $imageFile) {
                $imagePaths[] = $imageFile->store('products/images', 'public');
            }
            $data['images'] = $imagePaths; // Store as JSON array of paths
        }

        // Handle PDF manual upload
        if ($request->hasFile('pdf_manual')) {
            $data['pdf_manual_path'] = $request->file('pdf_manual')->store('products/manuals', 'public');
        }

        $product = Product::create($data);

        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
        }

        ActivityLog::record(
            action: 'created_product',
            loggable: $product,
            description: "User " . (Auth::user()->name ?? 'N/A') . " created product '{$product->name}'.",
            properties: ['attributes' => $product->toArray()]
        );

        // return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        return response()->json([
            'message' => 'Product created (admin)',
            'product' => $product
        ], 201); // Placeholder
    }


    /**
     * Show the form for editing the specified resource for Admin.
     */
    public function adminEdit(Product $product)
    {
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        // return view('admin.products.edit', compact('product', 'categories', 'tags'));
        return response()->json([
            'message' => 'Product edit form (admin)',
            'product' => $product->load('tags'),
            'categories' => $categories,
            'tags' => $tags
        ]); // Placeholder
    }

    /**
     * Update the specified resource in storage for Admin.
     */
    public function adminUpdate(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_manual' => 'nullable|file|mimes:pdf|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->except(['images', 'pdf_manual', 'tags', '_token', '_method']);
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        // Handle image uploads (replace or add to existing)
        if ($request->hasFile('images')) {
            // Optionally delete old images first if replacing all
            // if ($product->images) {
            //     foreach ($product->images as $oldImagePath) {
            //         Storage::disk('public')->delete($oldImagePath);
            //     }
            // }
            $imagePaths = $product->images ?? []; // Keep existing if not replacing all
            foreach ($request->file('images') as $imageFile) {
                $imagePaths[] = $imageFile->store('products/images', 'public');
            }
            $data['images'] = $imagePaths;
        }

        // Handle PDF manual upload
        if ($request->hasFile('pdf_manual')) {
            // Optionally delete old PDF
            if ($product->pdf_manual_path) {
                Storage::disk('public')->delete($product->pdf_manual_path);
            }
            $data['pdf_manual_path'] = $request->file('pdf_manual')->store('products/manuals', 'public');
        }

        $originalAttributes = $product->getOriginal();
        $product->update($data);

        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
        } else {
            $product->tags()->detach(); // Remove all tags if 'tags' is not present or empty
        }

        $changedAttributes = $product->getChanges();
        ActivityLog::record(
            action: 'updated_product',
            loggable: $product,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated product '{$product->name}'.",
            properties: [
                'old' => array_intersect_key($originalAttributes, $changedAttributes),
                'new' => $changedAttributes
            ]
        );

        // return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        return response()->json([
            'message' => 'Product updated (admin)',
            'product' => $product
        ]); // Placeholder
    }

    /**
     * Remove the specified resource from storage for Admin.
     */
    public function adminDestroy(Product $product)
    {
        // Optionally delete associated files (images, PDF) from storage
        if ($product->images) {
            foreach ($product->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        if ($product->pdf_manual_path) {
            Storage::disk('public')->delete($product->pdf_manual_path);
        }

        $productName = $product->name; // Get name before deleting
        $productAttributes = $product->toArray(); // Get attributes before deleting
        $product->delete();

        ActivityLog::record(
            action: 'deleted_product',
            loggable: $product, // Still works with soft deletes, for hard deletes, $productAttributes might be better for properties
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted product '{$productName}'.",
            properties: ['attributes' => $productAttributes]
        );

        // return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        return response()->json([
            'message' => 'Product deleted (admin)'
        ], 200); // Placeholder for 204 No Content
    }
}
