@extends('layouts.admin')

@section('title', 'Pengaturan Tampilan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="/admin/dashboard" class="hover:text-gray-900 transition-colors">Dashboard</a>
                    </li>
                    <li><i class="ph ph-caret-right text-gray-400"></i></li>
                    <li class="text-gray-900 font-medium">Pengaturan</li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Pengaturan Aplikasi</h1>
            <p class="text-gray-500 mt-1 text-sm">Atur informasi toko, kontak, dan tampilan aplikasi.</p>
        </div>
        
        <div class="flex items-center gap-3 shrink-0">
            <form id="reset-ui-form" action="{{ route('admin.settings.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
            <button type="button" 
                    @click="$dispatch('confirm-action', { 
                        title: 'Reset Tampilan UI?', 
                        message: 'Apakah Anda yakin ingin mereset semua pengaturan tampilan UI ke default? <strong>Catatan:</strong> Pengaturan identitas toko tidak akan direset.', 
                        confirmText: 'Ya, Reset UI', 
                        action: () => document.getElementById('reset-ui-form').submit() 
                    })"
                    class="bg-white border border-gray-200 px-4 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors shadow-sm flex items-center gap-2 font-medium focus:outline-none h-10">
                <i class="ph ph-arrow-counter-clockwise w-5 h-5 text-gray-500"></i>
                <span class="hidden sm:inline">Reset UI</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="ph ph-check-circle w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6" x-data="{ activeTab: 'store' }">
        @csrf
        @method('PUT')

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl border border-gray-200 p-1.5 flex flex-wrap gap-1">
            <button type="button" @click="activeTab = 'store'" 
                :class="activeTab === 'store' ? 'bg-primary-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="ph ph-storefront"></i>
                <span class="hidden sm:inline">Info Toko</span>
            </button>
            <button type="button" @click="activeTab = 'contact'" 
                :class="activeTab === 'contact' ? 'bg-primary-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="ph ph-phone"></i>
                <span class="hidden sm:inline">Kontak</span>
            </button>
            <button type="button" @click="activeTab = 'social'" 
                :class="activeTab === 'social' ? 'bg-primary-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="ph ph-share-network"></i>
                <span class="hidden sm:inline">Sosial Media</span>
            </button>
            <button type="button" @click="activeTab = 'ui'" 
                :class="activeTab === 'ui' ? 'bg-primary-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="ph ph-paint-brush"></i>
                <span class="hidden sm:inline">Tampilan UI</span>
            </button>
            <button type="button" @click="activeTab = 'stats'" 
                :class="activeTab === 'stats' ? 'bg-primary-500 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="ph ph-chart-bar"></i>
                <span class="hidden sm:inline">Statistik</span>
            </button>
        </div>

        <!-- Store Settings Tab -->
        <div x-show="activeTab === 'store'" x-cloak x-transition class="space-y-6">
            <!-- Store Information -->
            <div class="card p-6 border-primary-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-storefront text-primary-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Informasi Toko</h2>
                        <p class="text-sm text-gray-500">Nama, alamat, dan identitas toko</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Store Name -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Nama Toko</span>
                            <span class="text-sm text-gray-500 block">Nama lengkap toko yang ditampilkan di seluruh aplikasi</span>
                        </label>
                        <input type="text" name="store_name" value="{{ $storeSettings['store_name'] ?? 'Bintang Agung Tani' }}" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <!-- Store Branch -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Nama Cabang</span>
                            <span class="text-sm text-gray-500 block">Nama cabang toko (untuk checkout)</span>
                        </label>
                        <input type="text" name="store_branch" value="{{ $storeSettings['store_branch'] ?? 'Cabang Agung Tani' }}" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <!-- Store Phone -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Telepon Toko</span>
                            <span class="text-sm text-gray-500 block">Nomor telepon untuk invoice</span>
                        </label>
                        <input type="text" name="store_phone" value="{{ $storeSettings['store_phone'] ?? '(0266) 123456' }}" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <!-- Store Address Line 1 -->
                    <div class="md:col-span-2">
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Alamat Baris 1 (Invoice)</span>
                            <span class="text-sm text-gray-500 block">Baris pertama alamat untuk invoice</span>
                        </label>
                        <input type="text" name="store_address_line1" value="{{ $storeSettings['store_address_line1'] ?? 'Jl. Raya Pertanian No.12, Kec. Cisaat' }}" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <!-- Store Address Line 2 -->
                    <div class="md:col-span-2">
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Alamat Baris 2 (Invoice)</span>
                            <span class="text-sm text-gray-500 block">Baris kedua alamat untuk invoice</span>
                        </label>
                        <input type="text" name="store_address_line2" value="{{ $storeSettings['store_address_line2'] ?? 'Kabupaten Sukabumi, Jawa Barat 43152' }}" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <!-- Store Address Full -->
                    <div class="md:col-span-2">
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Alamat Lengkap</span>
                            <span class="text-sm text-gray-500 block">Alamat lengkap untuk footer dan halaman kontak</span>
                        </label>
                        <textarea name="store_address" rows="2" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none">{{ $storeSettings['store_address'] ?? 'Jl. Pertanian No. 125, Jakarta Selatan, Indonesia' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Operational Hours -->
            <div class="card p-6 border-amber-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-clock text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Jam Operasional</h2>
                        <p class="text-sm text-gray-500">Informasi waktu layanan toko</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Operational Hours -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Jam Operasional</span>
                            <span class="text-sm text-gray-500 block">Format: 08.00 - 17.00</span>
                        </label>
                        <input type="text" name="operational_hours" value="{{ $operationalSettings['operational_hours'] ?? '08.00 - 17.00' }}" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <!-- Operational Hours Full -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Jam Operasional Lengkap</span>
                            <span class="text-sm text-gray-500 block">Dengan zona waktu (contoh: 08.00 - 16.00 WIB)</span>
                        </label>
                        <input type="text" name="operational_hours_full" value="{{ $operationalSettings['operational_hours_full'] ?? '08.00 - 16.00 WIB' }}" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Settings Tab -->
        <div x-show="activeTab === 'contact'" x-cloak x-transition class="space-y-6" style="display: none;">
            <!-- Contact Information -->
            <div class="card p-6 border-blue-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-phone text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Informasi Kontak</h2>
                        <p class="text-sm text-gray-500">WhatsApp, email, dan kontak customer service</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- WhatsApp -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Nomor WhatsApp</span>
                            <span class="text-sm text-gray-500 block">Format: 08xx-xxxx-xxxx</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-whatsapp-logo absolute left-3 top-1/2 -translate-y-1/2 text-green-600 w-5 h-5"></i>
                            <input type="text" name="whatsapp_number" value="{{ $contactSettings['whatsapp_number'] ?? '0822-1234-5678' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Email Kontak</span>
                            <span class="text-sm text-gray-500 block">Email untuk customer service</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                            <input type="email" name="contact_email" value="{{ $contactSettings['contact_email'] ?? 'info@bintangtani.com' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>

                    <!-- Support Email -->
                    <div class="md:col-span-2">
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Email Support</span>
                            <span class="text-sm text-gray-500 block">Email untuk bantuan teknis (lupa password, dll)</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-envelope-simple absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                            <input type="email" name="support_email" value="{{ $contactSettings['support_email'] ?? 'support@bintangagungtani.com' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Tab -->
        <div x-show="activeTab === 'social'" x-cloak x-transition class="space-y-6" style="display: none;">
            <!-- Social Media Links -->
            <div class="card p-6 border-pink-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-share-network text-pink-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Sosial Media</h2>
                        <p class="text-sm text-gray-500">Link profil sosial media toko</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Facebook -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Facebook</span>
                            <span class="text-sm text-gray-500 block">URL halaman Facebook</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-facebook-logo absolute left-3 top-1/2 -translate-y-1/2 text-blue-600 w-5 h-5"></i>
                            <input type="url" name="social_facebook" value="{{ $socialSettings['social_facebook'] ?? 'https://facebook.com/bintangagungtani' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Instagram</span>
                            <span class="text-sm text-gray-500 block">URL halaman Instagram</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-instagram-logo absolute left-3 top-1/2 -translate-y-1/2 text-pink-600 w-5 h-5"></i>
                            <input type="url" name="social_instagram" value="{{ $socialSettings['social_instagram'] ?? 'https://instagram.com/bintangagungtani' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>

                    <!-- Twitter -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Twitter / X</span>
                            <span class="text-sm text-gray-500 block">URL profil Twitter</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-twitter-logo absolute left-3 top-1/2 -translate-y-1/2 text-sky-500 w-5 h-5"></i>
                            <input type="url" name="social_twitter" value="{{ $socialSettings['social_twitter'] ?? 'https://twitter.com/bintangtani' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>

                    <!-- YouTube -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">YouTube</span>
                            <span class="text-sm text-gray-500 block">URL channel YouTube</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-youtube-logo absolute left-3 top-1/2 -translate-y-1/2 text-red-600 w-5 h-5"></i>
                            <input type="url" name="social_youtube" value="{{ $socialSettings['social_youtube'] ?? 'https://youtube.com/bintangagungtani' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- UI Settings Tab -->
        <div x-show="activeTab === 'ui'" x-cloak x-transition class="space-y-6" style="display: none;">
            <!-- Dashboard Sections -->
            <div class="card p-6 border-primary-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-layout text-primary-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Dashboard User</h2>
                        <p class="text-sm text-gray-500">Atur section yang muncul di halaman dashboard user</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_welcome_banner" value="1" {{ ($uiSettings['show_welcome_banner'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Banner Selamat Datang</span>
                            <span class="text-sm text-gray-500">Tampilkan banner welcome di atas</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_categories_grid" value="1" {{ ($uiSettings['show_categories_grid'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Grid Kategori</span>
                            <span class="text-sm text-gray-500">Tampilkan kategori produk</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_stats_overview" value="1" {{ ($uiSettings['show_stats_overview'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Ringkasan Statistik</span>
                            <span class="text-sm text-gray-500">Tampilkan statistik keranjang, pesanan</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_best_sellers" value="1" {{ ($uiSettings['show_best_sellers'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Produk Terlaris</span>
                            <span class="text-sm text-gray-500">Tampilkan section best sellers</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_new_arrivals" value="1" {{ ($uiSettings['show_new_arrivals'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Produk Terbaru</span>
                            <span class="text-sm text-gray-500">Tampilkan section new arrivals</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_promo_banners" value="1" {{ ($uiSettings['show_promo_banners'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Banner Promo</span>
                            <span class="text-sm text-gray-500">Tampilkan banner promo di bawah</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Product Page Settings -->
            <div class="card p-6 border-amber-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-storefront text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Halaman Produk</h2>
                        <p class="text-sm text-gray-500">Atur tampilan halaman katalog produk</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_category_filter" value="1" {{ ($uiSettings['show_category_filter'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Filter Kategori</span>
                            <span class="text-sm text-gray-500">Tampilkan filter kategori di sidebar</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_price_filter" value="1" {{ ($uiSettings['show_price_filter'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Filter Harga</span>
                            <span class="text-sm text-gray-500">Tampilkan filter rentang harga</span>
                        </div>
                    </label>

                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Produk Per Halaman</span>
                            <span class="text-sm text-gray-500 block">Jumlah produk yang ditampilkan (8-100)</span>
                        </label>
                        <input type="number" name="products_per_page" value="{{ $uiSettings['products_per_page'] ?? 20 }}" min="8" max="100" class="w-full md:w-48 px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Jumlah Kategori di Sidebar</span>
                            <span class="text-sm text-gray-500 block">Kategori yang muncul di sidebar (1-20)</span>
                        </label>
                        <input type="number" name="sidebar_category_count" value="{{ $uiSettings['sidebar_category_count'] ?? 8 }}" min="1" max="20" class="w-full md:w-48 px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
            </div>

            <!-- Features Settings -->
            <div class="card p-6 border-blue-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-toggle-left text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Fitur Aplikasi</h2>
                        <p class="text-sm text-gray-500">Aktifkan atau nonaktifkan fitur tertentu</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="enable_cart_drawer" value="1" {{ ($uiSettings['enable_cart_drawer'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Cart Drawer</span>
                            <span class="text-sm text-gray-500">Slide-over cart di kanan</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="enable_toast_notifications" value="1" {{ ($uiSettings['enable_toast_notifications'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Toast Notifications</span>
                            <span class="text-sm text-gray-500">Notifikasi popup di pojok kanan</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="enable_wishlist" value="1" {{ ($uiSettings['enable_wishlist'] ?? false) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Wishlist</span>
                            <span class="text-sm text-gray-500">Fitur simpan produk favorit</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_search_bar" value="1" {{ ($uiSettings['show_search_bar'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Search Bar</span>
                            <span class="text-sm text-gray-500">Kolom pencarian di navbar</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_cart_icon" value="1" {{ ($uiSettings['show_cart_icon'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Icon Cart</span>
                            <span class="text-sm text-gray-500">Ikon keranjang di navbar</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="show_user_menu" value="1" {{ ($uiSettings['show_user_menu'] ?? true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <div>
                            <span class="font-medium text-gray-900 block">Menu User</span>
                            <span class="text-sm text-gray-500">Dropdown menu user di navbar</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Statistics Settings Tab -->
        <div x-show="activeTab === 'stats'" x-cloak x-transition class="space-y-6" style="display: none;">
            <!-- Statistics Display -->
            <div class="card p-6 border-purple-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="ph ph-chart-bar text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Statistik Display</h2>
                        <p class="text-sm text-gray-500">Statistik yang ditampilkan di halaman login/landing</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Farmers -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Total Petani</span>
                            <span class="text-sm text-gray-500 block">Format: 10K+ (untuk tampilan landing page)</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-users absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                            <input type="text" name="total_farmers" value="{{ $statisticsSettings['total_farmers'] ?? '10K+' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>

                    <!-- Total Products -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Total Produk</span>
                            <span class="text-sm text-gray-500 block">Format: 200+ (untuk tampilan landing page)</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-package absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                            <input type="text" name="total_products" value="{{ $statisticsSettings['total_products'] ?? '500+' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div>
                        <label class="block mb-2">
                            <span class="font-medium text-gray-900">Total Pesanan</span>
                            <span class="text-sm text-gray-500 block">Format: 50K+ (untuk tampilan landing page)</span>
                        </label>
                        <div class="relative">
                            <i class="ph ph-shopping-cart absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                            <input type="text" name="total_orders" value="{{ $statisticsSettings['total_orders'] ?? '50K+' }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <i class="ph ph-info text-amber-600 w-5 h-5 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-amber-800 font-medium">Catatan</p>
                            <p class="text-sm text-amber-700 mt-1">Statistik ini hanya untuk tampilan visual di halaman login dan landing page. Nilai bisa disesuaikan dengan perkiraan atau data aktual toko Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-6 border-t border-gray-200">
            <button type="submit" class="btn-primary flex items-center gap-2 px-6 py-3 shadow-lg">
                <i class="ph ph-floppy-disk w-5 h-5"></i>
                <span>Simpan Semua Pengaturan</span>
            </button>
        </div>
    </form>
</div>
@endsection
