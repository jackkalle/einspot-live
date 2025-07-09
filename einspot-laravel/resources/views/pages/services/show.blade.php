@extends('layouts.app')

@section('title', $service->name . ' - Einspot Solutions')

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
                    <a href="{{ route('services.index') }}" class="hover:text-einspot-red-600">Services</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                </li>
                <li class="text-gray-700" aria-current="page">{{ $service->name }}</li>
            </ol>
        </nav>

        <div class="lg:flex lg:gap-12">
            {{-- Main Content Area --}}
            <div class="lg:w-2/3">
                @if($service->image_url)
                    <img src="{{ Storage::url($service->image_url) ?: 'https://via.placeholder.com/800x500/cccccc/808080?text=Service+Image' }}"
                         alt="{{ $service->name }}"
                         class="w-full h-auto md:h-[400px] object-cover rounded-xl shadow-lg mb-8">
                @endif

                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">{{ $service->name }}</h1>

                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed mb-8">
                    {!! nl2br(e($service->description)) !!}
                </div>

                @if($service->features && count($service->features) > 0)
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Key Features & Benefits</h3>
                    <ul class="space-y-3">
                        @foreach($service->features as $feature)
                        <li class="flex items-start">
                            <svg class="flex-shrink-0 h-6 w-6 text-einspot-red-500 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Call to Action for this service --}}
                <div class="mt-10 p-6 bg-red-50 border border-einspot-red-200 rounded-lg text-center">
                    <h3 class="text-2xl font-semibold text-einspot-red-700 mb-3">Interested in this Service?</h3>
                    <p class="text-gray-700 mb-6">
                        Let us help you with your next project. Contact us for a personalized quote or consultation.
                    </p>
                    @php
                        $whatsappMessage = $service->whatsapp_text ? $service->whatsapp_text : "Hello EINSPOT, I'd like a quote for the " . $service->name . " service.";
                        $whatsappLink = "https://wa.me/" . App\Models\Setting::getValue('whatsapp_number', '2348123647982') . "?text=" . rawurlencode($whatsappMessage . "\n\nName: \nCompany: \nLocation: \nDetails: ");
                    @endphp
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                       class="btn-cta inline-flex items-center mr-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"></path></svg>
                        Chat on WhatsApp
                    </a>
                    <a href="{{ route('contact.form', ['service' => $service->slug]) }}" class="btn-cta-outline inline-flex items-center">
                        Request Formal Quote
                    </a>
                </div>

            </div>

            {{-- Sidebar - Other Services --}}
            <aside class="lg:w-1/3 space-y-6 mt-12 lg:mt-0">
                <div class="sticky top-24 p-6 bg-gray-50 rounded-xl shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Other Services</h3>
                    @php
                        $otherServices = \App\Models\Service::where('id', '!=', $service->id)->latest()->take(5)->get();
                    @endphp
                    @if($otherServices->count() > 0)
                    <ul class="space-y-3">
                        @foreach($otherServices as $otherService)
                        <li>
                            <a href="{{ route('services.show', $otherService->slug) }}"
                               class="flex items-center p-3 rounded-lg hover:bg-red-100 group transition-colors">
                                @if($otherService->icon_path)
                                <img src="{{ Storage::url($otherService->icon_path) }}" alt="" class="h-6 w-6 mr-3 object-contain">
                                @else
                                <span class="h-6 w-6 mr-3 text-einspot-red-500">●</span>
                                @endif
                                <span class="text-gray-700 group-hover:text-einspot-red-700">{{ $otherService->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('services.index') }}" class="block text-center mt-6 btn-cta text-sm w-full py-2">View All Services</a>
                    @else
                    <p class="text-gray-600">No other services available at the moment.</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
