@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="/admin/dashboard" class="hover:text-gray-800 transition-colors">Dashboard Admin</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <span class="text-gray-900 font-medium">Kelola Produk</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Kelola Produk</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola master data produk, harga, dan stok.</p>
        </div>
        <div class="flex items-center gap-3">
             {{-- Add Product Button --}}
        </div>
    </div>

    <!-- Top Action Bar -->
    <form action="{{ route('admin.produk.index') }}" method="GET" id="filter-form" class="space-y-4">
        <div class="card p-4 sm:p-5 flex flex-col sm:flex-row gap-4 justify-between items-center w-full shadow-sm border-gray-200">
            <div class="relative w-full sm:max-w-md">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama produk, SKU..." 
                       class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0">
                <button type="submit" class="btn-primary w-full sm:w-auto text-sm justify-center shadow-md py-2.5">
                    <i class="ph ph-magnifying-glass ph-bold w-4 h-4"></i> Cari
                </button>
                <a href="{{ route('admin.produk.create') }}" class="btn-secondary bg-white border-primary-100 text-primary-700 hover:bg-primary-50 w-full sm:w-auto text-sm justify-center shadow-sm py-2.5">
                    <i class="ph ph-plus ph-bold w-4 h-4"></i> Tambah Produk
                </a>
            </div>
        </div>

    <!-- Main Container Card -->
    <div class="card overflow-hidden border-primary-100 shadow-sm">
        <!-- Table Toolbar -->
        <div class="px-6 py-4 border-b border-primary-100 flex flex-col xl:flex-row gap-4 items-start xl:items-center justify-between bg-white">
            <div class="flex items-center gap-2">
                <i class="ph ph-package w-5 h-5 text-primary-600 ph-fill"></i>
                <h2 class="text-sm font-bold text-gray-900 tracking-wide">Daftar Produk</h2>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                <div class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1">
                    <i class="ph ph-funnel ph-bold w-3 h-3"></i> Filter
                </div>
                
                {{-- Category Filter --}}
                <div class="relative w-full sm:w-40">
                    <select name="category" onchange="this.form.submit()" 
                            class="w-full pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-primary-500/20 focus:border-primary-500 appearance-none cursor-pointer transition-colors">
                        <option value="">Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 w-3 h-3 pointer-events-none"></i>
                </div>

                {{-- Stock Status Filter --}}
                <div class="relative w-full sm:w-36">
                    <select name="stock_status" onchange="this.form.submit()" 
                            class="w-full pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-primary-500/20 focus:border-primary-500 appearance-none cursor-pointer transition-colors">
                        <option value="">Stok</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Tersedia</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Menipis</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 w-3.5 h-3.5 pointer-events-none"></i>
                </div>

                {{-- Product Status --}}
                <div class="relative w-full sm:w-36">
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-primary-500/20 focus:border-primary-500 appearance-none cursor-pointer transition-colors">
                        <option value="">Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 w-3 h-3 pointer-events-none"></i>
                </div>

                @if(request()->anyFilled(['search', 'category', 'stock_status', 'status']))
                    <a href="{{ route('admin.produk.index') }}" class="inline-flex items-center justify-center w-full sm:w-auto gap-1.5 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg border border-red-100 transition-colors" title="Bersihkan Filter">
                        <i class="ph ph-x-circle ph-fill"></i> Reset
                    </a>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap lg:whitespace-normal">
                <thead class="bg-gradient-to-r from-primary-50/50 to-primary-50/20 text-primary-700 text-xs font-bold uppercase tracking-wide border-b-2 border-primary-100">
                    <tr>
                        <th class="px-6 py-4 w-24">Gambar</th>
                        <th class="px-6 py-4">Nama Produk & Varian</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga Dasar</th>
                        <th class="px-6 py-4 text-center">Stok</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-primary-50/10 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-1.5 shrink-0 overflow-hidden">
                                @if($product->getFirstImage())
                                    <img loading="lazy" src="{{ $product->getFirstImage() }}" alt="{{ $product->name }}" class="w-full h-full object-contain rounded transition-transform duration-300 group-hover:scale-110">
                                @else
                                    <i class="ph ph-image ph-fill w-6 h-6 text-gray-400"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="font-bold text-gray-900 group-hover:text-primary-600 transition-colors">{{ $product->name }}</div>
                                @if($product->hasDiscount())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 uppercase tracking-tighter">
                                        <i class="ph-fill ph-tag mr-0.5"></i> Promo
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5"><i class="ph ph-tag w-3 h-3 text-gray-400"></i> SKU: {{ $product->sku }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->category)
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-md bg-green-50 text-green-700 border border-green-200">{{ $product->category->name }}</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-md bg-gray-50 text-gray-700 border border-gray-200">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                @if($product->hasDiscount())
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-gray-900 font-bold text-sm">{{ $product->getFormattedPrice() }}</span>
                                        <span class="text-[10px] bg-red-100 text-red-600 px-1 rounded font-bold">-{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%</span>
                                    </div>
                                    <span class="text-[11px] text-gray-400 line-through decoration-red-400/50 decoration-1">{{ $product->getFormattedOriginalPrice() }}</span>
                                @else
                                    <span class="text-gray-900 font-bold">{{ $product->getFormattedPrice() }}</span>
                                @endif
                                <span class="text-gray-500 font-normal text-[10px] uppercase mt-0.5 tracking-wider">/{{ $product->unit ?? 'unit' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->stock < 10)
                                <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-red-50 text-red-700 border border-red-200">{{ $product->stock }}</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-primary-50 text-primary-700 border border-primary-200">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.produk.show', $product) }}" class="icon-button text-gray-500 hover:text-blue-600 hover:bg-blue-50 bg-white border border-gray-200 rounded-lg shadow-sm transition-colors" title="Lihat Detail">
                                    <i class="ph ph-eye ph-bold w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.produk.edit', $product) }}" class="icon-button text-gray-500 hover:text-amber-600 hover:bg-amber-50 bg-white border border-gray-200 rounded-lg shadow-sm transition-colors" title="Edit">
                                    <i class="ph ph-pencil-simple ph-bold w-4 h-4"></i>
                                </a>
                                {{-- Moved --}}

                                <button type="button" 
                                        @click="$dispatch('confirm-action', { 
                                            title: 'Hapus Produk?', 
                                            message: 'Apakah Anda yakin ingin menghapus produk <strong>{{ $product->name }}</strong>? Tindakan ini tidak dapat dibatalkan.', 
                                            confirmText: 'Ya, Hapus', 
                                            action: () => document.getElementById('delete-product-{{ $product->id }}').submit() 
                                        })"
                                        class="icon-button text-gray-500 hover:text-red-600 hover:bg-red-50 bg-white border border-gray-200 rounded-lg shadow-sm transition-colors" title="Hapus">
                                    <i class="ph ph-trash ph-bold w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="ph ph-package w-12 h-12 text-gray-300"></i>
                                <p>Tidak ada produk ditemukan</p>
                                <a href="{{ route('admin.produk.index') }}" class="text-primary-600 hover:underline text-sm">Reset filter</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Flowbite Pagination -->
        <div class="bg-gradient-to-r from-primary-50/30 to-primary-50/10 border-t border-primary-100 p-5 flex w-full">
            {{ $products->links('vendor.pagination.custom') }}
        </div>
        
    </div>
    </form>

    {{-- Hidden Delete Forms --}}
    <div class="hidden">
        @foreach($products as $product)
            <form id="delete-product-{{ $product->id }}" action="{{ route('admin.produk.destroy', $product) }}" method="POST">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</div>

<style>
    /* Force hide default browser arrow for select elements with appearance-none */
    select.appearance-none {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background-image: none !important;
    }

    /* Hide arrow in IE/Edge */
    select.appearance-none::-ms-expand {
        display: none !important;
    }

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
