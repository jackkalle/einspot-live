{{-- Extends a main layout --}}
{{-- @extends('layouts.app') --}}

{{-- @section('title', 'Einspot Solutions - Your Engineering Partner') --}}

{{-- @section('content') --}}
    <h1>Welcome to Einspot Solutions (Home Page)</h1>
    <p>This is the homepage content that will be built out with Blade and Tailwind/Bootstrap.</p>

    {{-- Placeholder for Hero Section --}}
    <div>
        <h2>Hero Section</h2>
        {{-- @if(isset($heroSlides) && count($heroSlides) > 0)
            <ul>
            @foreach($heroSlides as $slide)
                <li>{{ $slide['title'] ?? 'Slide' }}: {{ $slide['description'] ?? '' }}</li>
            @endforeach
            </ul>
        @else
            <p>Hero slider content will be here.</p>
        @endif --}}
        <p>Hero slider content will be here.</p>
    </div>

    {{-- Placeholder for Services Highlights --}}
    <div>
        <h2>Services Highlights</h2>
        {{-- @if(isset($featuredServices) && $featuredServices->count() > 0)
            <ul>
            @foreach($featuredServices as $service)
                <li>{{ $service->name }}</li>
            @endforeach
            </ul>
        @else
            <p>Featured services will be listed here.</p>
        @endif --}}
        <p>Featured services will be listed here.</p>
    </div>

    {{-- Placeholder for Product Categories Preview --}}
    <div>
        <h2>Product Categories</h2>
        {{-- @if(isset($productCategories) && $productCategories->count() > 0)
            <ul>
            @foreach($productCategories as $category)
                <li>{{ $category->name }}</li>
            @endforeach
            </ul>
        @else
            <p>Product categories will be listed here.</p>
        @endif --}}
        <p>Product categories will be listed here.</p>
    </div>

    {{-- Placeholder for Latest Projects Grid --}}
    <div>
        <h2>Latest Projects</h2>
        {{-- @if(isset($latestProjects) && $latestProjects->count() > 0)
            <ul>
            @foreach($latestProjects as $project)
                <li>{{ $project->title }}</li>
            @endforeach
            </ul>
        @else
            <p>Latest projects will be showcased here.</p>
        @endif --}}
        <p>Latest projects will be showcased here.</p>
    </div>

    {{-- Placeholder for Blog Section Preview --}}
    <div>
        <h2>Latest Blog Posts</h2>
        {{-- @if(isset($latestBlogPosts) && $latestBlogPosts->count() > 0)
            <ul>
            @foreach($latestBlogPosts as $post)
                <li>{{ $post->title }}</li>
            @endforeach
            </ul>
        @else
            <p>Latest blog posts will be previewed here.</p>
        @endif --}}
        <p>Latest blog posts will be previewed here.</p>
    </div>

    {{-- WhatsApp CTA and Quote Button --}}
    <div>
        <p>WhatsApp CTA and Quote Button will be here.</p>
    </div>

