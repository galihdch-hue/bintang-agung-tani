@php
    use App\Models\Setting;

    $cartItems = $cart->items ?? collect();
    $cartSubtotal = $cart->getFormattedSubtotal() ?? 'Rp 0';
    $cartTotal = $cartSubtotal;
    $addressesCount = $addresses->count() ?? 0;
@endphp

@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('user.dashboard') }}"
                                class="hover:text-primary-600 transition-colors flex items-center gap-1">
                                <i class="ph ph-house text-xs"></i>
                                Dashboard
                            </a>
                        </li>
                        <li><i class="ph ph-caret-right text-gray-300 text-xs"></i></li>
                        <li>
                            <a href="{{ route('user.cart.index') }}"
                                class="hover:text-primary-600 transition-colors">Keranjang</a>
                        </li>
                        <li><i class="ph ph-caret-right text-gray-300 text-xs"></i></li>
                        <li class="text-gray-900 font-semibold">Checkout</li>
                    </ol>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Checkout</h1>
                    <div
                        class="bg-gradient-to-br from-primary-500 to-primary-600 text-white p-2 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="ph ph-bag-check ph-fill text-xl"></i>
                    </div>
                </div>
                <p class="text-gray-500 mt-2">Selesaikan pesanan Anda dengan aman dan cepat</p>
            </div>

            <!-- Progress Steps -->
            <div class="flex items-center gap-4 bg-white px-6 py-3 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                        <i class="ph ph-check text-primary-600 font-bold"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Keranjang</span>
                </div>
                <i class="ph ph-caret-right text-gray-300"></i>
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-sm">2</span>
                    </div>
                    <span class="text-sm font-bold text-primary-600">Checkout</span>
                </div>
                <i class="ph ph-caret-right text-gray-300"></i>
                <div class="flex items-center gap-2 opacity-50">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-gray-400 font-bold text-sm">3</span>
                    </div>
                    <span class="text-sm font-medium text-gray-500">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            <!-- Left Column: Address & Order Summary -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Address Section -->
                <div class="card-featured overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="ph ph-map-pin text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">Alamat Pengambilan</h2>
                                <p class="text-primary-100 text-sm">Pastikan alamat yang dipakai untuk data pengambilan sudah benar</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6" x-data="checkoutAddressManager({{ auth()->user()->defaultAddress ? json_encode(auth()->user()->defaultAddress) : 'null' }}, {{ json_encode($addresses) }})">
                        <div class="space-y-4">
                            <div class="bg-gradient-to-br from-primary-50 to-primary-100/50 border-2 border-primary-200 rounded-2xl p-6 h-fit">
                                <div class="flex items-center justify-between gap-3 mb-4">
                                    <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                                        Alamat Utama
                                    </span>
                                    <button type="button" @click="showAddressModal = true"
                                        class="btn-secondary text-sm flex items-center justify-center gap-2">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                        Ubah
                                    </button>
                                </div>

                                <h3 class="font-bold text-gray-900 text-lg mb-4" x-text="selectedAddressData.recipient_name"></h3>

                                <div class="space-y-3 text-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                                            <i class="ph ph-phone text-primary-500"></i>
                                        </div>
                                        <span class="text-gray-700 pt-1.5" x-text="selectedAddressData.phone"></span>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                                            <i class="ph ph-map-pin text-primary-500"></i>
                                        </div>
                                        <span class="text-gray-700 leading-relaxed pt-1" x-text="selectedAddressData.full_address"></span>
                                    </div>

                                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-primary-200">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                                                <i class="ph ph-storefront text-white"></i>
                                            </div>
                                            <span class="font-bold text-gray-800">{{ Setting::get('store_branch', 'Cabang Utama') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Selection Modal -->
                        <div x-show="showAddressModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                            style="display: none;">
                            <div
                                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showAddressModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0" @click="showAddressModal = false"
                                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                <div x-show="showAddressModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Alamat Pengambilan</h3>

                                        <div class="space-y-3 max-h-80 overflow-y-auto">
                                            <template x-for="address in addresses" :key="address.id">
                                                <div class="flex items-start p-4 border-2 rounded-xl cursor-pointer transition-colors"
                                                    :class="selectedAddress == address.id ? 'border-primary-500 bg-primary-50' :
                                                        'border-gray-200 hover:border-primary-300'"
                                                    @click="selectAddress(address)">
                                                    <input type="radio" :value="address.id" x-model="selectedAddress"
                                                        class="mt-1 mr-3 text-primary-600 focus:ring-primary-500" required>
                                                    <div class="flex-1">
                                                        <p class="font-bold text-gray-900"
                                                            x-text="address.label || 'Alamat'"></p>
                                                        <p class="text-sm text-gray-600 mt-1"
                                                            x-text="address.full_address"></p>
                                                        <p class="text-sm text-gray-500" x-text="address.phone"></p>
                                                        <span x-show="address.is_default"
                                                            class="inline-block mt-2 text-xs font-bold text-primary-600 bg-primary-100 px-2 py-1 rounded">Default</span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="addresses.length === 0" class="text-center py-8">
                                                <p class="text-gray-500 mb-4">Belum ada alamat tersimpan</p>
                                            </div>
                                        </div>

                                        <!-- Add New Address Button -->
                                        <div class="mt-4 pt-4 border-t border-gray-100">
                                            <button type="button"
                                                @click="showAddressModal = false; showAddAddressModal = true"
                                                class="w-full flex items-center justify-center gap-2 text-primary-600 hover:text-primary-700 font-medium py-2 rounded-lg hover:bg-primary-50 transition-colors">
                                                <i class="ph ph-plus text-lg"></i>
                                                Tambah Alamat Baru
                                            </button>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                        <button type="button" @click="confirmAddressSelection()"
                                            class="btn-primary w-full sm:w-auto">
                                            Pilih Alamat
                                        </button>
                                        <button type="button" @click="showAddressModal = false"
                                            class="btn-secondary w-full sm:w-auto mt-3 sm:mt-0">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add New Address Modal -->
                        <div x-show="showAddAddressModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                            style="display: none;">
                            <div
                                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showAddAddressModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0" @click="showAddAddressModal = false"
                                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                <div x-show="showAddAddressModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                    <form @submit.prevent="submitNewAddress" class="bg-white">
                                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Alamat Baru</h3>

                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Label Alamat
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="newAddress.label"
                                                        placeholder="Contoh: Rumah, Kantor" required
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Penerima
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="newAddress.recipient_name" required
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                                        <i class="ph ph-phone text-gray-400 mr-1"></i>
                                                        Nomor Telepon <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="tel" x-model="newAddress.phone" required
                                                        placeholder="Contoh: 08123456789"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                    <p class="text-xs text-gray-500 mt-1">Nomor telepon wajib diisi untuk
                                                        pengambilan</p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Alamat
                                                        Lengkap <span class="text-red-500">*</span></label>
                                                    <textarea x-model="newAddress.full_address" rows="3" required
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Kota
                                                            <span class="text-red-500">*</span></label>
                                                        <input type="text" x-model="newAddress.city" required
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Provinsi
                                                            <span class="text-red-500">*</span></label>
                                                        <input type="text" x-model="newAddress.province" required
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kode
                                                        Pos</label>
                                                    <input type="text" x-model="newAddress.postal_code"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" x-model="newAddress.is_default"
                                                        id="is_default_new"
                                                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                                    <label for="is_default_new" class="text-sm text-gray-700">Jadikan
                                                        alamat utama</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                            <button type="submit" :disabled="submitting"
                                                class="btn-primary w-full sm:w-auto flex items-center justify-center gap-2">
                                                <span x-show="!submitting">Simpan Alamat</span>
                                                <span x-show="submitting" class="flex items-center gap-2">
                                                    <i class="ph ph-spinner animate-spin"></i>
                                                    Menyimpan...
                                                </span>
                                            </button>
                                            <button type="button" @click="showAddAddressModal = false; resetNewAddress()"
                                                class="btn-secondary w-full sm:w-auto mt-3 sm:mt-0">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="checkout-selected-address" class="checkout-selected-address" name="address_id" :value="selectedAddress">

                <!-- Order Summary Table -->
                <div class="card-featured overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                <i class="ph ph-receipt text-primary-600 text-xl"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">Ringkasan Pesanan</h2>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead
                                class="bg-gray-50/50 text-gray-500 font-semibold border-b border-gray-100 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 min-w-[300px]">Produk</th>
                                    <th class="px-6 py-4 text-center whitespace-nowrap">Harga</th>
                                    <th class="px-6 py-4 text-right whitespace-nowrap">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($cartItems as $cartItem)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-6 py-5">
                                            <div class="flex gap-4 items-start">
                                                <div
                                                    class="w-16 h-16 shrink-0 bg-gray-50 border border-gray-100 rounded-xl p-1.5 flex items-center justify-center overflow-hidden">
                                                    @if ($cartItem->product && $cartItem->product->getFirstImage())
                                                        <img loading="lazy"
                                                            src="{{ asset($cartItem->product->getFirstImage()) }}"
                                                            alt="{{ $cartItem->product_name }}"
                                                            class="w-full h-full object-cover rounded-lg mix-blend-multiply group-hover:scale-105 transition-transform duration-300">
                                                    @else
                                                        <i class="ph ph-package text-2xl text-gray-300"></i>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h4
                                                        class="font-bold text-gray-900 text-sm mb-1 line-clamp-2 leading-tight group-hover:text-primary-600 transition-colors">
                                                        {{ $cartItem->product_name }}</h4>
                                                    <p class="text-xs text-gray-500">Qty: {{ $cartItem->quantity }} item
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center align-middle">
                                            <div class="font-bold text-primary-600">
                                                {{ $cartItem->getFormattedUnitPrice() }}</div>
                                        </td>
                                        <td class="px-6 py-5 text-right align-middle">
                                            <div class="font-bold text-gray-900 text-lg">
                                                {{ $cartItem->getFormattedTotal() }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-4">
                                                <div
                                                    class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i class="ph ph-shopping-cart text-4xl text-gray-300"></i>
                                                </div>
                                                <p class="text-gray-500">Keranjang kosong</p>
                                                <a href="{{ route('user.produk.index') }}"
                                                    class="text-primary-600 font-semibold hover:text-primary-700">Mulai
                                                    Belanja</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100/50">
                                <tr>
                                    <td colspan="2" class="px-6 pt-5 pb-2 text-right font-medium text-gray-500">
                                        Subtotal Produk</td>
                                    <td class="px-6 pt-5 pb-2 text-right font-bold text-gray-900 text-base">
                                        {{ $cartSubtotal }}</td>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                    <td colspan="2"
                                        class="px-6 pt-2 pb-5 text-right font-bold text-gray-900 text-lg border-t border-dashed border-gray-200">
                                        Grand Total</td>
                                    <td
                                        class="px-6 pt-2 pb-5 text-right font-bold text-primary-600 text-2xl border-t border-dashed border-gray-200">
                                        {{ $cartSubtotal }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Final Summary -->
            <div class="lg:col-span-4 space-y-6">

                <div class="card-featured p-6 sticky top-24">
                    <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                            <i class="ph ph-credit-card text-amber-600 text-xl"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Ringkasan Pembayaran</h2>
                    </div>

                    <!-- Info Box -->
                    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                                <i class="ph ph-info text-amber-600"></i>
                            </div>
                            <p class="leading-relaxed">Pembayaran dan pengambilan barang dilakukan langsung di toko kami
                                dengan menunjukkan <strong>Barcode Pesanan</strong>.</p>
                        </div>
                    </div>

                    <!-- Total Summary -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center text-gray-600 text-sm">
                            <span>Subtotal</span>
                            <span class="font-semibold">{{ $cartSubtotal }}</span>
                        </div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-900 text-lg">Total Bayar</span>
                            <span class="font-bold text-primary-600 text-2xl">{{ $cartSubtotal }}</span>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form action="{{ route('user.checkout.store') }}" method="POST" class="space-y-4"
                        x-data="{ loading: false }"
                        @submit.prevent="const selectedAddressInput = document.getElementById('checkout-selected-address'); $refs.addressId.value = selectedAddressInput ? selectedAddressInput.value : ''; if (!$refs.addressId.value) { alert('Silakan pilih alamat terlebih dahulu.'); loading = false; return; } loading = true; $el.submit();">
                        @csrf
                        <!-- address_id synced from address selection panel -->
                        <input type="hidden" name="address_id" x-ref="addressId">
                        <input type="hidden" name="shipping_cost" value="0">
                        <input type="hidden" name="shipping_courier" value="Ambil di Toko">
                        <input type="hidden" name="shipping_service" value="Ambil Sendiri">

                        <button type="submit" :disabled="loading"
                            class="w-full relative overflow-hidden bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-xl py-4 shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 group">
                            <span x-show="!loading" x-cloak class="relative z-10 flex items-center gap-2">
                                <i class="ph ph-check-circle text-xl"></i>
                                Buat Pesanan
                            </span>
                            <span x-show="loading" x-cloak class="relative z-10 flex items-center gap-2">
                                <i class="ph ph-spinner animate-spin text-xl"></i>
                                Memproses...
                            </span>
                            <!-- Shine Effect -->
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                            </div>
                        </button>
                    </form>

                    <p class="text-xs text-gray-500 mt-4 text-center leading-relaxed">
                        Setelah membuat pesanan, Anda akan dipindahkan ke halaman pemilihan metode pembayaran dan melanjutkan transaksi untuk pengambilan di toko.
                    </p>
                </div>

            </div>
        </div>

        <!-- Value Props Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mt-8">
            <div class="card-featured p-6 flex items-start gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl flex items-center justify-center shrink-0 border border-green-200">
                    <i class="ph ph-storefront text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-1">Ambil di Toko</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Pesan online, bayar dan ambil barang langsung secara
                        aman di toko kami.</p>
                </div>
            </div>
            <div class="card-featured p-6 flex items-start gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl flex items-center justify-center shrink-0 border border-blue-200">
                    <i class="ph ph-shield-check text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-1">Transaksi Aman</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Invoice dan bukti pemesanan digital otomatis terbit
                        untuk setiap pembelian.</p>
                </div>
            </div>
            <div class="card-featured p-6 flex items-start gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl flex items-center justify-center shrink-0 border border-amber-200">
                    <i class="ph ph-headset text-amber-600 text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-1">Layanan Pelanggan</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Hubungi Kami Setiap Jam Kerja
                        {{ Setting::get('operational_hours_full', '08.00 - 16.00 WIB') }} untuk bantuan pesanan.</p>
                </div>
            </div>
        </div>

    </div>

    <style>
        /* Progress Steps Animation */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            .animate-ping {
                animation: none;
            }
        }

        /* Table Hover Effect */
        tr:hover td {
            background-color: rgba(249, 250, 251, 0.5);
        }
    </style>

    <script>
        function checkoutAddressManager(defaultAddress, initialAddresses) {
            const defaultData = {
                recipient_name: '{{ auth()->user()->name }}',
                phone: '{{ auth()->user()->phone ?? '-' }}',
                full_address: '{{ auth()->user()->defaultAddress?->full_address ?? 'Belum ada alamat default' }}'
            };

            return {
                showAddressModal: false,
                showAddAddressModal: false,
                selectedAddress: defaultAddress?.id || null,
                selectedAddressData: defaultAddress ? {
                    id: defaultAddress.id,
                    recipient_name: defaultAddress.recipient_name,
                    phone: defaultAddress.phone,
                    full_address: defaultAddress.full_address
                } : defaultData,
                addresses: initialAddresses || [],
                submitting: false,
                newAddress: {
                    label: '',
                    recipient_name: '{{ auth()->user()->name }}',
                    phone: '',
                    full_address: '',
                    city: '',
                    province: '',
                    postal_code: '',
                    is_default: false
                },

                init() {
                    if (!this.selectedAddress && this.addresses.length > 0) {
                        const firstAddress = this.addresses[0];
                        this.selectAddress(firstAddress);
                    } else {
                        this.syncSelectedAddressInput();
                    }
                },

                syncSelectedAddressInput() {
                    const hiddenInput = document.getElementById('checkout-selected-address');
                    if (hiddenInput) {
                        hiddenInput.value = this.selectedAddress || '';
                    }
                },

                selectAddress(address) {
                    this.selectedAddress = address.id;
                    this.selectedAddressData = {
                        id: address.id,
                        recipient_name: address.recipient_name,
                        phone: address.phone,
                        full_address: address.full_address
                    };
                    this.syncSelectedAddressInput();
                },

                confirmAddressSelection() {
                    if (this.selectedAddress && this.selectedAddressData) {
                        this.syncSelectedAddressInput();
                        this.showAddressModal = false;
                    }
                },

                resetNewAddress() {
                    this.newAddress = {
                        label: '',
                        recipient_name: '{{ auth()->user()->name }}',
                        phone: '',
                        full_address: '',
                        city: '',
                        province: '',
                        postal_code: '',
                        is_default: false
                    };
                },

                async submitNewAddress() {
                    if (!this.newAddress.phone || this.newAddress.phone.trim() === '') {
                        alert('Nomor telepon wajib diisi!');
                        return;
                    }

                    this.submitting = true;

                    try {
                        const response = await fetch('{{ route('user.alamat.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.newAddress)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.addresses.push(data.address);

                            this.selectedAddress = data.address.id;
                            this.selectedAddressData = {
                                id: data.address.id,
                                recipient_name: data.address.recipient_name,
                                phone: data.address.phone,
                                full_address: data.address.full_address
                            };
                            this.syncSelectedAddressInput();

                            this.showAddAddressModal = false;
                            this.resetNewAddress();

                            if (window.toastr) {
                                toastr.success('Alamat berhasil ditambahkan');
                            }
                        } else {
                            if (data.errors) {
                                const errorMessages = Object.values(data.errors).flat().join('\n');
                                alert(errorMessages);
                            } else {
                                alert(data.message || 'Gagal menambahkan alamat');
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    } finally {
                        this.submitting = false;
                    }
                }
            };
        }
    </script>
@endsection
