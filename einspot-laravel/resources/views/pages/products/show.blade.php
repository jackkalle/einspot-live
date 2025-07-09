@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name) . ' - Einspot Solutions')

@section('meta_description', $product->meta_description ?: Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->meta_keywords ?: ($product->category ? $product->category->name . ', ' : '') . $product->name . ', einspot products')
@section('canonical_url')
    <link rel="canonical" href="{{ route('products.show', $product->slug) }}" />
@endsection

@push('styles')
{{-- Add page-specific styles if needed, e.g., for a gallery --}}
<style>
    .product-gallery-main { max-height: 500px; }
    .product-gallery-thumbnail { cursor: pointer; border: 2px solid transparent; }
    .product-gallery-thumbnail.active { border-color: #E53935; /* einspot-red-600 */ }
</style>
@endpush

@section('content')
<div class="bg-white py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumbs --}}
        <nav class="mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-einspot-red-600">Home</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('products.index') }}" class="hover:text-einspot-red-600">Products</a>
                    @if($product->category)
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                    @endif
                </li>
                @if($product->category)
                <li class="flex items-center">
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-einspot-red-600">{{ $product->category->name }}</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                </li>
                @endif
                <li class="text-gray-700" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">
            {{-- Product Gallery --}}
            <div x-data="{ mainImage: '{{ $product->images && count($product->images) > 0 ? Storage::url($product->images[0]) : 'https://via.placeholder.com/600x500/cccccc/808080?text=No+Image' }}' }">
                <div class="mb-4 rounded-lg overflow-hidden shadow-lg">
                    <img x-bind:src="mainImage" alt="{{ $product->name }}" class="w-full h-auto object-contain product-gallery-main bg-gray-100">
                </div>
                @if($product->images && count($product->images) > 1)
                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                    @foreach($product->images as $imagePath)
                    <div>
                        <img src="{{ Storage::url($imagePath) }}" alt="{{ $product->name }} thumbnail"
                             class="w-full h-20 object-cover rounded-md product-gallery-thumbnail"
                             x-on:click="mainImage = '{{ Storage::url($imagePath) }}'"
                             :class="{ 'active': mainImage === '{{ Storage::url($imagePath) }}' }">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Product Details --}}
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">{{ $product->name }}</h1>

                @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-sm text-einspot-red-600 hover:text-einspot-red-700 font-semibold mb-4 inline-block">{{ $product->category->name }}</a>
                @endif

                <div class="text-3xl font-bold text-einspot-red-600 mb-6">
                    ₦{{ number_format($product->price, 2) }}
                </div>

                <div class="prose prose-lg text-gray-700 mb-6">
                    {!! nl2br(e($product->description)) !!}
                </div>

                @if($product->stock_quantity > 0)
                    <p class="text-green-600 font-semibold mb-1">In Stock ({{ $product->stock_quantity }} available)</p>
                @else
                    <p class="text-red-600 font-semibold mb-1">Out of Stock</p>
                @endif

                {{-- TODO: Add to Cart functionality if e-commerce is enabled --}}
                {{-- <div class="my-6">
                    <label for="quantity" class="sr-only">Quantity</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-20 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-einspot-red-500 focus:border-einspot-red-500">
                    <button type="submit" class="ml-4 btn-cta">Add to Cart</button>
                </div> --}}

                <div class="my-6 pt-6 border-t border-gray-200">
                    @php
                        $productName = $product->name;
                        $whatsappMessage = "Hello EINSPOT, I’d like a quote for: " . $productName . "\n\nName: \nCompany: \nLocation: \nQuantity:";
                        $whatsappNumber = App\Models\Setting::getValue('whatsapp_number', '2348123647982');
                        $whatsappLink = "https://wa.me/" . $whatsappNumber . "?text=" . rawurlencode($whatsappMessage);
                    @endphp
                     <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                        class="btn-cta w-full sm:w-auto text-center inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"></path></svg>
                        Quote on WhatsApp
                    </a>
                </div>

                @if($product->pdf_manual_path)
                <div class="mt-6">
                    <a href="{{ Storage::url($product->pdf_manual_path) }}" target="_blank"
                       class="inline-flex items-center text-einspot-red-600 hover:text-einspot-red-700 font-medium">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Download PDF Manual
                    </a>
                </div>
                @endif

                @if($product->tags->isNotEmpty())
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Tags:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->tags as $tag)
                            <a href="{{ route('products.index', ['tag' => $tag->slug]) }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-xs hover:bg-gray-300 transition-colors">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Related Products --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <section class="mt-16 pt-12 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                @foreach ($relatedProducts as $relatedProduct)
                    <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="block">
                            <img src="{{ $relatedProduct->images && count($relatedProduct->images) > 0 ? Storage::url($relatedProduct->images[0]) : 'https://via.placeholder.com/400x300/cccccc/808080?text=No+Image' }}"
                                 alt="{{ $relatedProduct->name }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        </a>
                        <div class="p-4 flex flex-col flex-grow">
                            <h3 class="text-md font-semibold text-gray-800 mb-1 flex-grow group-hover:text-einspot-red-700 transition-colors">
                                <a href="{{ route('products.show', $relatedProduct->slug) }}">{{ Str::limit($relatedProduct->name, 40) }}</a>
                            </h3>
                            <p class="text-lg font-bold text-einspot-red-600 mb-3">
                                ₦{{ number_format($relatedProduct->price, 2) }}
                            </p>
                            <a href="{{ route('products.show', $relatedProduct->slug) }}" class="mt-auto btn-cta-outline w-full text-center text-xs py-2">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</div>

@push('scripts')
{{-- Alpine.js for simple gallery interactivity, assuming it's available or loaded via app.js --}}
{{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
@endpush

@endsection
