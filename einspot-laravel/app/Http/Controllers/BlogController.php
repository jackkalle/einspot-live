<?php

namespace App\Http\Controllers;

use App\Models\Blog; // Changed from BlogPost to Blog to match model name
use App\Models\Category;
use App\Models\Tag;
use App\Models\User; // For author
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class BlogController extends Controller
{
    // === PUBLIC FACING METHODS ===

    /**
     * Display a listing of the resource. (Blog Page)
     */
    public function index(Request $request)
    {
        $postsQuery = Blog::with(['category', 'tags', 'user'])
            ->where('is_published', true)
            ->whereHas('category', function ($q) { // Ensure posts are from 'blog' categories
                $q->where('type', 'blog');
            });

        if ($request->filled('category')) {
            $postsQuery->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'))->where('type', 'blog');
            });
        }

        if ($request->filled('tag')) {
            $postsQuery->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->input('tag'));
            });
        }

        // Optional: Search query for blog posts
        if ($request->filled('q')) {
            $searchQuery = $request->input('q');
            $postsQuery->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                  ->orWhere('content', 'like', "%{$searchQuery}%")
                  ->orWhere('excerpt', 'like', "%{$searchQuery}%");
            });
        }

        $posts = $postsQuery->latest('published_at')->paginate(10);
        $posts->appends($request->all());

        $categories = Category::where('type', 'blog')->orderBy('name')->get();
        // $tags = Tag::has('blogPosts')->orderBy('name')->get(); // Get only tags used in published blog posts

        // For now, returning JSON. Will switch to view() later.
        // return view('blog.index', compact('posts', 'categories', 'tags', 'request'));
        return response()->json([
            'message' => 'Blog post listing (public)',
            'posts' => $posts,
            'categories' => $categories,
            // 'tags' => $tags,
            'request_params' => $request->all()
        ]);
    }

    /**
     * Display the specified resource. (Individual Blog Post Page)
     */
    public function show($slug) // Use slug for SEO friendly URLs
    {
        $post = Blog::with(['category', 'tags', 'user'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereHas('category', function($q){ // Ensure category is of type 'blog'
                $q->where('type', 'blog');
            })
            ->firstOrFail();

        // Example: Get recent posts (excluding current)
        $recentPosts = Blog::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        // return view('blog.show', compact('post', 'recentPosts'));
        return response()->json([
            'message' => 'Blog post detail (public)',
            'post' => $post,
            'recent_posts' => $recentPosts
        ]);
    }

    // === ADMIN FACING METHODS ===

    public function adminIndex()
    {
        $posts = Blog::with(['category', 'user'])->latest()->paginate(15);
        // return view('admin.blogs.index', compact('posts'));
        return response()->json([
            'message' => 'Blog post listing (admin)',
            'posts' => $posts
        ]); // Placeholder
    }

    public function adminCreate()
    {
        $categories = Category::where('type', 'blog')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        // return view('admin.blogs.create', compact('categories', 'tags'));
        return response()->json([
            'message' => 'Blog post create form (admin)',
            'categories' => $categories,
            'tags' => $tags
        ]); // Placeholder
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'image_url_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // File upload for image_url
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->except(['image_url_file', 'tags']);
        $data['slug'] = $request->slug ?: Str::slug($request->name);
        $data['user_id'] = Auth::id(); // Set current authenticated user as author
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $request->is_published && $request->published_at ? $request->published_at : now();


        if ($request->hasFile('image_url_file')) {
            $data['image_url'] = $request->file('image_url_file')->store('blogs/images', 'public');
        }

        $post = Blog::create($data);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        ActivityLog::record(
            action: 'created_blog_post',
            loggable: $post,
            description: "User " . (Auth::user()->name ?? 'N/A') . " created blog post '{$post->title}'.",
            properties: ['attributes' => $post->toArray()]
        );

        // return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
        return response()->json([
            'message' => 'Blog post created (admin)',
            'post' => $post
        ], 201); // Placeholder
    }

    public function adminEdit(Blog $blog) // Route model binding
    {
        $categories = Category::where('type', 'blog')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        // return view('admin.blogs.edit', compact('blog', 'categories', 'tags'));
         return response()->json([
            'message' => 'Blog post edit form (admin)',
            'post' => $blog->load('tags'),
            'categories' => $categories,
            'tags' => $tags
        ]); // Placeholder
    }

    public function adminUpdate(Request $request, Blog $blog) // Route model binding
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'image_url_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->except(['image_url_file', 'tags', '_token', '_method']);
        $data['slug'] = $request->slug ?: Str::slug($request->name);
        $data['is_published'] = $request->boolean('is_published');
        // user_id (author) typically does not change, or requires specific logic if it can

        if ($request->hasFile('image_url_file')) {
            if ($blog->image_url) Storage::disk('public')->delete($blog->image_url);
            $data['image_url'] = $request->file('image_url_file')->store('blogs/images', 'public');
        }

        $originalAttributes = $blog->getOriginal(); // Capture before update
        $blog->update($data);

        if ($request->has('tags')) {
            $blog->tags()->sync($request->tags);
        } else {
            $blog->tags()->detach();
        }

        $changedAttributes = $blog->getChanges(); // Get changes after sync/detach if they modify 'updated_at'
        // If tags() doesn't touch updated_at, getChanges before sync might be better for attribute changes only.
        // For simplicity, getChanges after all operations.

        ActivityLog::record(
            action: 'updated_blog_post',
            loggable: $blog,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated blog post '{$blog->title}'.",
            properties: [
                'old' => array_intersect_key($originalAttributes, $changedAttributes),
                'new' => $changedAttributes
            ]
        );

        // return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
        return response()->json([
            'message' => 'Blog post updated (admin)',
            'post' => $blog
        ]); // Placeholder
    }

    public function adminDestroy(Blog $blog) // Route model binding
    {
        if ($blog->image_url) Storage::disk('public')->delete($blog->image_url);

        $postTitle = $blog->title;
        $postAttributes = $blog->toArray();
        $blog->delete();

        ActivityLog::record(
            action: 'deleted_blog_post',
            loggable: $blog, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted blog post '{$postTitle}'.",
            properties: ['attributes' => $postAttributes]
        );

        // return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully.');
        return response()->json([
            'message' => 'Blog post deleted (admin)'
        ], 200); // Placeholder
    }
}
