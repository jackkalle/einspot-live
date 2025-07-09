@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' - Einspot Blog')

@section('meta_description', $post->meta_description ?: Str::limit(strip_tags($post->excerpt ?: $post->content), 160))
@section('meta_keywords', $post->meta_keywords ?: ($post->category ? $post->category->name . ', ' : '') . $post->title . ', einspot blog')
@section('canonical_url')
    <link rel="canonical" href="{{ route('blog.show', $post->slug) }}" />
@endsection

@push('styles')
{{-- Styles for prose content if not covered by a global typography plugin --}}
<style>
    .prose img { margin-left: auto; margin-right: auto; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .prose h2 { margin-top: 2em; margin-bottom: 1em; }
    .prose h3 { margin-top: 1.6em; margin-bottom: 0.8em; }
    .prose p, .prose ul, .prose ol { margin-bottom: 1.25em; }
    .prose a { color: #E53935; text-decoration: underline; } /* einspot-red-600 */
    .prose a:hover { color: #B71C1C; } /* einspot-red-900 */
</style>
@endpush


@section('content')
<div class="bg-white py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <article class="max-w-3xl mx-auto">
            {{-- Breadcrumbs --}}
            <nav class="mb-8 text-sm text-gray-500" aria-label="Breadcrumb">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <a href="{{ route('home') }}" class="hover:text-einspot-red-600">Home</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                    </li>
                    <li class="flex items-center">
                        <a href="{{ route('blog.index') }}" class="hover:text-einspot-red-600">Blog</a>
                        @if($post->category)
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                        @endif
                    </li>
                    @if($post->category)
                    <li class="flex items-center">
                        <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-einspot-red-600">{{ $post->category->name }}</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                    </li>
                    @endif
                    <li class="text-gray-700 truncate" aria-current="page" title="{{ $post->title }}">{{ Str::limit($post->title, 30) }}</li>
                </ol>
            </nav>

            {{-- Post Header --}}
            <header class="mb-8">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">{{ $post->title }}</h1>
                <div class="text-gray-600 text-sm flex items-center space-x-4">
                    @if($post->user)
                    <span>By <a href="#" class="text-einspot-red-600 hover:underline">{{ $post->user->name }}</a></span>
                    <span>&bull;</span>
                    @endif
                    <time datetime="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}">
                        {{ $post->published_at ? $post->published_at->format('F j, Y') : $post->created_at->format('F j, Y') }}
                    </time>
                    @if($post->category)
                    <span>&bull;</span>
                    <span>In <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="text-einspot-red-600 hover:underline">{{ $post->category->name }}</a></span>
                    @endif
                </div>
            </header>

            {{-- Featured Image --}}
            @if($post->image_url)
                <img src="{{ Storage::url($post->image_url) ?: 'https://via.placeholder.com/800x450/cccccc/808080?text=Blog+Post+Image' }}"
                     alt="Featured image for {{ $post->title }}"
                     class="w-full h-auto md:h-[450px] object-cover rounded-xl shadow-lg mb-8 md:mb-12">
            @endif

            {{-- Post Content --}}
            {{-- Ensure @tailwindcss/typography plugin is used for good default styling or style manually --}}
            <div class="prose prose-lg lg:prose-xl max-w-none text-gray-800 leading-relaxed">
                {!! $post->content !!} {{-- Assuming content is HTML from WYSIWYG. Use e() if it's plain text. --}}
            </div>

            {{-- Tags --}}
            @if($post->tags->isNotEmpty())
            <div class="mt-10 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-semibold text-gray-500 mb-3">TAGS:</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm hover:bg-gray-300 hover:text-gray-900 transition-colors">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Author Bio (Optional Placeholder) --}}
            @if($post->user)
            <div class="mt-12 pt-8 border-t border-gray-200 flex items-center bg-gray-50 p-6 rounded-lg shadow">
                {{-- <img src="https://via.placeholder.com/80x80/cccccc/808080?text={{ substr($post->user->name, 0, 1) }}" alt="{{ $post->user->name }}" class="w-16 h-16 rounded-full mr-4"> --}}
                <div>
                    <p class="text-xs text-gray-600 mb-1">WRITTEN BY</p>
                    <h4 class="text-lg font-semibold text-gray-800">{{ $post->user->name }}</h4>
                    {{-- <p class="text-gray-600 text-sm">Author bio placeholder. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p> --}}
                </div>
            </div>
            @endif

            {{-- TODO: Social Share Buttons --}}
            {{-- TODO: Comments Section (if required by project scope) --}}

        </article>

        {{-- Recent Posts Section --}}
        @if(isset($recentPosts) && $recentPosts->count() > 0)
        <section class="mt-16 pt-12 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Recent Articles</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($recentPosts as $recentPost)
                <article class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                    <a href="{{ route('blog.show', $recentPost->slug) }}" class="block aspect-video overflow-hidden">
                        <img src="{{ $recentPost->image_url ? Storage::url($recentPost->image_url) : 'https://via.placeholder.com/400x250/cccccc/808080?text=Blog+Image' }}"
                             alt="{{ $recentPost->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </a>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="text-md font-semibold text-gray-800 mb-2 flex-grow group-hover:text-einspot-red-700 transition-colors">
                            <a href="{{ route('blog.show', $recentPost->slug) }}">{{ Str::limit($recentPost->title, 60) }}</a>
                        </h3>
                         <time datetime="{{ $recentPost->published_at ? $recentPost->published_at->toIso8601String() : '' }}" class="text-xs text-gray-500 mb-2">
                            {{ $recentPost->published_at ? $recentPost->published_at->format('M d, Y') : '' }}
                        </time>
                        <div class="mt-auto">
                            <a href="{{ route('blog.show', $recentPost->slug) }}" class="text-sm text-einspot-red-600 hover:text-einspot-red-700 font-medium">Read More &rarr;</a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</div>
@endsection
