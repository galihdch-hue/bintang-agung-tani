@php
    $cartCount = 0;
    if (auth()->check()) {
        $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
        if ($cart) {
            $cartCount = $cart->getTotalItems();
        }
    }

    $authUser = auth()->user();
    $authName = $authUser->name ?? 'User';
    $authEmail = $authUser->email ?? '-';
    $authPhotoUrl =
        $authUser?->profile_photo_url ??
        'https://ui-avatars.com/api/?name=' . urlencode($authName) . '&background=ecfdf5&color=059669&size=32';
@endphp

<nav class="sticky top-0 z-40 bg-gradient-to-r from-primary-600 to-primary-700 backdrop-blur-sm shadow-sm border-b border-primary-800"
    x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <div class="flex items-center gap-8">
                <a href="/user/dashboard" class="flex items-center gap-2 group">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.store_name', 'Bintang Agung Tani') }}"
                        class="h-10 w-auto object-contain group-hover:scale-105 transition-transform">
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="/user/produk"
                        class="px-3 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/10 transition-all duration-200 text-sm font-medium {{ request()->is('user/produk') ? 'bg-white/15 text-white' : '' }}">Produk</a>
                    <a href="/user/keranjang"
                        class="px-3 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/10 transition-all duration-200 text-sm font-medium {{ request()->is('user/keranjang') ? 'bg-white/15 text-white' : '' }}">Keranjang</a>
                    <a href="/user/riwayat"
                        class="px-3 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/10 transition-all duration-200 text-sm font-medium {{ request()->is('user/riwayat') ? 'bg-white/15 text-white' : '' }}">Pesanan</a>
                </div>
            </div>

            {{-- Right Side Actions --}}
            <div class="flex items-center gap-4">
                {{-- Search Bar (Desktop) --}}
                <div class="hidden md:block relative group">
                    <input type="text" placeholder="Cari produk..."
                        class="w-64 bg-white/10 border border-white/20 text-white placeholder-white/60 text-sm rounded-xl pl-10 pr-4 py-2 focus:outline-none focus:bg-white/20 focus:border-white/30 focus:ring-4 focus:ring-white/10 transition-all duration-200"
                        aria-label="Cari produk"
                        onkeyup="if(event.key==='Enter') window.location.href='/user/produk?search='+this.value">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass w-4 h-4 text-white/60 group-focus-within:text-white transition-colors"></i>
                    </div>
                </div>

                {{-- Cart --}}
                <a href="/user/keranjang"
                    class="relative p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all group"
                    aria-label="Keranjang belanja">
                    <i class="ph ph-shopping-cart w-6 h-6 group-hover:scale-110 transition-transform"></i>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-amber-400 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border-2 border-primary-600">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                    @endif
                </a>

                {{-- User Menu --}}
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-2.5 bg-white/10 hover:bg-white/20 p-1.5 rounded-xl border border-white/20 transition-all focus:outline-none text-white group">
                        <img src="{{ $authPhotoUrl }}" alt="{{ $authName }}"
                            class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-300 shadow-sm">
                        <span class="hidden md:inline text-sm font-medium">{{ $authName }}</span>
                        <i class="ph ph-caret-down w-3.5 h-3.5 text-white/60 transition-transform group-hover:text-white" :class="{ 'rotate-180': open }"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50"
                        style="display: none;" role="menu">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ $authName }}</p>
                            <p class="text-xs text-gray-500">{{ $authEmail }}</p>
                        </div>
                        <a href="/user/profil"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            role="menuitem">
                            <i class="ph ph-user w-4 h-4 text-gray-400"></i> Profil
                        </a>
                        <a href="/user/riwayat"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            role="menuitem">
                            <i class="ph ph-receipt w-4 h-4 text-gray-400"></i> Pesanan
                        </a>
                        <a href="/user/bantuan"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            role="menuitem">
                            <i class="ph ph-info w-4 h-4 text-gray-400"></i> Bantuan
                        </a>
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form action="/logout" method="POST" role="menuitem">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 focus-visible:ring-inset rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Mobile Menu Button --}}
                <div class="md:hidden">
                    <button @click="mobileMenu = !mobileMenu"
                        class="icon-button text-white/85 hover:text-white hover:bg-white/10 rounded-lg transition-colors focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-700"
                        aria-label="Toggle menu">
                        <svg class="w-6 h-6" :class="{ 'hidden': mobileMenu }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="w-6 h-6" :class="{ 'hidden': !mobileMenu }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden py-3 border-t border-white/20"
            style="display: none;" role="navigation" aria-label="Mobile menu">
            
            {{-- Mobile Search --}}
            <div class="px-3 mb-4">
                <div class="relative group">
                    <input type="text" placeholder="Cari produk..."
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/60 text-sm rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:bg-white/20 focus:border-white/30 transition-all duration-200"
                        onkeyup="if(event.key==='Enter') window.location.href='/user/produk?search='+this.value">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass w-4 h-4 text-white/60"></i>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <a href="/user/dashboard"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/90 hover:bg-white/10 transition-colors {{ request()->is('user/dashboard') ? 'bg-white/20 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5 {{ request()->is('user/dashboard') ? 'text-white' : 'text-white/70' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="/user/produk"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/90 hover:bg-white/10 transition-colors {{ request()->is('user/produk') ? 'bg-white/20 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5 {{ request()->is('user/produk') ? 'text-white' : 'text-white/70' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span>Produk</span>
                </a>
                <a href="/user/keranjang"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/90 hover:bg-white/10 transition-colors {{ request()->is('user/keranjang') ? 'bg-white/20 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5 {{ request()->is('user/keranjang') ? 'text-white' : 'text-white/70' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span>Keranjang</span>
                    @if ($cartCount > 0)
                        <span
                            class="ml-auto bg-primary-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="/user/riwayat"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/90 hover:bg-white/10 transition-colors {{ request()->is('user/riwayat') ? 'bg-white/20 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5 {{ request()->is('user/riwayat') ? 'text-white' : 'text-white/70' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                    <span>Pesanan</span>
                </a>
                <hr class="my-2 border-white/20">
                <a href="/user/profil"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/90 hover:bg-white/10 transition-colors {{ request()->is('user/profil') ? 'bg-white/20 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5 {{ request()->is('user/profil') ? 'text-white' : 'text-white/70' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil</span>
                </a>
            </div>
        </div>
    </div>
</nav>
