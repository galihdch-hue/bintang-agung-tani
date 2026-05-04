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
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
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
        <div class="p-5 border-t border-primary-100">
            {{ $products->links() }}
        </div>
    </div>
    
    <!-- Restock Quick Action Modal -->
    <div x-show="showUpdateModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showUpdateModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-[440px] mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-300 delay-75" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-8 scale-95">
            
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-50/50 to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-gray-900">Restock Barang Cepat</h3>
                    <p class="text-sm font-medium text-gray-600 mt-1 truncate max-w-[300px]" x-text="updateTarget"></p>
                </div>
                <button @click="showUpdateModal = false" class="text-gray-400 hover:text-gray-900 relative z-10 bg-gray-100 rounded-lg p-1.5 transition-colors border border-transparent hover:border-gray-200 hover:shadow-sm">
                    <i class="ph ph-x ph-bold w-5 h-5"></i>
                </button>
            </div>
            
            <form :action="'{{ url('/admin/stok') }}/' + productId" method="POST" class="p-6 space-y-5" id="stockUpdateForm" x-ref="stockForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="product_id" x-model="productId">
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Stok Saat Ini (Sisa)</p>
                        <p class="text-3xl font-black text-gray-900 leading-none" x-text="currentStock"></p>
                    </div>
                </div>

                <div>
                    <label class="form-label block mb-1.5">Jumlah Penambahan Stok <span class="text-red-500">*</span></label>
                    <div class="relative flex items-center">
                        <span class="absolute left-0 pl-4 font-bold text-xl text-primary-500 pointer-events-none">+</span>
                        <input type="number" name="quantity" min="1" value="10" required class="form-input w-full pl-9 text-lg font-bold text-gray-900 bg-white border-primary-200 focus:ring-primary-500/20 focus:border-primary-500 shadow-sm" style="height: 3rem;">
                    </div>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Stok baru akan ditambahkan ke sisa stok saat ini.</p>
                </div>

                <div>
                    <label class="form-label block mb-1.5">Keterangan / Referensi Invois <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" placeholder="Misal: Restock supplier" class="form-input w-full" required>
                </div>
            </form>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button @click="showUpdateModal = false" type="button" class="text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">Batalkan</button>
                <button type="submit" form="stockUpdateForm" class="btn-primary shadow-md text-sm px-6 h-10">
                    <i class="ph ph-check ph-bold w-4 h-4"></i> Konfirmasi Restock
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
