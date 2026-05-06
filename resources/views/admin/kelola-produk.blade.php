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
    <div class="card p-4 sm:p-5 flex flex-col sm:flex-row gap-4 justify-between items-center w-full">
        <div class="relative w-full sm:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
            </div>
            <input type="text" placeholder="Cari nama produk, SKU, kategori..." 
                   class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
        </div>
        
        <a href="{{ route('admin.produk.create') }}" class="btn-primary w-full sm:w-auto text-sm justify-center shadow-md">
            <i class="ph ph-plus ph-bold w-4 h-4"></i> Tambah Produk
        </a>
    </div>

    <!-- Main Container Card -->
    <div class="card overflow-hidden border-primary-100">

        <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
            <i class="ph ph-package w-5 h-5 text-primary-600 ph-fill"></i>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Daftar Produk Aktif</h2>
        </div>

        <!-- Table Toolbar -->
        <div class="px-6 py-3 border-b border-primary-100 flex flex-wrap gap-4 items-center justify-between bg-white">
            <div class="flex items-center gap-2">
                <button class="text-gray-500 hover:text-primary-600 bg-gray-50 hover:bg-primary-50 px-3 py-1.5 rounded-lg border border-gray-200 hover:border-primary-200 transition-colors text-sm font-medium flex items-center gap-1.5">
                    <i class="ph ph-funnel w-4 h-4"></i> Semua Kategori
                </button>
                <div class="h-5 w-px bg-gray-200 mx-1"></div>
                <button class="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors border border-transparent hover:border-primary-100" title="Refresh Data">
                    <i class="ph ph-arrows-clockwise w-5 h-5"></i>
                </button>
            </div>
            <span class="text-xs text-gray-500 font-medium">Menampilkan {{ $products->firstItem() ?? 1 }}-{{ $products->lastItem() ?? $products->count() }} dari {{ $products->total() }} produk</span>
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
                            <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-1.5 shrink-0">
                                @if($product->getFirstImage())
                                    <img loading="lazy" src="{{ $product->getFirstImage() }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded mix-blend-multiply">
                                @else
                                    <i class="ph ph-image ph-fill w-6 h-6 text-gray-400"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 group-hover:text-primary-600 transition-colors">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5"><i class="ph ph-tag w-3 h-3 text-gray-400"></i> SKU: {{ $product->sku }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->category)
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-md bg-green-50 text-green-700 border border-green-200">{{ $product->category->name }}</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-md bg-gray-50 text-gray-700 border border-gray-200">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-900 font-bold">{{ $product->getFormattedPrice() }}<span class="text-gray-500 font-normal text-xs">/{{ $product->unit ?? 'unit' }}</span></td>
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
                                <form id="delete-product-{{ $product->id }}" action="{{ route('admin.produk.destroy', $product) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
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
        <div class="bg-gradient-to-r from-primary-50/30 to-primary-50/10 border-t border-primary-100 p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-sm text-gray-500 font-medium">Menampilkan <span class="text-gray-900 font-bold">{{ $products->firstItem() ?? 1 }}-{{ $products->lastItem() ?? $products->count() }}</span> dari <span class="text-gray-900 font-bold">{{ $products->total() }}</span> Produk</span>
            <nav aria-label="Page navigation" class="mx-auto sm:mx-0">
                {{ $products->links() }}
            </nav>
        </div>
        
    </div>

</div>
@endsection
