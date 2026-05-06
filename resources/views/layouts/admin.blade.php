@php
use App\Models\Setting;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Admin {{ Setting::get('store_name', 'Bintang Agung Tani') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons (local to avoid CORB issues) -->
    <link rel="stylesheet" href="{{ asset('fonts/phosphor/style.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/phosphor/bold/style.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/phosphor/fill/style.css') }}">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="font-sans antialiased text-gray-700 bg-gray-50 text-render-optimized">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-primary text-white px-4 py-2 rounded-lg font-medium">
        Skip to main content
    </a>
    <div class="min-h-screen flex flex-col bg-white" x-data="{ sidebarOpen: false }">

        <!-- Admin Navbar -->
        @if(!View::hasSection('no_navbar'))
            @include('components.admin.navbar')
        @endif

        <div class="flex flex-1 w-full relative overflow-hidden">
            <!-- Admin Sidebar -->
            @include('components.admin.sidebar')

            <!-- Main Content -->
            <main id="main-content" class="flex-1 min-w-0 flex flex-col relative overflow-y-auto @if(!View::hasSection('full_screen')) md:ml-72 @endif" role="main" tabindex="-1">
                @if(View::hasSection('full_screen'))
                    @yield('content')
                @else
                    <div class="container-main py-4 md:py-5 min-h-full">
                        @yield('content')
                    </div>
                @endif
            </main>
        </div>
    </div>
    
    @include('components.toast')
    @include('components.confirm-modal')
    @yield('scripts')
</body>
</html>

