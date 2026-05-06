@php
$authName = auth()->user()->name ?? 'User';
$cartCount = 0;
if(auth()->check()) {
    $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
    if($cart) {
        $cartCount = $cart->getTotalItems();
    }
}

// Get categories for sidebar (only those with active products)
$sidebarCategories = \App\Models\Category::where('is_active', true)
    ->whereHas('activeProducts')
    ->withCount('activeProducts')
    ->orderBy('sort_order')
    ->take(8)
    ->get();
@endphp

{{-- Mobile Sidebar Overlay --}}
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 md:hidden transition-opacity"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"></div>

{{-- Sidebar --}}
<aside id="sidebar"
    class="fixed md:sticky md:top-16 inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-50 transform transition-transform duration-300 ease-out md:translate-x-0 flex flex-col h-screen md:h-[calc(100vh-4rem)]"
    :class="{ '-translate-x-full': !sidebarOpen }"
    role="navigation"
    aria-label="Sidebar navigation">
    
    {{-- Mobile Close Button --}}
    <button @click="sidebarOpen = false" 
            type="button"
            class="md:hidden absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            aria-label="Close sidebar">
        <i class="ph ph-x text-xl"></i>
    </button>
    
    {{-- User Mini Profile --}}
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($authName) }}&background=ecfdf5&color=059669&size=40" 
                 alt="{{ $authName }}" 
                 class="w-10 h-10 rounded-full object-cover">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $authName }}</p>
                <p class="text-xs text-gray-500">Petani</p>
            </div>
        </div>
    </div>
    
    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto p-3 space-y-1">
        <a href="/user/dashboard" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->is('user/dashboard*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="ph ph-squares-four text-lg {{ request()->is('user/dashboard*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
            Dashboard
        </a>
        
        <a href="/user/produk" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->is('user/produk') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="ph ph-storefront text-lg {{ request()->is('user/produk') ? 'text-primary-600' : 'text-gray-400' }}"></i>
            Semua Produk
        </a>
        
        {{-- Categories Section --}}
        @if($sidebarCategories->count() > 0)
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori</p>
        </div>
        
        @foreach($sidebarCategories as $category)
        <a href="/user/produk?kategori={{ $category->slug }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors {{ request('kategori') == $category->slug ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="ph {{ $category->icon ?? 'ph-package' }} text-lg {{ request('kategori') == $category->slug ? 'text-primary-600' : 'text-gray-400' }}"></i>
            <span class="truncate">{{ $category->name }}</span>
        </a>
        @endforeach
        @endif
        
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Transaksi</p>
        </div>
        
        <a href="/user/keranjang" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->is('user/keranjang*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="ph ph-shopping-cart text-lg {{ request()->is('user/keranjang*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
            Keranjang
            @if($cartCount > 0)
                <span class="ml-auto bg-primary-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
            @endif
        </a>
        
        <a href="/user/riwayat" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->is('user/riwayat*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="ph ph-receipt text-lg {{ request()->is('user/riwayat*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
            Riwayat Pesanan
        </a>
    </nav>
    
    {{-- Bottom Actions --}}
    <div class="p-3 border-t border-gray-100">
        <a href="/user/bantuan" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors {{ request()->is('user/bantuan*') ? 'bg-primary-50 text-primary-700' : '' }}">
            <i class="ph ph-question text-lg {{ request()->is('user/bantuan*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
            Bantuan
        </a>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors mt-1">
                <i class="ph ph-sign-out text-lg"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>
