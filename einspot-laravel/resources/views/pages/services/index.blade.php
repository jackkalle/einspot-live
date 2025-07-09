@extends('layouts.app')

@section('title', 'Our Engineering Services - Einspot Solutions')

@section('content')
<div class="bg-white py-8 md:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Hero Section for Services Page --}}
        <section class="text-center py-12 md:py-16 bg-einspot-red-50 rounded-xl shadow-md mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-einspot-red-700 mb-3">Our Engineering Services</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Professional, reliable, and innovative solutions tailored to your needs across Nigeria.
            </p>
        </section>

        {{-- Services Grid --}}
        @if(isset($services) && $services->count() > 0)
        <section class="py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-12">
                @foreach ($services as $service)
                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col md:flex-row items-center">
                    @if($service->image_url)
                    <div class="md:w-2/5 h-64 md:h-auto">
                        <img src="{{ Storage::url($service->image_url) ?: 'https://via.placeholder.com/400x300/cccccc/808080?text=Service+Image' }}"
                             alt="{{ $service->name }}"
                             class="w-full h-full object-cover md:rounded-l-2xl md:rounded-r-none group-hover:scale-105 transition-transform duration-500">
                    </div>
                    @endif
                    <div class="p-6 md:p-8 {{ $service->image_url ? 'md:w-3/5' : 'w-full' }}">
                        <div class="flex items-center mb-3">
                            @if($service->icon_path)
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-red-100 text-einspot-red-600 rounded-full mr-3">
                                <img src="{{ Storage::url($service->icon_path) }}" alt="{{ $service->name }} icon" class="h-6 w-6 object-contain">
                                {{-- Or use SVG path if icon_path stores SVG data --}}
                            </div>
                            @endif
                            <h2 class="text-2xl font-bold text-gray-800 group-hover:text-einspot-red-600 transition-colors">
                                <a href="{{ route('services.show', $service->slug) }}">{{ $service->name }}</a>
                            </h2>
                        </div>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed line-clamp-3">
                            {{ Str::limit($service->description, 150) }}
                        </p>

                        @if($service->features && count($service->features) > 0)
                        <ul class="text-sm space-y-1 mb-4 text-gray-700">
                            @foreach(collect($service->features)->take(3) as $feature)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-einspot-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                        @endif
                        <div class="mt-auto">
                            <a href="{{ route('services.show', $service->slug) }}" class="btn-cta-outline text-sm py-2 px-4 inline-block">
                                Learn More & Request Quote
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @else
            <div class="text-center py-12">
                <p class="text-xl text-gray-500">No services currently available. Please check back soon.</p>
            </div>
        @endif

        {{-- Why Choose Us Section (similar to original design) --}}
        <section class="py-16 mt-12 bg-gray-100 rounded-xl">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Why Choose EINSPOT?</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Combining technical expertise with reliable service delivery for your project's success.
                    </p>
                </div>
                @php
                    $whyChooseUsItems = [
                        ['icon' => '🏆', 'title' => '10+ Years Experience', 'desc' => 'Proven track record in the Nigerian market.'],
                        ['icon' => '🔧', 'title' => 'Expert Certified Team', 'desc' => 'Skilled engineers and technicians.'],
                        ['icon' => '⚡', 'title' => 'Prompt & Efficient', 'desc' => 'Quick turnaround on all projects.'],
                        ['icon' => '🛡️', 'title' => 'Quality Guaranteed', 'desc' => 'Commitment to 100% satisfaction.']
                    ];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach ($whyChooseUsItems as $item)
                    <div class="text-center bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="text-3xl mb-3 text-einspot-red-500">{{ $item['icon'] }}</div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 text-sm">{{ $item['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