{{-- @endsection --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einspot Solutions - Home</title>
    {{-- Minimal styling --}}
    <title>@yield('title', 'Einspot Solutions - Your Engineering Partner')</title>
    {{-- Minimal styling from app.blade.php will apply --}}
</head>
<body>

@extends('layouts.app')

@section('title', 'Einspot Solutions - Your Engineering Partner')

@section('content')

    {{-- Hero Section - Full-width slider with product highlights --}}
    {{-- This would ideally be a dynamic slider fetching from $heroSlides in PageController --}}
    <section class="relative h-[60vh] sm:h-[70vh] md:h-screen bg-cover bg-center text-white" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://via.placeholder.com/1920x1080/cccccc/808080?text=Hero+Image+Placeholder+1');">
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-4">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 leading-tight">
                Engineered for <span class="text-einspot-red-500">Life</span>
            </h1>
            <p class="text-lg sm:text-xl md:text-2xl mb-8 max-w-3xl">
                Trusted HVAC, Water Heating, and Engineering Solutions for Nigeria.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('products.index') }}" class="btn-cta">
                    Explore Products
                </a>
                <a href="#find-pro" class="btn-cta-outline"> {{-- Link to a section or page --}}
                    Find a Professional
                </a>
            </div>
        </div>
        {{-- Add slider controls if multiple slides --}}
    </section>

    {{-- Services Highlights - Service Icons in 2-row layout --}}
    <section class="py-12 md:py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Our Core Services</h2>
                <p class="text-lg text-gray-600 mt-2">Comprehensive solutions for all your engineering needs.</p>
            </div>
            {{-- Data: $featuredServices (collection of Service models) --}}
            @php
                // Mock data for services if $featuredServices is not passed or empty
                $mockServices = collect([
                    (object)['name' => 'HVAC Systems', 'icon_path' => ' M10 20v-6m0 0V4m0 6h10M10 4h10M4 20h16M4 4h2m14 0h-2M4 10h2m14 0h-2m-4 10v-6m0 0V4m0 6h-3m3 0h3', 'description' => 'Design, installation, and maintenance of HVAC systems.'],
                    (object)['name' => 'Water Heating', 'icon_path' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7.014A7.986 7.986 0 0112 2c2.21 0 4.21.895 5.657 2.343A8 8 0 0117.657 18.657z', 'description' => 'Rheem certified water heater supply and installation.'],
                    (object)['name' => 'Fire Safety', 'icon_path' => 'M12 1.05C6.477 1.05 2.05 5.477 2.05 11s4.427 9.95 9.95 9.95 9.95-4.427 9.95-9.95S17.523 1.05 12 1.05zm0 16.95c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM12 7c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2z', 'description' => 'Certified fire detection and suppression systems.'],
                    (object)['name' => 'Building Automation', 'icon_path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7-7-7M19 10v10a1 1 0 01-1 1h-3', 'description' => 'Advanced Building Management Systems (BMS).'],
                    (object)['name' => 'Electrical Engineering', 'icon_path' => 'M13 10V3L4 14h7v7l9-11h-7z', 'description' => 'Professional electrical wiring and smart lighting.'],
                    (object)['name' => 'Plumbing Systems', 'icon_path' => 'M18.364 5.636l-3.536 3.536m0 0L11.292 11.292l3.536-3.535m0 0L18.364 5.636m-3.535 3.536L8.464 15.535m0 0l-3.535 3.536m0 0l3.535-3.536m0 0l6.364-6.364', 'description' => 'Complete plumbing design and installation.'],
                ]);
                $servicesToDisplay = $featuredServices ?? $mockServices;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($servicesToDisplay->take(6) as $service)
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col items-center text-center">
                    <div class="bg-red-100 text-einspot-red-600 p-4 rounded-full mb-4 inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service->icon_path ?? 'M13 10V3L4 14h7v7l9-11h-7z' }}" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $service->name }}</h3>
                    <p class="text-gray-600 text-sm flex-grow">{{ Str::limit($service->description ?? 'Service description placeholder.', 100) }}</p>
                    <a href="{{-- route('services.show', $service->slug) --}}" class="mt-4 text-einspot-red-600 hover:text-einspot-red-700 font-medium transition-colors">
                        Learn More &rarr;
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Product Categories Preview --}}
    <section class="py-12 md:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Explore Our Products</h2>
                <p class="text-lg text-gray-600 mt-2">High-quality solutions across various categories.</p>
            </div>
            {{-- Data: $productCategories (collection of Category models of type 'product') --}}
            @php
                $mockProductCategories = collect([
                    (object)['name' => 'Water Heaters', 'slug' => 'water-heaters', 'image_url' => 'https://via.placeholder.com/400x300/FF0000/FFFFFF?text=Water+Heaters'],
                    (object)['name' => 'HVAC Systems', 'slug' => 'hvac-systems', 'image_url' => 'https://via.placeholder.com/400x300/00FF00/FFFFFF?text=HVAC+Systems'],
                    (object)['name' => 'Fire Safety', 'slug' => 'fire-safety', 'image_url' => 'https://via.placeholder.com/400x300/0000FF/FFFFFF?text=Fire+Safety'],
                    (object)['name' => 'Pumps & Controls', 'slug' => 'pumps-controls', 'image_url' => 'https://via.placeholder.com/400x300/FFFF00/000000?text=Pumps'],
                ]);
                 $categoriesToDisplay = $productCategories ?? $mockProductCategories;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($categoriesToDisplay->take(4) as $category)
                <div class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <a href="{{-- route('products.index', ['category' => $category->slug]) --}}" class="block">
                        <img src="{{ $category->image_url ?? 'https://via.placeholder.com/400x300/cccccc/808080?text=Category+Image' }}" alt="{{ $category->name }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 group-hover:text-einspot-red-600 transition-colors">{{ $category->name }}</h3>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
             <div class="text-center mt-12">
                <a href="{{ route('products.index') }}" class="btn-cta">View All Products</a>
            </div>
        </div>
    </section>

    {{-- Latest Projects Grid - Bento Grid for Project Showcase (3-column responsive) --}}
    <section class="py-12 md:py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Our Recent Projects</h2>
                <p class="text-lg text-gray-600 mt-2">Delivering excellence in every endeavor.</p>
            </div>
            {{-- Data: $latestProjects (collection of Project models) --}}
            @php
                $mockProjects = collect([
                    (object)['title' => 'Almat Farms Hotel HVAC', 'image_url' => 'https://via.placeholder.com/600x400/FF0000/FFFFFF?text=Project+1', 'type' => 'HVAC', 'slug' => 'almat-farms'],
                    (object)['title' => 'Lekki Residence Water Heaters', 'image_url' => 'https://via.placeholder.com/600x400/00FF00/FFFFFF?text=Project+2', 'type' => 'Water Heating', 'slug' => 'lekki-residence'],
                    (object)['title' => 'RoyalMicro Bank Fire Safety', 'image_url' => 'https://via.placeholder.com/600x400/0000FF/FFFFFF?text=Project+3', 'type' => 'Fire Safety', 'slug' => 'royalmicro-bank'],
                ]);
                 $projectsToDisplay = $latestProjects ?? $mockProjects;
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($projectsToDisplay->take(3) as $project)
                <div class="group relative rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden aspect-[4/3]">
                    <img src="{{ $project->image_url ?? 'https://via.placeholder.com/600x400/cccccc/808080?text=Project+Image' }}" alt="{{ $project->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-xs bg-einspot-red-600 text-white px-2 py-1 rounded mb-1 inline-block">{{ $project->type ?? 'Engineering' }}</span>
                        <h3 class="text-lg font-semibold text-white group-hover:text-einspot-red-300 transition-colors">{{ $project->title }}</h3>
                        <a href="{{-- route('projects.show', $project->slug) --}}" class="text-sm text-einspot-red-300 hover:text-white font-medium">View Project &rarr;</a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('projects.index') }}" class="btn-cta-outline">Explore All Projects</a>
            </div>
        </div>
    </section>

    {{-- Blog Section Preview - Blog Grid with Date & Category --}}
    <section class="py-12 md:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Engineering Insights</h2>
                <p class="text-lg text-gray-600 mt-2">Latest news, tips, and articles from our experts.</p>
            </div>
            {{-- Data: $latestBlogPosts (collection of Blog models) --}}
             @php
                $mockBlogPosts = collect([
                    (object)['title' => 'The Future of Smart Buildings', 'slug' => 'smart-buildings', 'image_url' => 'https://via.placeholder.com/400x250/FF9900/FFFFFF?text=Blog+1', 'published_at' => now()->subDays(2), 'category' => (object)['name' => 'Technology']],
                    (object)['title' => 'Choosing Your Water Heater', 'slug' => 'water-heater-guide', 'image_url' => 'https://via.placeholder.com/400x250/0099FF/FFFFFF?text=Blog+2', 'published_at' => now()->subDays(5), 'category' => (object)['name' => 'Home Solutions']],
                    (object)['title' => 'Fire Safety Essentials', 'slug' => 'fire-safety-101', 'image_url' => 'https://via.placeholder.com/400x250/9900FF/FFFFFF?text=Blog+3', 'published_at' => now()->subDays(10), 'category' => (object)['name' => 'Safety']],
                ]);
                $blogPostsToDisplay = $latestBlogPosts ?? $mockBlogPosts;
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                 @foreach($blogPostsToDisplay->take(3) as $post)
                <article class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col">
                    <a href="{{-- route('blog.show', $post->slug) --}}" class="block">
                        <img src="{{ $post->image_url ?? 'https://via.placeholder.com/400x250/cccccc/808080?text=Blog+Image'}}" alt="{{ $post->title }}" class="w-full h-48 object-cover group-hover:opacity-80 transition-opacity duration-300">
                    </a>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="mb-2">
                            <span class="text-xs text-einspot-red-600 font-semibold uppercase">{{ $post->category->name ?? 'General' }}</span>
                            <span class="text-xs text-gray-500 mx-1">&bull;</span>
                            <time datetime="{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->toIso8601String() : '' }}" class="text-xs text-gray-500">
                                {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('M d, Y') : 'N/A' }}
                            </time>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex-grow group-hover:text-einspot-red-600 transition-colors">
                            <a href="{{-- route('blog.show', $post->slug) --}}">{{ Str::limit($post->title, 60) }}</a>
                        </h3>
                        <a href="{{-- route('blog.show', $post->slug) --}}" class="text-sm text-einspot-red-600 hover:text-einspot-red-700 font-medium self-start">Read More &rarr;</a>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('blog.index') }}" class="btn-cta">Visit Our Blog</a>
            </div>
        </div>
    </section>

    {{-- WhatsApp CTA and Quote Button section (can be part of footer or a separate section) --}}
    <section class="py-12 md:py-16 bg-einspot-red-600 text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">Have a Project in Mind?</h2>
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto">Let's discuss your requirements. Get a free quote or chat with our experts on WhatsApp.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="#quote-form-link" class="btn-cta bg-white text-einspot-red-600 hover:bg-gray-100 hover:text-einspot-red-700 border-white">
                    Request a Detailed Quote
                </a>
                <a href="https://wa.me/{{ App\Models\Setting::getValue('whatsapp_number', '2348123647982') }}?text=Hello%20EINSPOT,%20I%E2%80%99d%20like%20to%20inquire%20about%20your%20services." target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg font-semibold text-einspot-red-600 bg-white hover:bg-gray-100 transition-colors duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"></path></svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>
@endsection

</body>
</html>
