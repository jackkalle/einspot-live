@extends('layouts.app')

@section('title', 'Our Projects - Einspot Solutions')

@section('content')
<div class="bg-gray-50 py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Hero Section for Projects Page --}}
        <section class="text-center py-12 md:py-16 bg-einspot-red-50 rounded-xl shadow-md mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-einspot-red-700 mb-3">Our Project Portfolio</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Showcasing our engineering excellence and successful project deliveries across Nigeria.
            </p>
        </section>

        {{-- Optional: Filter by Project Type if $projectTypes is available --}}
        @if(isset($projectTypes) && $projectTypes->count() > 0)
        <section class="mb-12">
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('projects.index', array_filter(request()->except(['type', 'page']))) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ !request('type') ? 'bg-einspot-red-600 text-white hover:bg-einspot-red-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    All Projects
                </a>
                @foreach($projectTypes as $type)
                <a href="{{ route('projects.index', array_merge(request()->except(['page']), ['type' => $type])) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ request('type') == $type ? 'bg-einspot-red-600 text-white hover:bg-einspot-red-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ $type }}
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Projects Grid / Bento Grid --}}
        @if(isset($projects) && $projects->count() > 0)
        <section>
            {{-- Guideline: Bento Grid for Project Showcase (3-column responsive) --}}
            {{-- This example uses a standard responsive grid. A true bento might need more complex CSS/JS for varying item sizes. --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($projects as $project)
                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
                    <a href="{{ route('projects.show', $project->slug) }}" class="block aspect-[4/3] overflow-hidden">
                        <img src="{{ $project->image_url ? Storage::url($project->image_url) : 'https://via.placeholder.com/600x450/cccccc/808080?text=Project+Image' }}"
                             alt="{{ $project->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </a>
                    <div class="p-6 flex flex-col flex-grow">
                        @if($project->type)
                        <span class="text-xs text-einspot-red-600 font-semibold uppercase mb-1">{{ $project->type }}</span>
                        @endif
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 flex-grow group-hover:text-einspot-red-700 transition-colors">
                            <a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a>
                        </h3>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Client:</span> {{ $project->client ?: 'N/A' }}</p>
                        <p class="text-sm text-gray-600 mb-3"><span class="font-medium">Location:</span> {{ $project->location ?: 'N/A' }}</p>

                        <p class="text-gray-700 text-sm mb-4 line-clamp-3 flex-grow">
                            {{ Str::limit($project->description, 120) }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('projects.show', $project->slug) }}" class="btn-cta-outline text-sm py-2 px-4 inline-block w-full text-center">
                                View Project Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $projects->links() }}
            </div>
        </section>
        @else
            <div class="text-center py-16">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-xl font-medium text-gray-900">No projects found</h3>
                <p class="mt-1 text-gray-500">
                    @if(request('type'))
                        No projects found for this type. Try selecting 'All Projects'.
                    @else
                        We are currently updating our portfolio. Please check back soon!
                    @endif
                </p>
                 @if(request('type'))
                    <div class="mt-6">
                        <a href="{{ route('projects.index') }}" class="btn-cta-outline">
                            View All Projects
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- Statistics Section (from original ProjectsPage component) --}}
        <section class="py-16 mt-12 bg-white rounded-xl shadow-md">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8 text-center">
                    @php
                        $stats = [
                            ['icon' => '🏆', 'number' => App\Models\Setting::getValue('projects_completed_stat', '150+'), 'label' => 'Projects Completed'],
                            ['icon' => '😊', 'number' => App\Models\Setting::getValue('happy_clients_stat', '50+'), 'label' => 'Happy Clients'],
                            ['icon' => '📅', 'number' => App\Models\Setting::getValue('years_experience_stat', '10+'), 'label' => 'Years Experience'],
                            ['icon' => '🚀', 'number' => App\Models\Setting::getValue('support_availability_stat', '24/7'), 'label' => 'Support Available']
                        ];
                    @endphp
                    @foreach ($stats as $stat)
                    <div class="bg-gray-50 rounded-xl p-6 hover:bg-gray-100 transition-colors">
                        <div class="text-3xl mb-3 text-einspot-red-500">{{ $stat['icon'] }}</div>
                        <div class="text-3xl font-bold text-einspot-red-600 mb-1">{{ $stat['number'] }}</div>
                        <div class="text-gray-600 font-medium">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
