<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Potentially use models if home/about pages need dynamic data beyond settings
// use App\Models\Product;
// use App\Models\Service;
// use App\Models\Project;
// use App\Models\Blog;

class PageController extends Controller
{
    public function home()
    {
        // Fetch data for home page sections if needed
        // $heroSlides = Setting::getValue('hero_slider_content', []); // Example
        // $featuredProducts = Product::latest()->take(4)->get();
        // $featuredServices = Service::take(6)->get();
        // $latestProjects = Project::latest()->take(3)->get();
        // $latestBlogPosts = Blog::where('is_published', true)->latest('published_at')->take(3)->get();

        // return view('pages.home', compact(
        //     'heroSlides',
        //     'featuredProducts',
        //     'featuredServices',
        //     'latestProjects',
        //     'latestBlogPosts'
        // ));

        // Example data fetching (will be refined)
        // $heroSlidesData = Setting::getValue('hero_slider_content');
        // $heroSlides = $heroSlidesData ? json_decode($heroSlidesData, true) : [];

        // $featuredProducts = \App\Models\Product::whereHas('category', fn($q) => $q->where('type', 'product'))->inRandomOrder()->take(4)->get();
        // $featuredServices = \App\Models\Service::inRandomOrder()->take(3)->get();
        // $latestProjects = \App\Models\Project::latest()->take(3)->get();
        // $latestBlogPosts = \App\Models\Blog::where('is_published', true)
        //                                     ->whereHas('category', fn($q) => $q->where('type', 'blog'))
        //                                     ->latest('published_at')->take(3)->get();
        // $productCategories = \App\Models\Category::where('type', 'product')->orderBy('name')->take(6)->get();


        // return view('pages.home', compact(
        //     'heroSlides',
        //     'featuredProducts',
        //     'featuredServices',
        //     'latestProjects',
        //     'latestBlogPosts',
        //     'productCategories'
        // ));

        // For now, just return the view as data fetching needs models to be working with DB
        return view('pages.home');
    }

    public function about()
    {
        // $teamMembers = []; // Fetch from DB or config if needed
        // $companyStory = Setting::getValue('company_story', 'Default company story...');
        // $mission = Setting::getValue('company_mission', 'Default mission...');
        // $vision = Setting::getValue('company_vision', 'Default vision...');

        // return view('pages.about', compact('companyStory', 'mission', 'vision', 'teamMembers'));
        return response()->json([
            'message' => 'About Us page data',
            // 'companyStory' => $companyStory,
            // 'mission' => $mission,
            // 'vision' => $vision,
            'content' => 'About Us Page Content (Placeholder)'
        ]);
    }

    public function contact()
    {
        // $contactInfo = [
        //     'email' => Setting::getValue('contact_email', 'info@example.com'),
        //     'phone' => Setting::getValue('contact_phone', '+1234567890'),
        //     'address' => Setting::getValue('address', '123 Main St, City'),
        //     'map_url' => Setting::getValue('google_maps_embed_url', null),
        // ];
        // return view('pages.contact', compact('contactInfo'));
         return response()->json([
            'message' => 'Contact page data',
            // 'contactInfo' => $contactInfo,
            'content' => 'Contact Page Content (Placeholder)'
        ]);
    }
}
