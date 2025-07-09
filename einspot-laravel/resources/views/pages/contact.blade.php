@extends('layouts.app')

@section('title', 'Contact Us - Einspot Solutions')

@section('content')
<div class="bg-white py-12 md:py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Hero Section for Contact Page --}}
        <section class="text-center py-12 md:py-16 bg-einspot-red-600 text-white rounded-xl shadow-lg">
            <h1 class="text-4xl sm:text-5xl font-bold mb-4">Get In Touch</h1>
            <p class="text-xl sm:text-2xl max-w-3xl mx-auto">
                We're here to help with all your engineering needs. Reach out to us today!
            </p>
        </section>

        {{-- Contact Form & Information Section --}}
        <section class="py-12 md:py-16">
            <div class="grid md:grid-cols-2 gap-12 items-start">

                {{-- Contact Form --}}
                <div class="bg-white p-8 rounded-xl shadow-2xl">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Us a Message</h2>
                    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-einspot-red-500 focus:border-transparent">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-einspot-red-500 focus:border-transparent">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-einspot-red-500 focus:border-transparent">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company (Optional)</label>
                                <input type="text" name="company" id="company" value="{{ old('company') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-einspot-red-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label for="service" class="block text-sm font-medium text-gray-700 mb-1">Service of Interest</label>
                            <select name="service" id="service"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-einspot-red-500 focus:border-transparent bg-white">
                                <option value="">Select a service (Optional)</option>
                                <option value="hvac_systems" @if(old('service') == 'hvac_systems') selected @endif>HVAC Systems</option>
                                <option value="water_heating" @if(old('service') == 'water_heating') selected @endif>Water Heating</option>
                                <option value="fire_safety" @if(old('service') == 'fire_safety') selected @endif>Fire Safety</option>
                                <option value="building_automation" @if(old('service') == 'building_automation') selected @endif>Building Automation</option>
                                <option value="electrical_engineering" @if(old('service') == 'electrical_engineering') selected @endif>Electrical Engineering</option>
                                <option value="plumbing_systems" @if(old('service') == 'plumbing_systems') selected @endif>Plumbing Systems</option>
                                <option value="consultation" @if(old('service') == 'consultation') selected @endif>Consultation & Project Supervision</option>
                                <option value="other" @if(old('service') == 'other') selected @endif>Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea name="message" id="message" rows="5" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-einspot-red-500 focus:border-transparent">{{ old('message') }}</textarea>
                            @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <button type="submit" class="btn-cta w-full">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Contact Information --}}
                <div class="space-y-8">
                    <div class="bg-gray-100 p-8 rounded-xl shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Contact Details</h3>
                        <div class="space-y-4">
                            {{-- Phone --}}
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-einspot-red-100 text-einspot-red-600 rounded-full">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-lg font-semibold text-gray-700">Phone</p>
                                    <a href="tel:{{ App\Models\Setting::getValue('contact_phone', '+2348123647982') }}" class="text-gray-600 hover:text-einspot-red-600">{{ App\Models\Setting::getValue('contact_phone', '+234 812 364 7982') }}</a>
                                </div>
                            </div>
                            {{-- Email --}}
                            <div class="flex items-start">
                                 <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-einspot-red-100 text-einspot-red-600 rounded-full">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-lg font-semibold text-gray-700">Email</p>
                                    <a href="mailto:{{ App\Models\Setting::getValue('contact_email', 'info@einspot.com.ng') }}" class="text-gray-600 hover:text-einspot-red-600">{{ App\Models\Setting::getValue('contact_email', 'info@einspot.com.ng') }}</a>
                                    {{-- <p class="text-gray-600">{{ App\Models\Setting::getValue('secondary_email', 'info@einspot.com') }}</p> --}}
                                </div>
                            </div>
                            {{-- Address --}}
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-einspot-red-100 text-einspot-red-600 rounded-full">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-lg font-semibold text-gray-700">Address</p>
                                    <p class="text-gray-600">{{ App\Models\Setting::getValue('address', 'Lagos, Nigeria') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-100 p-8 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Business Hours</h3>
                        <div class="space-y-2 text-gray-700">
                            <p><strong>Monday - Friday:</strong> {{ App\Models\Setting::getValue('hours_weekday', '8:00 AM - 6:00 PM') }}</p>
                            <p><strong>Saturday:</strong> {{ App\Models\Setting::getValue('hours_saturday', '9:00 AM - 4:00 PM') }}</p>
                            <p><strong>Sunday:</strong> {{ App\Models\Setting::getValue('hours_sunday', 'Closed') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Map Section (Placeholder) --}}
        <section class="py-12 md:py-16 bg-gray-200">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Find Us On The Map</h2>
                <div class="bg-white rounded-xl shadow-lg p-2">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-300 rounded-lg flex items-center justify-center">
                        {{-- Embed Google Map iframe here using Setting::getValue('google_maps_embed_url') --}}
                        @if(App\Models\Setting::getValue('google_maps_embed_url'))
                            <iframe src="{{ App\Models\Setting::getValue('google_maps_embed_url') }}" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @else
                            <p class="text-gray-500">Google Map will be embedded here.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
