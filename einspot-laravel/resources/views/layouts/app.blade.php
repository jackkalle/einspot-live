<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- For AJAX requests if needed --}}

    <title>@yield('title', config('app.name', 'Einspot Solutions'))</title>

    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
    {{-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}
    {{-- Using Poppins or Inter as per guidelines - will need to be properly linked later --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <!-- Styles -->
    {{-- Scripts and styles will be compiled here using Vite --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    {{-- Placeholder for global CSS. Will integrate Tailwind/Bootstrap later --}}
    <title>@yield('title', config('app.name', 'Einspot Solutions'))</title>
    <meta name="description" content="@yield('meta_description', 'Einspot Solutions offers top-tier engineering services including HVAC, Water Heating, Fire Safety, and Building Automation in Nigeria.')">
    <meta name="keywords" content="@yield('meta_keywords', 'einspot, engineering, nigeria, hvac, water heating, fire safety, building automation, rheem')">
    @yield('canonical_url')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    {{-- Assuming Vite setup for Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Inline styles for basic structure if app.css isn't fully functional yet in sandbox --}}
    <style>
        body { font-family: 'Poppins', 'Inter', sans-serif; }
        /* Basic alert styling - can be replaced by Tailwind components */
        .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 0.375rem; }
        .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc;}
        .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7;}
    </style>

    @stack('styles') {{-- For page-specific styles --}}
</head>
<body class="font-poppins antialiased bg-gray-50 text-gray-800">
    <div id="app" class="min-h-screen flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow-md sticky top-0 z-50">
            <!-- Top Bar -->
            <div class="bg-gray-100 text-xs text-gray-600">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col sm:flex-row justify-between items-center">
                    <div class="flex items-center space-x-4 mb-1 sm:mb-0">
                        <span>🌍 Nigeria</span>
                        <span>📞 {{ App\Models\Setting::getValue('contact_phone', '+234 812 364 7982') }}</span>
                        <span>📧 {{ App\Models\Setting::getValue('contact_email', 'info@einspot.com.ng') }}</span>
                    </div>
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="#" class="hover:text-red-600 transition-colors">Warranties</a>
                        <a href="#" class="hover:text-red-600 transition-colors">Resources</a>
                        <a href="#" class="hover:text-red-600 transition-colors">Careers</a>
                    </div>
                </div>
            </div>

            <!-- Main Navigation -->
            <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800 hover:text-red-600 transition-colors">
                            EINSPOT <span class="text-red-600">SOLUTIONS</span>
                        </a>
                    </div>

                    <div class="hidden lg:flex items-center space-x-6">
                        <a href="{{ route('products.index') }}" class="font-medium {{ request()->routeIs('products.index') ? 'text-red-600' : 'text-gray-700 hover:text-red-600' }} transition-colors">Products</a>
                        <a href="{{ route('services.index') }}" class="font-medium {{ request()->routeIs('services.index') ? 'text-red-600' : 'text-gray-700 hover:text-red-600' }} transition-colors">Services</a>
                        <a href="{{ route('projects.index') }}" class="font-medium {{ request()->routeIs('projects.index') ? 'text-red-600' : 'text-gray-700 hover:text-red-600' }} transition-colors">Projects</a>
                        <a href="{{ route('blog.index') }}" class="font-medium {{ request()->routeIs('blog.index') ? 'text-red-600' : 'text-gray-700 hover:text-red-600' }} transition-colors">Blog</a>
                        <a href="{{ route('about') }}" class="font-medium {{ request()->routeIs('about') ? 'text-red-600' : 'text-gray-700 hover:text-red-600' }} transition-colors">About</a>
                        <a href="{{ route('contact.form') }}" class="font-medium {{ request()->routeIs('contact.form') ? 'text-red-600' : 'text-gray-700 hover:text-red-600' }} transition-colors">Contact</a>
                    </div>

                    <div class="flex items-center space-x-3">
                        {{-- Search Bar Placeholder --}}
                        <div class="hidden md:block">
                             <form action="{{ route('products.search') }}" method="GET">
                                <input type="text" name="q" placeholder="Search..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-red-500">
                            </form>
                        </div>

                        @guest
                            <a href="{{ route('login') }}" class="hidden sm:inline-block text-gray-700 hover:text-red-600 font-medium transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">Sign Up</a>
                        @endguest
                        @auth
                            <div class="relative group">
                                <button class="flex items-center space-x-1 text-gray-700 hover:text-red-600 transition-colors">
                                    <span class="font-medium text-sm">{{ Auth::user()->firstName ?? Auth::user()->name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    @if(Auth::user()->isAdmin)
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                    @endif
                                    {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Dashboard</a> --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @endauth

                        {{-- Mobile Menu Toggle --}}
                        <button class="lg:hidden p-2 rounded-md text-gray-600 hover:text-red-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500" aria-label="Open menu" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                </div>
                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden lg:hidden py-2">
                    <a href="{{ route('products.index') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 rounded">Products</a>
                    <a href="{{ route('services.index') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 rounded">Services</a>
                    <a href="{{ route('projects.index') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 rounded">Projects</a>
                    <a href="{{ route('blog.index') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 rounded">Blog</a>
                    <a href="{{ route('about') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 rounded">About</a>
                    <a href="{{ route('contact.form') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 rounded">Contact</a>
                     <div class="mt-2 pt-2 border-t border-gray-200">
                        @guest
                            <a href="{{ route('login') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 rounded">Login</a>
                            <a href="{{ route('register') }}" class="block mt-1 bg-red-600 text-white text-center px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">Sign Up</a>
                        @endguest
                    </div>
                </div>
            </nav>
        </header>

        <!-- Page Content -->
        <main class="flex-grow">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                {{-- @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-gray-300 py-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-3">EINSPOT SOLUTIONS</h5>
                        <p class="text-sm">{{ App\Models\Setting::getValue('footer_address', 'Lagos, Nigeria') }}</p>
                        <p class="text-sm">Phone: {{ App\Models\Setting::getValue('contact_phone', '+234 812 364 7982') }}</p>
                        <p class="text-sm">Email: {{ App\Models\Setting::getValue('contact_email', 'info@einspot.com.ng') }}</p>
                    </div>
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-3">Quick Links</h5>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('about') }}" class="hover:text-red-400 transition-colors">About Us</a></li>
                            <li><a href="{{ route('products.index') }}" class="hover:text-red-400 transition-colors">Products</a></li>
                            <li><a href="{{ route('services.index') }}" class="hover:text-red-400 transition-colors">Services</a></li>
                            <li><a href="{{ route('contact.form') }}" class="hover:text-red-400 transition-colors">Contact Us</a></li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-3">Resources</h5>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('blog.index') }}" class="hover:text-red-400 transition-colors">Blog</a></li>
                            <li><a href="#" class="hover:text-red-400 transition-colors">FAQs</a></li>
                            <li><a href="#" class="hover:text-red-400 transition-colors">Support</a></li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-3">Connect With Us</h5>
                        {{-- Social media icons placeholder --}}
                        <div class="flex space-x-4">
                            @if(App\Models\Setting::getValue('social_facebook_url'))
                            <a href="{{ App\Models\Setting::getValue('social_facebook_url') }}" target="_blank" class="hover:text-red-400 transition-colors">FB</a>
                            @endif
                            @if(App\Models\Setting::getValue('social_twitter_url'))
                            <a href="{{ App\Models\Setting::getValue('social_twitter_url') }}" target="_blank" class="hover:text-red-400 transition-colors">TW</a>
                            @endif
                            @if(App\Models\Setting::getValue('social_linkedin_url'))
                            <a href="{{ App\Models\Setting::getValue('social_linkedin_url') }}" target="_blank" class="hover:text-red-400 transition-colors">LI</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 pt-8 text-center text-sm">
                    <p>&copy; {{ date('Y') }} {{ App\Models\Setting::getValue('website_name', 'Einspot Solutions') }}. All rights reserved.</p>
                    <p><a href="#" class="hover:text-red-400">Privacy Policy</a> | <a href="#" class="hover:text-red-400">Terms of Service</a></p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts') {{-- For page-specific scripts --}}

    {{-- General WhatsApp CTA Button --}}
    @php
        $generalWhatsappMessage = "Hello EINSPOT, I'd like to inquire about your services.";
        $whatsappNumber = App\Models\Setting::getValue('whatsapp_number', '2348123647982');
        $generalWhatsappLink = "https://wa.me/" . $whatsappNumber . "?text=" . rawurlencode($generalWhatsappMessage);
    @endphp
    <a href="{{ $generalWhatsappLink }}" target="_blank" rel="noopener noreferrer"
       class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg z-40 transition-transform hover:scale-110"
       aria-label="Chat on WhatsApp">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"></path></svg>
    </a>
</body>
</html>
