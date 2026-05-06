@props(['type' => 'user'])

@php
$navItems = $type === 'admin' ? [
    ['label' => 'Dashboard', 'icon' => 'ph-chart-pie-slice', 'href' => '/admin/dashboard'],
    ['label' => 'Produk', 'icon' => 'ph-package', 'href' => '/admin/produk'],
    ['label' => 'Pesanan', 'icon' => 'ph-clipboard-text', 'href' => '/admin/pesanan'],
    ['label' => 'Akun', 'icon' => 'ph-user', 'href' => '/admin/profil'],
] : [
    ['label' => 'Dashboard', 'icon' => 'ph-squares-four', 'href' => '/user/dashboard'],
    ['label' => 'Produk', 'icon' => 'ph-storefront', 'href' => '/user/produk'],
    ['label' => 'Keranjang', 'icon' => 'ph-shopping-cart', 'href' => '/user/keranjang'],
    ['label' => 'Akun', 'icon' => 'ph-user', 'href' => '/user/profil'],
];
@endphp

<!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden safe-area-inset-bottom">
    <div class="flex items-center justify-around h-16">
        @foreach($navItems as $item)
            @php
                $isActive = request()->is($item['href'] . '*') || request()->is($item['href']);
            @endphp
            <a href="{{ $item['href'] }}"
               class="flex flex-col items-center justify-center flex-1 h-full relative transition-colors duration-200 {{ $isActive ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600' }}">

                <div class="relative">
                    <i class="ph {{ $item['icon'] }} text-2xl {{ $isActive ? 'ph-fill' : 'ph' }}"></i>

                    @if($item['label'] === 'Keranjang' && isset($cartCount) && $cartCount > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                    @endif
                </div>

                <span class="text-[10px] font-medium mt-0.5 {{ $isActive ? 'text-primary-600' : '' }}">{{ $item['label'] }}</span>

                @if($isActive)
                    <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 bg-primary-500 rounded-full"></span>
                @endif
            </a>
        @endforeach
    </div>
</nav>

<!-- Spacer for mobile bottom nav -->
<div class="h-16 md:hidden"></div>

<style>
    /* Safe area support for modern mobile devices */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .safe-area-inset-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
    }
</style>
