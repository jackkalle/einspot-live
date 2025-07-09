@extends('layouts.app')

@php
    $pageTitle = 'Engineering Insights Blog';
    if (request()->has('category') && isset($activeCategory) && $activeCategory) {
        $pageTitle = $activeCategory->name . ' Articles';
    } elseif (request()->has('tag') && isset($activeTag) && $activeTag) {
        $pageTitle = 'Articles tagged with ' . $activeTag->name;
    } elseif (request()->has('q')) {
        $pageTitle = 'Search Results for "' . request('q') . '"';
    }
@endphp

@section('title', $pageTitle . ' - Einspot Solutions')

@section('content')
<div class="bg-gray-50 py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Hero Section for Blog Page --}}
        <section class="text-center py-10 md:py-12 bg-einspot-red-50 rounded-xl shadow-md mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-einspot-red-700 mb-3">{{ $pageTitle }}</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Stay updated with the latest trends, tips, and insights in engineering solutions from our experts.
            </p>
        </section>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Blog Posts Grid --}}
            <main class="w-full lg:w-2/3">
                @if(isset($posts) && $posts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach ($posts as $post)
                            <article class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
                                <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video overflow-hidden">
                                    <img src="{{ $post->image_url ? Storage::url($post->image_url) : 'https://via.placeholder.com/400x250/cccccc/808080?text=Blog+Image' }}"
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </a>
                                <div class="p-6 flex flex-col flex-grow">
                                    <div class="mb-3">
                                        @if($post->category)
                                        <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="text-xs text-einspot-red-600 hover:text-einspot-red-700 font-semibold uppercase">{{ $post->category->name }}</a>
                                        @endif
                                        <span class="text-xs text-gray-500 mx-1">&bull;</span>
                                        <time datetime="{{ $post->published_at ? $post->published_at->toIso8601String() : '' }}" class="text-xs text-gray-500">
                                            {{ $post->published_at ? $post->published_at->format('M d, Y') : ($post->created_at ? $post->created_at->format('M d, Y') : 'N/A') }}
                                        </time>
                                    </div>
                                    <h2 class="text-xl font-semibold text-gray-800 mb-3 flex-grow group-hover:text-einspot-red-700 transition-colors">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ Str::limit($post->title, 70) }}</a>
                                    </h2>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow">
                                        {{ Str::limit($post->excerpt ?: strip_tags($post->content), 150) }}
                                    </p>
                                    <div class="mt-auto">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="btn-cta-outline text-sm py-2 px-4 inline-block">
                                            Read More
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-12">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="text-center py-16 col-span-full">
                         <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-xl font-medium text-gray-900">No articles found</h3>
                        <p class="mt-1 text-gray-500">
                            @if(request('q') || request('category') || request('tag'))
                                Try adjusting your search or filters.
                            @else
                                We're busy writing new content. Please check back soon!
                            @endif
                        </p>
                        @if(request('q') || request('category') || request('tag'))
                            <div class="mt-6">
                                <a href="{{ route('blog.index') }}" class="btn-cta-outline">
                                    Clear Filters & Search
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </main>

            {{-- Sidebar --}}
            <aside class="w-full lg:w-1/3 space-y-8 mt-12 lg:mt-0">
                <div class="sticky top-24">
                    {{-- Search Form --}}
                    <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Search Blog</h3>
                        <form action="{{ route('blog.index') }}" method="GET">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search articles..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-einspot-red-500 mb-3">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                             @if(request('tag'))
                                <input type="hidden" name="tag" value="{{ request('tag') }}">
                            @endif
                            <button type="submit" class="w-full btn-cta text-sm py-2">Search</button>
                        </form>
                    </div>

                    {{-- Categories --}}
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Categories</h3>
                        <ul class="space-y-2">
                             <li>
                                <a href="{{ route('blog.index', array_filter(request()->except(['category','tag','page','q']))) }}"
                                   class="block px-3 py-2 rounded-md hover:bg-red-50 hover:text-einspot-red-700 transition-colors {{ !request('category') ? 'text-einspot-red-700 font-semibold bg-red-50' : 'text-gray-700' }}">
                                    All Categories
                                </a>
                            </li>
                            @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('blog.index', array_merge(request()->except(['page','q','tag']), ['category' => $category->slug])) }}"
                                   class="block px-3 py-2 rounded-md hover:bg-red-50 hover:text-einspot-red-700 transition-colors {{ request('category') == $category->slug ? 'text-einspot-red-700 font-semibold bg-red-50' : 'text-gray-700' }}">
                                    {{ $category->name }}
                                    {{-- Optional: Add count ( $category->blog_posts_count ) --}}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Tags --}}
                    @if(isset($tags) && $tags->count() > 0)
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Popular Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                            <a href="{{ route('blog.index', array_merge(request()->except(['page','q','category']), ['tag' => $tag->slug])) }}"
                               class="px-3 py-1 rounded-full text-sm transition-colors
                                      {{ request('tag') == $tag->slug ? 'bg-einspot-red-600 text-white hover:bg-einspot-red-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                        @if(request('tag'))
                            <a href="{{ route('blog.index', array_filter(request()->except(['tag', 'page','q','category']))) }}" class="mt-4 inline-block text-xs text-einspot-red-600 hover:underline">Clear tag filter</a>
                        @endif
                    </div>
                    @endif

                    {{-- Newsletter Signup (from original design) --}}
                    <div class="mt-8 p-6 bg-einspot-red-600 text-white rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold mb-3">Stay Updated</h3>
                        <p class="text-sm mb-4">Subscribe for the latest engineering insights.</p>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <input type="email" name="email" placeholder="Enter your email" required class="w-full px-4 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white mb-3" />
                            <button type="submit" class="w-full bg-white text-einspot-red-600 py-2 rounded-lg hover:bg-gray-100 transition font-semibold">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
