@php
    $authName = auth()->user()->name ?? 'Admin';
    $avatarName = urlencode($authName);
@endphp

<!-- Mobile Overlay -->
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-gray-900/60 z-40 md:hidden transition-opacity duration-300"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
></div>

<!-- Sidebar -->
<aside id="sidebar"
    class="w-72 bg-white border-r border-gray-200 flex-shrink-0 fixed inset-y-0 left-0 z-40 md:z-30 transform transition-transform duration-300 ease-natural flex flex-col -translate-x-full md:translate-x-0 h-screen shadow-subtle"
    :class="{ 'translate-x-0': sidebarOpen }"
    role="navigation">

    <!-- Profile Section -->
    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50/50">
        <div class="flex items-center gap-3 w-full">
            <img loading="lazy" src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=ecfdf5&color=059669" alt="{{ $authName }}" class="w-10 h-10 rounded-xl object-cover shrink-0 ring-2 ring-white shadow-soft">
            <div class="min-w-0 flex-1">
                <h3 class="font-bold text-gray-900 text-sm leading-tight truncate">{{ $authName }}</h3>
                <span class="text-[10px] text-gray-500 font-medium">Logged in as Admin</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="px-2 py-3 flex-1 overflow-y-auto w-full" aria-label="Admin menu">
        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-1 px-3">Menu Utama</div>
        <x-admin.sidebar-link href="/admin/dashboard" icon="ph-chart-pie-slice" :active="request()->is('admin/dashboard')">Dashboard</x-admin.sidebar-link>
        <x-admin.sidebar-link href="/admin/produk" icon="ph-package" :active="request()->is('admin/produk*')">Kelola Produk</x-admin.sidebar-link>
        <x-admin.sidebar-link href="/admin/kategori" icon="ph-tag" :active="request()->is('admin/kategori*')">Kelola Kategori</x-admin.sidebar-link>
        <x-admin.sidebar-link href="/admin/stok" icon="ph-warehouse" :active="request()->is('admin/stok*')">Kelola Stok</x-admin.sidebar-link>

        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 px-3">Pengguna</div>
        <x-admin.sidebar-link href="/admin/users" icon="ph-users" :active="request()->is('admin/users*')">Kelola User</x-admin.sidebar-link>

        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 px-3">Transaksi</div>
        <x-admin.sidebar-link href="/admin/pesanan" icon="ph-clipboard-text" :active="request()->is('admin/pesanan*')">Pesanan Masuk</x-admin.sidebar-link>
        <x-admin.sidebar-link href="/admin/verifikasi" icon="ph-seal-check" :active="request()->is('admin/verifikasi*')">Verifikasi Pembayaran</x-admin.sidebar-link>
        <x-admin.sidebar-link href="/admin/payment-methods" icon="ph-wallet" :active="request()->is('admin/payment-methods*')">Metode Pembayaran</x-admin.sidebar-link>
        <x-admin.sidebar-link href="/admin/scan" icon="ph-scan" :active="request()->is('admin/scan*')">Scan QR Pengambilan</x-admin.sidebar-link>

        <!-- Komunikasi & Layanan -->
        <div class="px-4 pt-6 pb-2">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-4">Komunikasi & Layanan</span>
        </div>
        <div class="space-y-1 px-3">
            <x-admin.sidebar-link :href="route('admin.notifications.index')" :active="request()->routeIs('admin.notifications.*')" icon="ph-bell" :badge="auth()->user()->unreadNotifications->count() ?: null">
                Notifikasi
            </x-admin.sidebar-link>

        </div>

        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 px-3">Sistem</div>
        <x-admin.sidebar-link href="/admin/settings" icon="ph-gear" :active="request()->is('admin/settings*')">Pengaturan Tampilan</x-admin.sidebar-link>
    </nav>

    <!-- Bottom Actions -->
    <div class="px-2 py-2 border-t border-gray-100 bg-gray-50/50">
        <form action="/logout" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-all touch-target group">
                <i class="ph ph-power w-5 h-5 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
