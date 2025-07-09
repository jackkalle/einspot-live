@extends('layouts.app')

@php
    // Determine title based on whether a category or tag is active
    $pageTitle = 'All Products';
    if (request()->has('category') && $activeCategory = $categories->firstWhere('slug', request('category'))) {
        $pageTitle = $activeCategory->name . ' Products';
    } elseif (request()->has('tag') && $activeTag = $tags->firstWhere('slug', request('tag'))) {
        $pageTitle = 'Products tagged with ' . $activeTag->name;
    } elseif (request()->has('q')) {
        $pageTitle = 'Search Results for "' . request('q') . '"';
    }
@endphp

@section('title', $pageTitle . ' - Einspot Solutions')

@section('content')
<div class="bg-white py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Hero/Title Section --}}
        <section class="text-center py-10 md:py-12 bg-einspot-red-50 rounded-xl shadow-md mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-einspot-red-700 mb-3">{{ $pageTitle }}</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Browse our wide range of high-quality engineering products.
            </p>
        </section>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Filters Sidebar --}}
            <aside class="w-full lg:w-1/4">
                <div class="sticky top-24 space-y-6"> {{-- Sticky for desktop --}}
                    {{-- Search within products --}}
                    <div class="bg-gray-50 p-6 rounded-lg shadow">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Search Products</h3>
                        <form action="{{ route('products.index') }}" method="GET"> {{-- Or products.search --}}
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by keyword..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-einspot-red-500 mb-3">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('tag'))
                                <input type="hidden" name="tag" value="{{ request('tag') }}">
                            @endif
                            <button type="submit" class="w-full btn-cta text-sm py-2">Search</button>
                        </form>
                    </div>

                    {{-- Categories Filter --}}
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="bg-gray-50 p-6 rounded-lg shadow">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Product Categories</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('products.index', array_filter(request()->except(['category', 'page']))) }}"
                                   class="block px-3 py-2 rounded-md hover:bg-red-100 hover:text-einspot-red-700 transition-colors {{ !request('category') ? 'bg-einspot-red-100 text-einspot-red-700 font-semibold' : 'text-gray-700' }}">
                                    All Categories
                                </a>
                            </li>
                            @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('products.index', array_merge(request()->except(['page']), ['category' => $category->slug])) }}"
                                   class="block px-3 py-2 rounded-md hover:bg-red-100 hover:text-einspot-red-700 transition-colors {{ request('category') == $category->slug ? 'bg-einspot-red-100 text-einspot-red-700 font-semibold' : 'text-gray-700' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Tags Filter --}}
                    @if(isset($tags) && $tags->count() > 0)
                    <div class="bg-gray-50 p-6 rounded-lg shadow">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Product Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                            <a href="{{ route('products.index', array_merge(request()->except(['page']), ['tag' => $tag->slug])) }}"
                               class="px-3 py-1 rounded-full text-sm transition-colors
                                      {{ request('tag') == $tag->slug ? 'bg-einspot-red-600 text-white hover:bg-einspot-red-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                         @if(request('tag'))
                            <a href="{{ route('products.index', array_filter(request()->except(['tag', 'page']))) }}" class="mt-3 inline-block text-xs text-einspot-red-600 hover:underline">Clear tag filter</a>
                        @endif
                    </div>
                    @endif

                    {{-- TODO: Add other filters like price range if needed --}}
                </div>
            </aside>

            {{-- Products Grid --}}
            <main class="w-full lg:w-3/4">
                @if(isset($products) && $products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 md:gap-8">
                        @foreach ($products as $product)
                            <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
                                <a href="{{ route('products.show', $product->slug) }}" class="block">
                                    <img src="{{ $product->images && count($product->images) > 0 ? Storage::url($product->images[0]) : 'https://via.placeholder.com/400x300/cccccc/808080?text=No+Image' }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300">
                                </a>
                                <div class="p-6 flex flex-col flex-grow">
                                    @if($product->category)
                                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-xs text-einspot-red-600 hover:text-einspot-red-700 font-semibold uppercase mb-1">{{ $product->category->name }}</a>
                                    @endif
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2 flex-grow group-hover:text-einspot-red-700 transition-colors">
                                        <a href="{{ route('products.show', $product->slug) }}">{{ Str::limit($product->name, 50) }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2 flex-grow">{{ Str::limit($product->description, 70) }}</p>

                                    <div class="my-3">
                                        @if($product->tags->isNotEmpty())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($product->tags->take(3) as $tag)
                                                    <a href="{{ route('products.index', ['tag' => $tag->slug]) }}" class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full text-xs hover:bg-gray-300">#{{ $tag->name }}</a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-xl font-bold text-einspot-red-600 mb-4">
                                        ₦{{ number_format($product->price, 2) }}
                                    </div>
                                    <a href="{{ route('products.show', $product->slug) }}" class="mt-auto btn-cta w-full text-center text-sm py-2.5">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-12">
                        {{ $products->links() }} {{-- Make sure Laravel's default paginator is styled for Tailwind --}}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-xl font-medium text-gray-900">No products found</h3>
                        <p class="mt-1 text-gray-500">
                            @if(request('q') || request('category') || request('tag'))
                                Try adjusting your search or filters.
                            @else
                                Check back soon for new arrivals or explore other categories.
                            @endif
                        </p>
                        @if(request('q') || request('category') || request('tag'))
                            <div class="mt-6">
                                <a href="{{ route('products.index') }}" class="btn-cta-outline">
                                    Clear Filters & Search
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
