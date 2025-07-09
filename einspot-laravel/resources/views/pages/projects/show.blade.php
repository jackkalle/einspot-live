@extends('layouts.app')

@section('title', ($project->meta_title ?: $project->title) . ' - Einspot Solutions Projects')

@section('meta_description', $project->meta_description ?: Str::limit(strip_tags($project->description), 160))
@section('meta_keywords', $project->meta_keywords ?: $project->title . ', ' . $project->type . ', einspot projects, ' . $project->client)
@section('canonical_url')
    <link rel="canonical" href="{{ route('projects.show', $project->slug) }}" />
@endsection

@section('content')
<div class="bg-white py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumbs --}}
        <nav class="mb-8 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-einspot-red-600">Home</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('projects.index') }}" class="hover:text-einspot-red-600">Projects</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                </li>
                <li class="text-gray-700" aria-current="page">{{ $project->title }}</li>
            </ol>
        </nav>

        <article>
            {{-- Project Header --}}
            <header class="mb-8 md:mb-12">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-3">{{ $project->title }}</h1>
                <div class="flex flex-wrap text-sm text-gray-600 gap-x-4 gap-y-1">
                    @if($project->client)<span><strong>Client:</strong> {{ $project->client }}</span>@endif
                    @if($project->location)<span><strong>Location:</strong> {{ $project->location }}</span>@endif
                    @if($project->duration)<span><strong>Duration:</strong> {{ $project->duration }}</span>@endif
                    @if($project->status)
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $project->status === 'Completed' ? 'bg-green-100 text-green-800' : ($project->status === 'Ongoing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            Status: {{ $project->status }}
                        </span>
                    @endif
                    @if($project->type)<span><strong>Type:</strong> {{ $project->type }}</span>@endif
                </div>
            </header>

            {{-- Project Main Image --}}
            @if($project->image_url)
                <img src="{{ Storage::url($project->image_url) ?: 'https://via.placeholder.com/1200x600/cccccc/808080?text=Project+Main+Image' }}"
                     alt="Main image for {{ $project->title }}"
                     class="w-full h-auto md:h-[500px] object-cover rounded-xl shadow-lg mb-8 md:mb-12">
            @endif

            {{-- Project Description --}}
            <div class="prose prose-lg lg:prose-xl max-w-none text-gray-700 leading-relaxed mb-8 md:mb-12">
                {!! nl2br(e($project->description)) !!}
            </div>

            {{-- Additional Images Gallery (if any) --}}
            @if($project->images && count($project->images) > 0)
            <section class="mb-8 md:mb-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Project Gallery</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($project->images as $imagePath)
                    <div class="rounded-lg overflow-hidden shadow-md">
                        <img src="{{ Storage::url($imagePath) }}" alt="{{ $project->title }} gallery image" class="w-full h-56 object-cover">
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Brands Used & Technologies --}}
            <div class="grid md:grid-cols-2 gap-8 mb-8 md:mb-12">
                @if($project->brands_used && count($project->brands_used) > 0)
                <div class="bg-gray-50 p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Brands Used</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->brands_used as $brand)
                        <span class="px-3 py-1 bg-einspot-red-100 text-einspot-red-700 rounded-full text-sm font-medium">{{ $brand }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($project->technologies && count($project->technologies) > 0)
                <div class="bg-gray-50 p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Technologies Featured</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->technologies as $tech)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Call to Action for similar service --}}
            <div class="mt-10 py-8 border-t border-gray-200 text-center">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Need a Similar Project Handled?</h3>
                <p class="text-gray-600 mb-6 max-w-xl mx-auto">
                    Our team is ready to bring the same level of expertise and dedication to your engineering challenges.
                </p>
                @php
                    $whatsappMessage = "Hello EINSPOT, I'm interested in a project similar to '" . $project->title . "'.";
                    $whatsappLink = "https://wa.me/" . App\Models\Setting::getValue('whatsapp_number', '2348123647982') . "?text=" . rawurlencode($whatsappMessage);
                @endphp
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn-cta mr-2">
                    Discuss on WhatsApp
                </a>
                <a href="{{ route('contact.form', ['project_inquiry' => $project->slug]) }}" class="btn-cta-outline">
                    Request Formal Quote
                </a>
            </div>

        </article>

        {{-- Related/Recent Projects --}}
        @if(isset($recentProjects) && $recentProjects->count() > 0)
        <section class="mt-16 pt-12 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Other Recent Projects</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach ($recentProjects as $recentProject)
                    <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
                        <a href="{{ route('projects.show', $recentProject->slug) }}" class="block aspect-[4/3] overflow-hidden">
                            <img src="{{ $recentProject->image_url ? Storage::url($recentProject->image_url) : 'https://via.placeholder.com/600x450/cccccc/808080?text=Project+Image' }}"
                                 alt="{{ $recentProject->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </a>
                        <div class="p-4 flex flex-col flex-grow">
                            <h3 class="text-md font-semibold text-gray-800 mb-2 flex-grow group-hover:text-einspot-red-700 transition-colors">
                                <a href="{{ route('projects.show', $recentProject->slug) }}">{{ Str::limit($recentProject->title, 50) }}</a>
                            </h3>
                             @if($recentProject->type)
                            <span class="text-xs text-gray-500 mb-2">{{ $recentProject->type }}</span>
                            @endif
                            <div class="mt-auto">
                                <a href="{{ route('projects.show', $recentProject->slug) }}" class="btn-cta-outline text-xs py-1.5 px-3 inline-block w-full text-center">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

    </div>
</div>
@endsection
