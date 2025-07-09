<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
// use Carbon\Carbon; // For lastmod

class SitemapController extends Controller
{
    public function index()
    {
        $staticPages = [
            ['url' => route('home'), 'lastmod' => now()->toAtomString(), 'priority' => '1.0'],
            ['url' => route('about'), 'lastmod' => now()->toAtomString(), 'priority' => '0.8'],
            ['url' => route('contact.form'), 'lastmod' => now()->toAtomString(), 'priority' => '0.7'],
            ['url' => route('products.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
            ['url' => route('services.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.8'],
            ['url' => route('projects.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.8'],
            ['url' => route('blog.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.8'],
        ];

        $urls = $staticPages;

        Product::whereHas('category', fn($q) => $q->where('type', 'product'))
            ->get()->each(function (Product $product) use (&$urls) {
            $urls[] = [
                'url' => route('products.show', $product->slug),
                'lastmod' => $product->updated_at->toAtomString(),
                'priority' => '0.8'
            ];
        });

        Service::all()->each(function (Service $service) use (&$urls) {
            $urls[] = [
                'url' => route('services.show', $service->slug),
                'lastmod' => $service->updated_at->toAtomString(),
                'priority' => '0.7'
            ];
        });

        Project::all()->each(function (Project $project) use (&$urls) {
            $urls[] = [
                'url' => route('projects.show', $project->slug),
                'lastmod' => $project->updated_at->toAtomString(),
                'priority' => '0.7'
            ];
        });

        Blog::where('is_published', true)
            ->whereHas('category', fn($q) => $q->where('type', 'blog'))
            ->get()->each(function (Blog $blog) use (&$urls) {
            $urls[] = [
                'url' => route('blog.show', $blog->slug),
                'lastmod' => $blog->updated_at->toAtomString(), // or published_at
                'priority' => '0.7'
            ];
        });

        Category::where('type', 'product')->get()->each(function(Category $category) use (&$urls) {
            $urls[] = [
                'url' => route('products.index', ['category' => $category->slug]),
                'lastmod' => $category->updated_at->toAtomString(),
                'priority' => '0.7'
            ];
        });
        Category::where('type', 'blog')->get()->each(function(Category $category) use (&$urls) {
            $urls[] = [
                'url' => route('blog.index', ['category' => $category->slug]),
                'lastmod' => $category->updated_at->toAtomString(),
                'priority' => '0.6'
            ];
        });

        // Optionally add tag pages if they exist
        // Tag::all()->each(function(Tag $tag) use (&$urls) {
        //     $urls[] = [
        //         'url' => route('products.index', ['tag' => $tag->slug]), // Assuming tags filter products
        //         'lastmod' => $tag->updated_at->toAtomString(),
        //         'priority' => '0.5'
        //     ];
        // });


        return response()->view('sitemap.index', [
            'urls' => $urls,
        ])->header('Content-Type', 'text/xml');
    }
}
