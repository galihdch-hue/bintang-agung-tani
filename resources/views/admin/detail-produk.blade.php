@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <a href="/admin/produk" class="hover:text-primary-600 transition-colors">Kelola Produk</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <span class="text-gray-900 font-medium">Detail Produk</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Detail Produk</h1>
            <p class="text-gray-500 mt-1 text-sm">Lihat informasi lengkap dan performa penjualan produk.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.produk.index') }}" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
            <a href="{{ route('admin.produk.edit', $product) }}" class="btn-primary text-sm h-10 shadow-md">
                <i class="ph ph-pencil-simple ph-bold w-4 h-4"></i> Edit Produk
            </a>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Media Gallery -->
        <div class="lg:col-span-4 space-y-4 sticky top-24">
            @php
                $galleryImages = $product->getImages();
                if (empty($galleryImages) && $product->getFirstImage()) {
                    $galleryImages = [$product->getFirstImage()];
                }
                $mainImage = $galleryImages[0] ?? asset('images/no-product.jpg');
            @endphp

            <!-- Main Image -->
            <div class="card p-2 bg-white flex items-center justify-center aspect-square overflow-hidden group">
                <img loading="lazy" id="admin-main-product-image" src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-contain rounded-xl transition-transform duration-500 group-hover:scale-110">
            </div>

            <!-- Thumbnails -->
            @if(!empty($galleryImages))
            <div class="grid grid-cols-4 gap-3">
                @foreach($galleryImages as $index => $image)
                <button type="button" onclick="document.getElementById('admin-main-product-image').src='{{ $image }}'" class="{{ $index === 0 ? 'border-2 border-primary-500' : 'border-2 border-transparent hover:border-primary-300' }} rounded-lg overflow-hidden cursor-pointer shadow-sm relative transition-colors">
                    <img loading="lazy" src="{{ $image }}" alt="Thumb {{ $index + 1 }}" class="w-full h-full object-contain aspect-square {{ $index !== 0 ? 'opacity-70 hover:opacity-100' : '' }}">
                </button>
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Right Column: Product Detail & Stats -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Product Identity Card -->
            <div class="card p-6 border-t-4 border-t-primary-500 rounded-t-xl">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full {{ $product->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }} shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-green-500' : 'bg-red-500' }} mr-1.5 inline-block"></span> {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-600 border border-gray-200">
                                SKU: {{ $product->sku ?? 'N/A' }}
                            </span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-3 text-sm text-gray-500 font-medium">
                            <span class="flex items-center gap-1.5"><i class="ph ph-folder-notch w-4.5 h-4.5 text-gray-400"></i> Kategori: <span class="text-primary-600">{{ $product->category->name ?? 'N/A' }}</span></span>
                            <span class="flex items-center gap-1.5"><i class="ph ph-buildings w-4.5 h-4.5 text-gray-400"></i> Merek: <span class="text-gray-800">{{ $product->brand ?? 'N/A' }}</span></span>
                        </div>
                    </div>
                    
                    <div class="text-left sm:text-right shrink-0 bg-amber-50 rounded-xl p-4 border border-amber-100 min-w-[160px]">
                        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Harga Jual</p>
                        @if($product->hasDiscount())
                            <p class="text-sm font-bold text-gray-400 line-through decoration-red-400/50">Rp {{ number_format($product->getOriginalPrice(), 0, ',', '.') }}</p>
                            <p class="text-3xl font-black text-amber-500">Rp {{ number_format($product->getCurrentPrice(), 0, ',', '.') }}</p>
                        @else
                            <p class="text-3xl font-black text-amber-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        @endif
                        <p class="text-xs text-amber-700/80 font-medium text-right mt-1">/ {{ $product->unit ?? 'Unit' }}</p>
                    </div>
                </div>
                
                <!-- Quick Stats Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-5 border-y border-gray-100 mb-6 bg-gray-50/50 -mx-6 px-6">
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1 flex items-center gap-1"><i class="ph ph-package w-4 h-4 text-primary-500"></i> Stok Tersedia</p>
                        <p class="font-bold text-gray-900 text-xl">{{ $product->stock }} <span class="text-sm font-medium text-gray-500">{{ $product->unit ?? 'Unit' }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1 flex items-center gap-1"><i class="ph ph-shopping-cart-simple w-4 h-4 text-blue-500"></i> Total Terjual</p>
                        <p class="font-bold text-gray-900 text-xl">{{ $product->total_sold ?? 0 }} <span class="text-sm font-medium text-gray-500">{{ $product->unit ?? 'Unit' }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1 flex items-center gap-1"><i class="ph ph-scales w-4 h-4 text-amber-500"></i> Berat Aktual</p>
                        <p class="font-bold text-gray-900 text-xl">{{ $product->weight ?? 0 }} <span class="text-sm font-medium text-gray-500">{{ $product->weight_unit ?? 'Kg' }}/{{ $product->unit ?? 'Unit' }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1 flex items-center gap-1"><i class="ph ph-calendar-plus w-4 h-4 text-purple-500"></i> Tgl. Ditambahkan</p>
                        <p class="font-bold text-gray-900 text-base mt-1">{{ $product->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                
                <!-- Full Description -->
                <div>
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="ph ph-article ph-fill w-5 h-5 text-gray-400"></i> Deskripsi Lengkap
                    </h3>
                    <div class="text-gray-600 space-y-3 text-sm leading-relaxed bg-gray-50/50 p-5 rounded-xl border border-gray-100">
                        {!! nl2br(e($product->description ?? 'Tidak ada deskripsi untuk produk ini.')) !!}
                    </div>
                </div>
            </div>

            <!-- Stok & Log Activities Card -->
            <div class="card p-6">
                <div class="flex items-center justify-between border-b border-gray-100 mb-4 pb-4">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-clock-counter-clockwise ph-fill w-5 h-5 text-gray-400"></i> Aktivitas Stok Terakhir
                    </h3>
                    <a href="/admin/stok" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                        Kelola Stok
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($stockLogs as $log)
                    <div class="flex items-start gap-3">
                        @php
                            $isPositive = $log->quantity > 0;
                            $color = $isPositive ? 'green' : 'blue';
                            $icon = $isPositive ? 'trend-up' : 'shopping-cart-simple';
                            $actionText = $isPositive ? 'ditambahkan' : 'terjual';
                            $reference = $log->order ? '(INV-'.$log->order->order_number.')' : ($log->reason ?: '');
                        @endphp
                        <div class="bg-{{ $color }}-50 rounded-full p-2 shrink-0 border border-{{ $color }}-100 mt-0.5">
                            <i class="ph ph-{{ $icon }} ph-bold w-4 h-4 text-{{ $color }}-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                <span class="font-bold {{ $isPositive ? 'text-green-600' : 'text-blue-600' }}">{{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }} {{ $product->unit ?? 'Unit' }}</span> 
                                {{ $actionText }} {{ $reference }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Oleh: {{ $log->createdBy->name ?? ($log->order ? 'Sistem' : 'Admin') }} · {{ $log->created_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <i class="ph ph-clock-counter-clockwise w-12 h-12 mx-auto mb-2"></i>
                        <p class="text-sm">Belum ada aktivitas stok untuk produk ini</p>
                    </div>
                    @endforelse
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.stock.show', $product) }}" class="btn-primary w-full shadow-sm justify-center flex items-center gap-2">
                        <i class="ph ph-plus ph-bold w-4 h-4"></i> Kelola Stok Produk
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
