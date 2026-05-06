@extends('layouts.admin')

@section('title', 'Kelola Stok')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0" x-data="{ showUpdateModal: false, updateTarget: '', currentStock: 0, productId: null }">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Pengelolaan Stok</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Perhatian Stok Menipis</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar produk dengan stok di bawah batas aman yang perlu tindakan segera.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/produk" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-box-arrow-up ph-bold w-4 h-4"></i> Daftar Produk Lengkap
            </a>
        </div>
    </div>

    <!-- Summary Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card: Kritis -->
        <div class="card p-5 group flex items-center justify-between hover:-translate-y-1 transition-transform border-l-4 border-l-red-500">
            <div>
                <p class="text-sm font-bold text-red-600 uppercase tracking-wide mb-1">Status Kritis</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-black text-gray-900 leading-none">{{ $stats['out_of_stock_count'] ?? 0 }}</h3>
                    <p class="text-sm font-medium text-gray-500 mb-0.5">Item (≤ 5)</p>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <i class="ph ph-warning-circle ph-duotone w-6 h-6 animate-pulse"></i>
            </div>
        </div>
        
        <!-- Card: Hampir Habis -->
        <div class="card p-5 group flex items-center justify-between hover:-translate-y-1 transition-transform border-l-4 border-l-amber-500">
            <div>
                <p class="text-sm font-bold text-amber-600 uppercase tracking-wide mb-1">Hampir Habis</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-black text-gray-900 leading-none">{{ $stats['low_stock_count'] ?? 0 }}</h3>
                    <p class="text-sm font-medium text-gray-500 mb-0.5">Item (6-10)</p>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="ph ph-trend-down ph-duotone w-6 h-6"></i>
            </div>
        </div>
        
        <!-- Card: Menipis -->
        <div class="card p-5 group flex items-center justify-between hover:-translate-y-1 transition-transform border-l-4 border-l-yellow-500">
            <div>
                <p class="text-sm font-bold text-yellow-600 uppercase tracking-wide mb-1">Stok Menipis</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-black text-gray-900 leading-none">{{ ($stats['low_stock_count'] ?? 0) + ($stats['out_of_stock_count'] ?? 0) }}</h3>
                    <p class="text-sm font-medium text-gray-500 mb-0.5">Item (11-15)</p>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center shrink-0">
                <i class="ph ph-hourglass-low ph-duotone w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="card p-0 overflow-hidden w-full border-t-4 border-t-red-500">

        <!-- Top Action Bar -->
        <div class="p-5 border-b border-primary-100 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center bg-gradient-to-r from-white to-primary-50/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                    <i class="ph ph-warning ph-bold w-5 h-5 text-red-500"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">Daftar Inventaris Menipis</h2>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">Menampilkan produk dengan level stok ≤ 15 unit (Total {{ $products->total() }} Item)</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64 shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="text" placeholder="Cari nama atau SKU..." 
                           class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                </div>
            </div>
        </div>

        <!-- Flowbite Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[900px]">
                <thead class="bg-gradient-to-r from-primary-50/50 to-primary-50/20 text-primary-700 text-xs uppercase font-bold tracking-wider border-b-2 border-primary-100">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 w-48 hidden md:table-cell">Kapasitas Stok</th>
                        <th class="px-6 py-4 text-center">Sisa Stok</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white border-b border-gray-200">

                     <!-- Row 1: Kritis -->
                    @forelse($products as $index => $product)
                    <tr class="hover:bg-{{ $product->stock <= 10 ? 'red' : ($product->stock <= 50 ? 'orange' : 'green') }}-50/40 transition-colors {{ $product->stock <= 10 ? 'bg-red-50/10' : '' }}">
                        <td class="px-6 py-5 text-gray-500 text-center text-sm font-medium">{{ $products->firstItem() + $index }}</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl overflow-hidden border border-gray-200 shrink-0 shadow-sm">
                                    <img loading="lazy" src="{{ $product->getFirstImage() }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-base leading-tight">{{ $product->name }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-500 font-medium">SKU: {{ $product->sku }}</span>
                                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                        <span class="text-xs text-gray-500 font-medium">{{ $product->weight }} {{ $product->unit }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($product->category)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-primary-50 text-primary-700 border border-primary-200">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 hidden md:table-cell">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold {{ $product->stock <= 10 ? 'text-red-600' : ($product->stock <= 50 ? 'text-orange-600' : 'text-green-600') }}">{{ round(($product->stock / 100) * 100) }}%</span>
                                <span class="text-xs font-medium text-gray-500">Max: 100</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-200 shadow-inner">
                                <div class="h-2 rounded-full {{ $product->stock <= 10 ? 'bg-red-500' : ($product->stock <= 50 ? 'bg-orange-500' : 'bg-green-500') }}" style="width: {{ min(($product->stock / 100) * 100, 100) }}%"></div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-2xl font-black {{ $product->stock <= 10 ? 'text-red-600' : ($product->stock <= 50 ? 'text-orange-600' : 'text-gray-900') }}">{{ $product->stock }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($product->stock <= 10)
                                <span class="inline-flex px-2.5 py-1 text-[11px] uppercase tracking-wider font-bold rounded-full bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                    Kritis
                                </span>
                            @elseif($product->stock <= 50)
                                <span class="inline-flex px-2.5 py-1 text-[11px] uppercase tracking-wider font-bold rounded-full bg-orange-100 text-orange-700 border border-orange-200 shadow-sm">
                                    Menipis
                                </span>
                            @else
                                <span class="inline-flex px-2.5 py-1 text-[11px] uppercase tracking-wider font-bold rounded-full bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                    Aman
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            @if($product->stock <= 10)
                                <button @click="updateTarget = '{{ $product->name }}'; currentStock = {{ $product->stock }}; productId = {{ $product->id }}; showUpdateModal = true;" 
                                        class="btn-primary text-xs h-8 px-3 shadow-md border-red-600 bg-red-600 hover:bg-red-700">
                                    Restock Cepat
                                </button>
                            @else
                                <button @click="updateTarget = '{{ $product->name }}'; currentStock = {{ $product->stock }}; productId = {{ $product->id }}; showUpdateModal = true;" 
                                        class="btn-primary text-xs h-8 px-3 shadow-md">
                                    Update Stok
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada produk ditemukan
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-5 border-t border-primary-100 flex w-full">
            {{ $products->links('vendor.pagination.custom') }}
        </div>
    </div>
    
    <!-- Restock Quick Action Modal -->
    <div x-show="showUpdateModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showUpdateModal = false"></div>
        
        <!-- Modal Content -->
        <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-[460px] relative z-10 overflow-hidden border border-slate-200"
             x-transition:enter="transition ease-out duration-300 delay-75" 
             x-transition:enter-start="opacity-0 translate-y-8 scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            <!-- Header: Vibrant & Branded -->
            <div class="px-8 py-6 bg-gradient-to-br from-primary-600 to-teal-700 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 transform translate-x-4 -translate-y-4">
                    <i class="ph ph-package ph-fill text-[120px]"></i>
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Restock Barang Cepat</h3>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-bold uppercase tracking-wider backdrop-blur-md border border-white/10">Quick Action</span>
                            <p class="text-primary-50 text-sm font-medium truncate max-w-[220px]" x-text="updateTarget"></p>
                        </div>
                    </div>
                    <button @click="showUpdateModal = false" class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all border border-white/20 backdrop-blur-sm shadow-inner group">
                        <i class="ph ph-x ph-bold w-5 h-5 transition-transform group-hover:rotate-90"></i>
                    </button>
                </div>
            </div>
            
            <form :action="'{{ url('/admin/stok') }}/' + productId" method="POST" class="p-8 space-y-6" id="stockUpdateForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="product_id" x-model="productId">
                
                <!-- Info Card: Current Stock -->
                <div class="group relative">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-primary-500 to-teal-500 rounded-2xl blur opacity-10 group-hover:opacity-20 transition duration-500"></div>
                    <div class="relative flex items-center justify-between p-5 bg-white rounded-2xl border border-slate-100 shadow-sm transition-all duration-300">
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Stok Saat Ini (Sisa)</p>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-4xl font-black text-slate-900 leading-none tracking-tight" x-text="currentStock"></span>
                                <span class="text-sm font-bold text-slate-400 uppercase tracking-wide">Unit</span>
                            </div>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center shadow-inner border border-primary-100/50">
                            <i class="ph ph-stack ph-fill text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Input: Quantity -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        Jumlah Penambahan Stok
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                            <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center font-bold text-lg border border-primary-100 shadow-sm">
                                +
                            </div>
                        </div>
                        <input type="number" name="quantity" min="1" value="10" required 
                               class="block w-full pl-16 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xl font-black text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all shadow-sm"
                               placeholder="0">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unit Baru</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ph ph-info ph-fill text-primary-500 mt-0.5"></i>
                        <p class="text-[11px] leading-relaxed text-slate-500 font-medium">Stok baru akan ditambahkan ke sisa stok saat ini secara otomatis.</p>
                    </div>
                </div>

                <!-- Input: Reason -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        Keterangan / Referensi Invois
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="ph ph-note-pencil ph-bold w-5 h-5"></i>
                        </div>
                        <input type="text" name="reason" placeholder="Misal: Restock dari supplier CV. Makmur" 
                               class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all"
                               required>
                    </div>
                </div>
            </form>
            
            <!-- Footer: Actions -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <button @click="showUpdateModal = false" type="button" 
                        class="px-5 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
                    Batal
                </button>
                <button type="submit" form="stockUpdateForm" 
                        class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-primary-600/20 hover:shadow-primary-600/40 hover:-translate-y-0.5 transition-all flex items-center gap-2 group">
                    <i class="ph ph-check-circle ph-bold w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    Konfirmasi Restock
                </button>
            </div>
        </div>
    </div>

</div>

<style>
    /* Hide spin buttons in Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Hide spin buttons in Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endsection
