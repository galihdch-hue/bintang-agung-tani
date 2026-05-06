@php
$totalItems = $cart['total_items'] ?? 0;
$total = $cart['total'] ?? 0;
$totalDiscount = collect($cart['items'] ?? [])->sum(function($item) { 
    return ($item['original_price'] ?? 0) - $item['unit_price']; 
});
@endphp

@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Header -->
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
                    <li class="text-gray-900 font-semibold">Keranjang</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
            <p class="text-gray-500 mt-2">Kelola produk yang ingin Anda beli</p>
        </div>
        
        @if($totalItems > 0)
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-700 rounded-xl font-semibold text-sm border border-primary-200">
                <i class="ph ph-shopping-cart"></i>
                {{ $totalItems }} Barang
            </span>
        </div>
        @endif
    </div>
    
    @if($totalItems > 0)
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Left Column: Cart Items -->
        <div class="flex-1 w-full space-y-6">
            
            <!-- Cart Card -->
            <div class="card overflow-hidden">
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-primary-50/40 to-primary-50/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="ph ph-package text-primary-600"></i>
                        </div>
                        <span class="font-bold text-gray-900">Daftar Produk</span>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider">
                                <th class="w-12 px-6 py-4 text-center">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer" checked>
                                </th>
                                <th class="py-4 px-6 text-gray-600 font-semibold">Produk</th>
                                <th class="py-4 px-6 text-gray-600 font-semibold text-center">Harga</th>
                                <th class="py-4 px-6 text-gray-600 font-semibold text-center">Jumlah</th>
                                <th class="py-4 px-6 text-gray-600 font-semibold text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($cart['items'] as $index => $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-6 text-center align-middle">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer" checked>
                                </td>
                                <td class="py-6 px-6 align-top">
                                    <div class="flex gap-4">
                                        <div class="w-20 h-20 shrink-0 bg-gray-50 border border-gray-100 rounded-xl p-2 flex items-center justify-center">
                                            @if($item['image'])
                                                <img loading="lazy" src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <i class="ph ph-package text-2xl text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1 flex flex-col justify-center">
                                            <h4 class="font-bold text-gray-900 text-base mb-1 line-clamp-2">{{ $item['name'] }}</h4>
                                            @if($item['original_price'])
                                            <span class="text-sm text-gray-400 line-through">Rp {{ number_format($item['original_price'], 0, ',', '.') }}</span>
                                            @endif
                                            <div class="font-bold text-primary-600 text-lg">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6 px-6 align-middle text-center">
                                    <div class="font-semibold text-gray-700">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                </td>
                                <td class="py-6 px-6 align-middle text-center">
                                    <form action="{{ route('user.cart.update', $item['id']) }}" 
                                          method="POST" 
                                          class="inline-flex items-center" 
                                          x-data="{ 
                                              qty: {{ $item['quantity'] }}, 
                                              submitting: false,
                                              updateQuantity(change) {
                                                  if (this.submitting) return;
                                                  let newQty = parseInt(this.qty) + change;
                                                  if (newQty < 1) return;
                                                  this.qty = newQty;
                                                  this.submitting = true;
                                                  this.$nextTick(() => this.$refs.form.submit());
                                              }
                                          }"
                                          x-ref="form">
                                        @csrf
                                        @method('PATCH')
                                        <div class="inline-flex items-center border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden h-11 p-1">
                                            <button type="button" 
                                                    @click="updateQuantity(-1)"
                                                    class="w-9 h-full flex items-center justify-center text-gray-500 hover:bg-white hover:text-primary-600 hover:shadow-sm rounded-lg transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                                                    :disabled="qty <= 1 || submitting">
                                                <i class="ph ph-minus text-sm" x-show="!submitting"></i>
                                                <i class="ph ph-spinner animate-spin text-sm" x-show="submitting"></i>
                                            </button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   x-model="qty" 
                                                   min="1" 
                                                   class="w-12 h-full text-center text-gray-900 font-extrabold border-none bg-transparent p-0 text-base focus:ring-0"
                                                   readonly>
                                            <button type="button" 
                                                    @click="updateQuantity(1)"
                                                    class="w-9 h-full flex items-center justify-center text-gray-500 hover:bg-white hover:text-primary-600 hover:shadow-sm rounded-lg transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                                                    :disabled="submitting">
                                                <i class="ph ph-plus text-sm" x-show="!submitting"></i>
                                                <i class="ph ph-spinner animate-spin text-sm" x-show="submitting"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td class="py-6 px-6 align-middle text-right">
                                    <div class="font-bold text-gray-900 text-lg">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                                    <form action="{{ route('user.cart.remove', $item['id']) }}" 
                                          method="POST" 
                                          class="inline mt-2"
                                          x-data="{ deleting: false }"
                                          @submit.prevent="
                                              if(confirm('Hapus produk ini dari keranjang?')) {
                                                  deleting = true;
                                                  $el.submit();
                                              }
                                          ">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-gray-400 hover:text-red-500 transition-colors text-sm flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed"
                                                :disabled="deleting">
                                            <i class="ph ph-trash" x-show="!deleting"></i>
                                            <i class="ph ph-spinner ph-spin text-red-500" x-show="deleting"></i>
                                            <span x-text="deleting ? 'Menghapus...' : 'Hapus'"></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                                            <i class="ph ph-shopping-cart text-4xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500">Keranjang kosong</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        
                        @if($totalItems > 0)
                        <tfoot>
                            <tr class="bg-primary-50/30 border-t-2 border-primary-100">
                                <td colspan="4" class="py-4 px-6">
                                    <span class="text-sm font-bold text-gray-700">Total ({{ $totalItems }} Barang)</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="text-xl font-bold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('user.produk.index') }}" class="btn-secondary w-full sm:w-auto flex-1 justify-center group">
                    <i class="ph ph-arrow-left transition-transform group-hover:-translate-x-1"></i>
                    Lanjut Belanja
                </a>
                <form action="{{ route('user.cart.clear') }}" method="POST" class="w-full sm:w-auto flex-1" onsubmit="return confirm('Kosongkan keranjang belanja Anda?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-full shadow-sm hover:shadow-md transition-all active:scale-[0.98]">
                        <i class="ph ph-trash"></i>
                        Kosongkan Keranjang
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Right Column: Order Summary -->
        <div class="w-full lg:w-[360px] shrink-0 space-y-6">
            
            @if($totalItems > 0)
            <!-- Summary Card -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-primary-50/40 to-primary-50/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="ph ph-receipt text-primary-600"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Rincian Belanja</h3>
                    </div>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Total Harga ({{ $totalItems }} Barang)</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($totalDiscount > 0)
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Total Diskon</span>
                        <span class="font-bold text-red-500">-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Ongkos Kirim</span>
                        <span class="text-xs text-gray-400">Dihitung saat checkout</span>
                    </div>
                    
                    <hr class="border-dashed border-gray-200">
                    
                    <div class="flex justify-between items-center pt-2">
                        <span class="font-bold text-gray-900 text-lg">Total Bayar</span>
                        <span class="font-bold text-primary-600 text-2xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <a href="{{ route('user.checkout.index') }}" class="btn-primary w-full justify-center text-lg py-4 group relative overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2">
                            Checkout Sekarang
                            <i class="ph ph-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-primary-600 to-primary-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </a>
                </div>
            </div>
            
            <!-- Security Info -->
            <div class="space-y-4">
                <div class="card p-4 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                        <i class="ph ph-shield-check text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Transaksi Aman</h4>
                        <p class="text-xs text-gray-500">Dilindungi oleh enkripsi SSL 256-bit.</p>
                    </div>
                </div>
                
                <div class="card p-4 flex items-start gap-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                        <i class="ph ph-truck text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Pengiriman Terjamin</h4>
                        <p class="text-xs text-gray-500">Garansi uang kembali jika pesanan tidak sampai.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="card py-16">
        <div class="flex flex-col items-center text-center max-w-md mx-auto">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <i class="ph ph-shopping-cart text-5xl text-gray-300"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Keranjang Belanja Kosong</h3>
            <p class="text-gray-500 mb-6">Anda belum menambahkan produk apapun ke keranjang.</p>
            <a href="{{ route('user.produk.index') }}" class="btn-primary px-8">
                <i class="ph ph-magnifying-glass"></i>
                Jelajahi Produk
            </a>
        </div>
    </div>
    @endif
</div>

<style>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection