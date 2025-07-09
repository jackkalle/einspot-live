<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Einspot Solutions') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', 'Inter', sans-serif; }
        /* Basic alert styling for admin panel */
        .admin-alert { padding: 1rem; margin-bottom: 1rem; border-radius: 0.375rem; }
        .admin-alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc;}
        .admin-alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7;}
        .admin-alert-info { color: #055160; background-color: #cff4fc; border-color: #b6effb;}
        .admin-nav-link { @apply block px-4 py-2.5 text-sm text-gray-700 hover:bg-einspot-red-100 hover:text-einspot-red-700 rounded-md transition-colors duration-150; }
        .admin-nav-link.active { @apply bg-einspot-red-600 text-white font-semibold; }
    </style>

    @stack('styles')
</head>
<body class="font-poppins antialiased bg-gray-100 text-gray-800">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <div class="flex items-center justify-center h-20 border-b">
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-gray-800">
                    EINSPOT <span class="text-einspot-red-600">ADMIN</span>
                </a>
            </div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="mr-2">📊</span> Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="mr-2">📦</span> Products
                </a>
                <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="mr-2">🏷️</span> Categories
                </a>
                <a href="{{ route('admin.tags.index') }}" class="admin-nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                    <span class="mr-2">#️⃣</span> Tags
                </a>
                 <a href="{{-- route('admin.orders.index') --}}" class="admin-nav-link {{-- request()->routeIs('admin.orders.*') ? 'active' : '' --}}">
                    <span class="mr-2">🛒</span> Orders
                </a>
                <a href="{{ route('admin.services.index') }}" class="admin-nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <span class="mr-2">🛠️</span> Services
                </a>
                <a href="{{ route('admin.projects.index') }}" class="admin-nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                    <span class="mr-2">🏗️</span> Projects
                </a>
                <a href="{{ route('admin.blogs.index') }}" class="admin-nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                    <span class="mr-2">📝</span> Blog Posts
                </a>
                <a href="{{ route('admin.quote-requests.index') }}" class="admin-nav-link {{ request()->routeIs('admin.quote-requests.*') ? 'active' : '' }}">
                    <span class="mr-2">💬</span> Quote Requests
                </a>
                <a href="{{ route('admin.contact-submissions.index') }}" class="admin-nav-link {{ request()->routeIs('admin.contact-submissions.*') ? 'active' : '' }}">
                    <span class="mr-2">📧</span> Contact Submissions
                </a>
                <a href="{{ route('admin.newsletter-subscriptions.index') }}" class="admin-nav-link {{ request()->routeIs('admin.newsletter-subscriptions.*') ? 'active' : '' }}">
                    <span class="mr-2">📰</span> Newsletter Subs
                </a>
                {{-- <a href="{{-- route('admin.users.index') --}}" class="admin-nav-link {{-- request()->routeIs('admin.users.*') ? 'active' : '' --}}">
                    <span class="mr-2">👥</span> Users
                </a> --}}
                <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                    <span class="mr-2">⚙️</span> Site Settings
                </a>
                {{-- <a href="{{-- route('admin.activity-logs.index') --}}" class="admin-nav-link {{-- request()->routeIs('admin.activity-logs.*') ? 'active' : '' --}}">
                    <span class="mr-2">📜</span> Activity Logs
                </a> --}}

                <div class="pt-4 mt-4 border-t border-gray-200">
                    <a href="{{ route('home') }}" target="_blank" class="admin-nav-link">
                       <span class="mr-2">🌐</span> View Public Site
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="admin-nav-link w-full text-left">
                            <span class="mr-2">🚪</span> Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="flex items-center justify-between h-16 px-6 bg-white border-b lg:justify-end">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 focus:outline-none hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>

                <div class="flex items-center">
                    @auth
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <span class="text-sm font-medium">{{ Auth::user()->firstName ?? Auth::user()->name }}</span>
                            {{-- Placeholder for profile icon/avatar --}}
                            <div class="w-8 h-8 bg-einspot-red-500 text-white rounded-full flex items-center justify-center text-sm">
                                {{ strtoupper(substr(Auth::user()->firstName ?? Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8">
                <div class="container mx-auto">
                    @if(session('success'))
                        <div class="admin-alert admin-alert-success mb-6">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="admin-alert admin-alert-danger mb-6">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="admin-alert admin-alert-danger mb-6">
                            <p class="font-bold mb-1">Please correct the following errors:</p>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    {{-- Basic Alpine.js for sidebar toggle, if not included in app.js --}}
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
</body>
</html>
