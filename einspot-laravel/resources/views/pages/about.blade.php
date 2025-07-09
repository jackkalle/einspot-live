@extends('layouts.app')

@section('title', 'About Us - Einspot Solutions')

@section('content')
<div class="bg-white py-12 md:py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Hero Section for About Page --}}
        <section class="text-center py-12 md:py-16 bg-einspot-red-600 text-white rounded-xl shadow-lg">
            <h1 class="text-4xl sm:text-5xl font-bold mb-4">About EINSPOT SOLUTIONS</h1>
            <p class="text-xl sm:text-2xl max-w-3xl mx-auto">
                Your Trusted Partner in Engineering Excellence Since 2015.
            </p>
        </section>

        {{-- Company Story Section --}}
        <section class="py-12 md:py-16">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="prose lg:prose-lg max-w-none">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Story</h2>
                    <p>
                        Founded in 2015, EINSPOT SOLUTIONS NIG LTD has been at the forefront of engineering excellence in Nigeria.
                        We specialize in providing comprehensive HVAC, water heating, fire safety, and building automation solutions.
                        Our journey began with a commitment to delivering reliable and innovative services that meet the unique challenges of our environment.
                    </p>
                    <p>
                        As authorized distributors of Rheem products and partners with other leading global brands, we bring world-class engineering solutions
                        to Nigerian homes, businesses, and industries. Our dedication to quality workmanship, customer satisfaction, and sustainable practices
                        has established us as a trusted and respected name in the engineering sector.
                    </p>
                    <p>
                        Today, we continue to expand our expertise and services, driven by a passion for innovation and a commitment to building a better,
                        safer, and more efficient future for Nigeria.
                    </p>
                </div>
                <div>
                    <img src="https://via.placeholder.com/600x400/cccccc/808080?text=Our+Team+or+Facility"
                         alt="Einspot Solutions Team or Facility"
                         class="rounded-lg shadow-xl w-full object-cover">
                </div>
            </div>
        </section>

        {{-- Mission & Vision Section --}}
        <section class="py-12 md:py-16 bg-gray-100 rounded-xl">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-einspot-red-600 mb-4">Our Mission</h3>
                    <p class="text-gray-700">
                        To provide innovative, reliable, and sustainable engineering solutions that improve the quality of life
                        for our customers while contributing to Nigeria's infrastructural development through excellence and integrity.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-einspot-red-600 mb-4">Our Vision</h3>
                    <p class="text-gray-700">
                        To be the leading and most trusted engineering solutions provider in West Africa, recognized for our technical expertise,
                        customer-centric approach, and commitment to fostering sustainable development.
                    </p>
                </div>
            </div>
        </section>

        {{-- Core Values Section --}}
        <section class="py-12 md:py-16">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Our Core Values</h2>
            @php
                $coreValues = [
                    ['title' => 'Quality', 'icon' => '⭐', 'description' => 'We never compromise on the quality of our products and services, ensuring durability and performance.'],
                    ['title' => 'Reliability', 'icon' => '🔧', 'description' => 'Our solutions are built to last and perform consistently, providing peace of mind to our clients.'],
                    ['title' => 'Innovation', 'icon' => '💡', 'description' => 'We embrace new technologies and continuously improve our offerings to deliver cutting-edge solutions.'],
                    ['title' => 'Integrity', 'icon' => '🤝', 'description' => 'We conduct all business with the utmost honesty, transparency, and ethical practices.'],
                    ['title' => 'Customer Focus', 'icon' => '😊', 'description' => 'Our clients are at the heart of everything we do; their satisfaction is our top priority.'],
                    ['title' => 'Sustainability', 'icon' => '🌿', 'description' => 'We are committed to environmentally responsible practices and promoting energy efficiency.']
                ];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($coreValues as $value)
                <div class="text-center bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="text-4xl mb-4 text-einspot-red-500">{{ $value['icon'] }}</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $value['title'] }}</h3>
                    <p class="text-gray-600 text-sm">{{ $value['description'] }}</p>
                </div>
                @endforeach
            </div>
        </section>

        {{-- Team Section (Placeholder) --}}
        <section class="py-12 md:py-16 bg-gray-100 rounded-xl">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Meet Our Leadership</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Mock Team Member Data --}}
                @for ($i = 0; $i < 3; $i++)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden text-center">
                    <img src="https://via.placeholder.com/300x300/cccccc/808080?text=Team+Member" alt="Team Member {{ $i+1 }}" class="w-40 h-40 rounded-full mx-auto mt-6 mb-4 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800">Engr. Name Placeholder</h3>
                        <p class="text-einspot-red-600 font-medium">Position Placeholder</p>
                        <p class="text-gray-600 text-sm mt-2">Brief bio or expertise highlight for the team member.</p>
                    </div>
                </div>
                @endfor
            </div>
        </section>

        {{-- Certifications & Partnerships (Placeholder) --}}
        <section class="py-12 md:py-16">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Certifications & Partnerships</h2>
            <div class="flex flex-wrap justify-center items-center gap-8">
                <img src="https://via.placeholder.com/150x80/cccccc/808080?text=ISO+9001" alt="ISO 9001" class="h-16">
                <img src="https://via.placeholder.com/150x80/cccccc/808080?text=Rheem+Certified" alt="Rheem Certified" class="h-16">
                <img src="https://via.placeholder.com/150x80/cccccc/808080?text=COREN" alt="COREN Registered" class="h-16">
                <img src="https://via.placeholder.com/150x80/cccccc/808080?text=NFPA+Member" alt="NFPA Member" class="h-16">
            </div>
        </section>
    </div>
</div>
@endsection
