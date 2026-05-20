@php
use App\Models\Setting;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Setting::get('store_name', 'Bintang Agung Tani') }} - @yield('title', 'Solusi Pertanian')</title>

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

    <style>
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.2s ease-out; }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50">
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    <div class="min-h-screen flex flex-col">
        @include('components.navbar')

        <main id="main-content" class="flex-1 w-full" role="main">
            <div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
        
        @include('components.footer')
        @include('components.toast')
        @include('components.cart-drawer')
    </div>
</body>
</html>
