@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row bg-white">
    
    <!-- Left Side: Brand Showcase with Logo -->
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/3 relative overflow-hidden">
        <!-- Background: Rice Field Image -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=800&q=80" 
                 alt="Sawah" 
                 class="w-full h-full object-cover">
        </div>
        
        <!-- Dark Overlay for better text contrast - lighter so image shows -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/50 via-primary-800/35 to-primary-900/50"></div>
        
        <!-- Subtle Pattern Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.3) 1px, transparent 0); background-size: 32px 32px;"></div>
        
        <!-- Content Container -->
        <div class="relative z-10 flex flex-col justify-between h-full p-8 xl:p-12">
            
            <!-- Top: Small Brand -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <span class="text-white font-medium text-sm tracking-wide">{{ strtoupper(config('app.store_name', 'Bintang Agung Tani')) }}</span>
            </div>
            
            <!-- Center: Logo Display -->
            <div class="flex-1 flex flex-col justify-center items-center py-8">
                <div class="w-full max-w-[260px] xl:max-w-[300px] mb-6">
                    <img src="/images/logo.png" 
                         alt="{{ config('app.store_name', 'Bintang Agung Tani') }}" 
                         class="w-full h-auto"
                         style="filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                </div>
                <h2 class="text-2xl xl:text-3xl font-bold text-white text-center leading-tight">
                    Solusi Pertanian<br>
                    <span class="text-primary-200">Terlengkap</span>
                </h2>
                <p class="text-primary-100 text-center mt-4 text-sm xl:text-base max-w-xs">
                    Platform terpercaya untuk kebutuhan pertanian Anda
                </p>
            </div>
            
            <!-- Bottom: Stats -->
            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/10">
                <div class="text-center">
                    <div class="text-2xl xl:text-3xl font-bold text-white">{{ config('app.total_products', '200+') }}</div>
                    <div class="text-primary-200 text-xs xl:text-sm">Produk</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl xl:text-3xl font-bold text-white">{{ config('app.total_farmers', '10K+') }}</div>
                    <div class="text-primary-200 text-xs xl:text-sm">Petani</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side: Login Form -->
    <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-12 lg:py-0 relative overflow-hidden">
        <!-- Subtle Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-primary-50/20"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary-100/30 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-primary-50/40 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
        
        <!-- Content (above background) -->
        <div class="relative z-10 w-full max-w-md">
        
        <!-- Mobile Logo -->
        <div class="lg:hidden mb-8 text-center">
            <div class="w-24 h-24 mx-auto mb-4">
                <img src="/images/logo.png" 
                     alt="{{ config('app.store_name', 'Bintang Agung Tani') }}" 
                     class="w-full h-auto">
            </div>
            <h1 class="text-lg font-bold text-gray-900">{{ config('app.store_name', 'Bintang Agung Tani') }}</h1>
            <p class="text-primary-600 text-sm">Solusi Pertanian Terlengkap</p>
        </div>
        
        <!-- Form Container -->
        <div class="w-full max-w-md space-y-8">
            
            <!-- Header -->
            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                <p class="text-gray-500">Silakan masuk untuk melanjutkan</p>
            </div>
            
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-red-700">{{ $errors->first() }}</div>
                </div>
            @endif
            
            <!-- Login Form -->
            <form action="/login" method="POST" class="mt-8 space-y-5">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               placeholder="nama@email.com" required
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                    </div>
                </div>
                
                <!-- Password Field -->
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-700">Password</label>
                        <a href="/forgot-password" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center min-h-[44px] px-2 -mr-2">
                            Lupa?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" 
                               placeholder="Minimal 8 karakter" required minlength="8"
                               class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Remember Me -->
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="remember" class="peer sr-only">
                    <div class="w-5 h-5 bg-white border-2 border-gray-300 rounded peer-checked:bg-primary-600 peer-checked:border-primary-600 flex items-center justify-center transition-all shadow-sm">
                        <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>
                
                <!-- Submit Button: Same green as app -->
                <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                    <span>Masuk</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
            
            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-4 text-sm text-gray-400">atau</span>
                </div>
            </div>
            
            <!-- Register Link -->
            <div class="text-center">
                <p class="text-gray-500 text-sm">
                    Belum punya akun? 
                    <a href="/register" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center min-h-[44px] px-2 -mx-2">
                        Daftar sekarang
                    </a>
                </p>
            </div>
            
            <!-- Footer -->
            <p class="text-center text-xs text-gray-400 mt-8">
                © {{ date('Y') }} {{ config('app.store_name', 'Bintang Agung Tani') }}
            </p>
        </div>
        </div>
    </div>
</div>
@endsection
