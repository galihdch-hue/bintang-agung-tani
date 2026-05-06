@php
$currentCategory = request('kategori');
$currentSort = request('sort', 'terbaru');
$totalProducts = $products->total() ?? 0;
@endphp

@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="space-y-6" x-data="{ filterOpen: false }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('user.dashboard') }}" class="hover:text-primary-600 transition-colors flex items-center gap-1">
                            <i class="ph ph-house text-xs"></i>
                            Dashboard
                        </a>
                    </li>
                    <li><i class="ph ph-caret-right text-gray-300 text-xs"></i></li>
                    <li class="text-gray-900 font-semibold">Produk</li>
                </ol>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Katalog Produk</h1>
            <p class="text-gray-500 mt-2">Temukan produk pertanian berkualitas untuk kebutuhan Anda</p>
        </div>
        
        @if($currentCategory)
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Filter aktif:</span>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 text-primary-700 text-sm font-semibold rounded-lg border border-primary-200">
                    <i class="ph ph-funnel"></i>
                    {{ $categories->firstWhere('slug', $currentCategory)->name ?? 'Kategori' }}
                    <a href="{{ route('user.produk.index') }}" class="ml-1 hover:text-primary-800">
                        <i class="ph ph-x-circle"></i>
                    </a>
                </span>
            </div>
        @endif
    </div>
    
    {{-- Search & Filter Bar --}}
    <div class="card-featured p-4 flex items-center gap-3" x-data="{ loading: false }">
        <div class="relative flex-1">
            <i x-show="!loading" x-cloak class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
            <div x-show="loading" x-cloak class="absolute left-4 top-1/2 -translate-y-1/2">
                <x-loading-spinner size="sm" class="text-gray-400" />
            </div>
            <input type="text" 
                   name="search"
                   placeholder="Cari produk pertanian, pupuk, pestisida..."
                   class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all"
                   value="{{ request('search') }}"
                   x-on:keyup.enter="loading = true; window.location.href = '{{ route('user.produk.index') }}?search=' + $event.target.value">
        </div>
        <button @click="filterOpen = true"
                class="lg:hidden icon-button bg-primary-50 border border-primary-200 rounded-xl text-primary-600 hover:bg-primary-100 transition-all duration-200 flex items-center gap-2 min-w-[auto] px-4">
            <i class="ph ph-faders text-lg"></i>
            <span class="text-sm font-semibold">Filter</span>
        </button>
    </div>
    
    {{-- Main Content --}}
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Sidebar Filter - Sticky --}}
        <aside class="hidden lg:block w-72 shrink-0">
            <div class="sticky top-24 space-y-4">
                
                {{-- Categories Filter --}}
                <div class="card-featured overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="ph ph-squares-four text-white text-lg"></i>
                        </div>
                        <h3 class="font-bold text-white text-sm uppercase tracking-wider">Kategori</h3>
                    </div>
                    <div class="p-3 space-y-1">
                        <a href="{{ route('user.produk.index') }}" 
                           class="flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-200 {{ !$currentCategory ? 'bg-primary-50 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 {{ !$currentCategory ? 'bg-primary-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                    <i class="ph ph-apps {{ !$currentCategory ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                </div>
                                <span>Semua Kategori</span>
                            </div>
                            <span class="text-xs {{ !$currentCategory ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }} px-2.5 py-1 rounded-full font-semibold">{{ $totalProducts }}</span>
                        </a>
                        @foreach($categories as $category)
                        <a href="{{ route('user.produk.index', ['kategori' => $category->slug]) }}" 
                           class="flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-200 {{ $currentCategory == $category->slug ? 'bg-primary-50 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 {{ $currentCategory == $category->slug ? 'bg-primary-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                    <i class="ph {{ $category->icon ?? 'ph-package' }} {{ $currentCategory == $category->slug ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                </div>
                                <span>{{ $category->name }}</span>
                            </div>
                            <span class="text-xs {{ $currentCategory == $category->slug ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }} px-2.5 py-1 rounded-full font-semibold">{{ $categoryCounts[$category->slug] ?? 0 }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                
                {{-- Price Range Filter --}}
                <div class="card-featured p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                            <i class="ph ph-currency-dollar text-amber-600"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm">Rentang Harga</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="relative">
                            <input type="range" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary-600" min="0" max="1000000" step="10000" value="500000">
                            <div class="flex justify-between text-xs text-gray-400 mt-2">
                                <span>Rp 0</span>
                                <span>Rp 1jt</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                <input type="number" placeholder="Min" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all">
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                <input type="number" placeholder="Max" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all">
                            </div>
                        </div>
                        <button class="w-full py-2.5 bg-primary-50 text-primary-700 font-semibold text-sm rounded-lg hover:bg-primary-100 transition-colors flex items-center justify-center gap-2">
                            <i class="ph ph-check-circle"></i>
                            Terapkan Harga
                        </button>
                    </div>
                </div>
                
                {{-- Reset Filter --}}
                <a href="{{ route('user.produk.index') }}" class="flex items-center justify-center gap-2 w-full py-3 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                    Reset Semua Filter
                </a>
                
                {{-- Help Card --}}
                <div class="card-featured p-5 bg-gradient-to-br from-primary-50 to-primary-100/50 border-primary-200">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <i class="ph ph-question text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm mb-1">Butuh Bantuan?</h4>
                            <p class="text-gray-600 text-xs mb-3">Tim kami siap membantu mencari produk yang Anda butuhkan.</p>
                            <a href="{{ route('user.bantuan') }}" class="text-primary-600 text-xs font-semibold hover:text-primary-700 flex items-center gap-1">
                                Hubungi Kami
                                <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        {{-- Product Grid Area --}}
        <div class="flex-1">
            <div class="card-featured p-5 md:p-6">
                {{-- Grid Header --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                            <i class="ph ph-package text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Menampilkan</p>
                            <p class="text-lg font-bold text-gray-900">
                                <span class="text-primary-600">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> 
                                <span class="text-gray-400">dari</span> 
                                <span>{{ $totalProducts }}</span> produk
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500 hidden sm:inline">Urutkan:</span>
                        <div class="relative">
                            <select name="sort" 
                                    onchange="window.location.href = this.value"
                                    class="appearance-none bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl pl-4 pr-10 py-3 focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20 cursor-pointer font-medium min-w-[160px]">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}" {{ $currentSort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'terlaris']) }}" {{ $currentSort == 'terlaris' ? 'selected' : '' }}>Terlaris</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'harga-tertinggi']) }}" {{ $currentSort == 'harga-tertinggi' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'harga-terendah']) }}" {{ $currentSort == 'harga-terendah' ? 'selected' : '' }}>Harga Terendah</option>
                            </select>
                            <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                </div>
                
                {{-- Product Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
                    @forelse($products as $product)
                    <x-product-card
                        :image="$product->getFirstImage()"
                        :title="$product->name"
                        :price="$product->getFormattedPrice()"
                        :originalPrice="$product->hasDiscount() ? $product->getFormattedOriginalPrice() : null"
                        :discount="$product->hasDiscount() ? $product->getDiscountPercentage() : null"
                        :rating="$product->rating ?? null"
                        :soldCount="$product->sold_count ?? null"
                        :stock="$product->stock"
                        :href="route('user.produk.show', $product->slug)"
                        :productId="$product->id" />
                    @empty
                    <x-empty-state 
                        icon="magnifying-glass"
                        title="Produk Tidak Ditemukan"
                        description="Maaf, kami tidak menemukan produk yang sesuai dengan pencarian atau filter Anda."
                        actionText="Reset Filter"
                        actionHref="{{ route('user.produk.index') }}"
                        actionIcon="arrow-counter-clockwise" />
                    @endforelse
                </div>
                
                {{-- Pagination --}}
                @if($products->hasPages())
                <div class="mt-8 pt-6 border-t border-gray-100 flex w-full">
                    {{ $products->links('vendor.pagination.custom') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Mobile Filter Drawer --}}
    <div x-show="filterOpen" x-cloak
         class="fixed inset-0 bg-gray-900/60 z-50 lg:hidden"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         @keydown.escape.window="filterOpen = false">
        <div @click.stop 
             class="absolute inset-y-0 left-0 w-80 bg-white shadow-2xl overflow-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-primary-600 to-primary-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="ph ph-faders text-white"></i>
                    </div>
                    <h2 class="text-lg font-bold text-white">Filter Produk</h2>
                </div>
                <button @click="filterOpen = false" 
                        class="icon-button bg-white/20 rounded-lg text-white hover:bg-white/30 transition-colors"
                        aria-label="Tutup filter">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>
            
            <div class="p-5 space-y-6">
                {{-- Mobile Categories --}}
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                        <i class="ph ph-squares-four text-primary-600"></i>
                        Kategori
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('user.produk.index') }}" 
                           class="flex items-center justify-between px-4 py-3 rounded-xl text-sm {{ !$currentCategory ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 {{ !$currentCategory ? 'bg-primary-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                    <i class="ph ph-apps {{ !$currentCategory ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                </div>
                                <span>Semua Kategori</span>
                            </div>
                        </a>
                        @foreach($categories as $category)
                        <a href="{{ route('user.produk.index', ['kategori' => $category->slug]) }}" 
                           class="flex items-center justify-between px-4 py-3 rounded-xl text-sm {{ $currentCategory == $category->slug ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 {{ $currentCategory == $category->slug ? 'bg-primary-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                    <i class="ph {{ $category->icon ?? 'ph-package' }} {{ $currentCategory == $category->slug ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                </div>
                                <span>{{ $category->name }}</span>
                            </div>
                            <span class="text-xs {{ $currentCategory == $category->slug ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }} px-2 py-1 rounded-full font-semibold">{{ $categoryCounts[$category->slug] ?? 0 }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <hr class="border-gray-100">
                
                {{-- Mobile Price Range --}}
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                        <i class="ph ph-currency-dollar text-amber-600"></i>
                        Rentang Harga
                    </h3>
                    <div class="space-y-3">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" placeholder="Harga Minimum" class="w-full pl-10 pr-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all">
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" placeholder="Harga Maksimum" class="w-full pl-10 pr-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all">
                        </div>
                    </div>
                </div>
                
                <hr class="border-gray-100">
                
                {{-- Mobile Sort --}}
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                        <i class="ph ph-sort-ascending text-blue-600"></i>
                        Urutkan
                    </h3>
                    <div class="space-y-2">
                        @php
                        $sortOptions = [
                            'terbaru' => ['icon' => 'ph-calendar', 'label' => 'Terbaru'],
                            'terlaris' => ['icon' => 'ph-fire', 'label' => 'Terlaris'],
                            'harga-tertinggi' => ['icon' => 'ph-arrow-up', 'label' => 'Harga Tertinggi'],
                            'harga-terendah' => ['icon' => 'ph-arrow-down', 'label' => 'Harga Terendah'],
                        ];
                        @endphp
                        @foreach($sortOptions as $value => $option)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $value]) }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ $currentSort == $value ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                            <div class="w-8 h-8 {{ $currentSort == $value ? 'bg-primary-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                <i class="ph {{ $option['icon'] }} {{ $currentSort == $value ? 'text-primary-600' : 'text-gray-400' }}"></i>
                            </div>
                            <span>{{ $option['label'] }}</span>
                            @if($currentSort == $value)
                                <i class="ph ph-check-circle text-primary-600 ml-auto"></i>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                
                {{-- Apply Button --}}
                <div class="pt-4 border-t border-gray-100">
                    <button @click="filterOpen = false" 
                            class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="ph ph-check-circle text-lg"></i>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('user.produk.index') }}" class="mt-3 w-full py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                        <i class="ph ph-arrow-counter-clockwise"></i>
                        Reset Filter
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Animations */
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 3s linear infinite;
}

/* Range Slider Styling */
input[type="range"]::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    background: linear-gradient(to right, #16a34a, #15803d);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
}

input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: linear-gradient(to right, #16a34a, #15803d);
    border-radius: 50%;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .animate-spin-slow {
        animation: none;
    }
    
    .card-featured-hover:hover {
        transform: none;
    }
}
</style>
@endsection
