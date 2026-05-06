@extends('layouts.admin')

@section('title', 'Edit Produk')

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
                            <span class="text-gray-900 font-medium">Edit Produk</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Edit Produk</h1>
            <p class="text-gray-500 mt-1 text-sm">Perbarui informasi, harga, dan ketersediaan produk.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/produk" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Batal
            </a>
            <button type="submit" form="edit-product-form" class="btn-primary text-sm h-10 shadow-md">
                <i class="ph ph-floppy-disk ph-bold w-4 h-4"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <form id="edit-product-form" action="{{ route('admin.produk.update', $product) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        @csrf
        @method('PUT')
        
        <!-- Kolom Kiri: Informasi Utama & Harga -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Informasi Umum -->
            <div class="card p-6 border-t-4 border-t-primary-500 rounded-t-xl">
                <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-info ph-fill w-5 h-5 text-primary-600"></i>
                        <h2 class="text-lg font-bold text-gray-900">Informasi Umum</h2>
                    </div>
                    <span class="text-xs font-mono text-gray-400 bg-gray-50 px-2 py-1 flex items-center gap-1 border border-gray-200 rounded">
                        <i class="ph ph-barcode w-3.5 h-3.5"></i> SKU: {{ $product->sku }}
                    </span>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label for="name" class="form-label mb-1.5 block">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" class="form-input w-full" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="category_id" class="form-label mb-1.5 block">Kategori <span class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id" class="form-input w-full bg-gray-50/50" required>
                                <option value="" disabled>Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="brand" class="form-label mb-1.5 block">Merek / Brand <span class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                            <input type="text" id="brand" name="brand" class="form-input w-full" value="{{ old('brand', $product->brand ?? '') }}">
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="form-label mb-1.5 block">Deskripsi Produk <span class="text-red-500">*</span></label>
                        <div class="w-full mb-1 border border-gray-200 rounded-t-xl bg-gray-50 flex items-center p-2 gap-1 overflow-x-auto">
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors bg-white shadow-sm border border-gray-200"><i class="ph ph-text-b ph-bold w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-italic w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-underline w-4 h-4"></i></button>
                            <div class="w-px h-4 bg-gray-300 mx-1"></div>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-bullets w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-numbers w-4 h-4"></i></button>
                        </div>
                        <textarea id="description" name="description" rows="6" class="form-input w-full rounded-t-none resize-y" required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Harga & Inventaris -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <i class="ph ph-currency-circle-dollar ph-fill w-5 h-5 text-amber-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Harga & Inventaris</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <!-- Harga -->
                    <div class="md:col-span-1">
                        <label for="price_display" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Jual (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-semibold text-sm">Rp</span>
                            </div>
                            <input type="text" id="price_display"
                                class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 text-sm"
                                value="{{ old('price', $product->price > 0 ? number_format($product->price, 0, ',', '.') : '') }}"
                                placeholder="0"
                                required>
                            <input type="hidden" id="price" name="price" value="{{ old('price', $product->price) }}">
                        </div>

                        @error('price')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok -->
                    <div class="md:col-span-1">
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Tersedia <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stock" name="stock"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 text-sm"
                            style="-moz-appearance: textfield;"
                            value="{{ old('stock', $product->stock) }}"
                            min="0"
                            required>

                        @error('stock')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Berat -->
                    <div class="md:col-span-1">
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                            Berat/Volume (Pengiriman) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <input type="number" id="weight" name="weight" step="0.01"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 text-sm"
                                style="-moz-appearance: textfield;"
                                value="{{ old('weight', $product->weight) }}"
                                min="0"
                                required>

                            <select name="weight_unit"
                                class="px-3 py-2.5 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 text-gray-700 text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="kg" {{ old('weight_unit', 'kg') == 'kg' ? 'selected' : '' }}>Kg</option>
                                <option value="g" {{ old('weight_unit') == 'g' ? 'selected' : '' }}>Gram</option>
                                <option value="l" {{ old('weight_unit') == 'l' ? 'selected' : '' }}>Liter</option>
                                <option value="ml" {{ old('weight_unit') == 'ml' ? 'selected' : '' }}>Ml</option>
                            </select>
                        </div>

                        @error('weight')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan -->
                    <div class="md:col-span-1">
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Satuan Jual <span class="text-red-500">*</span>
                        </label>

                        <select id="unit" name="unit"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white text-gray-900 text-sm"
                            required>
                            <option value="zak" {{ old('unit', $product->unit) == 'zak' ? 'selected' : '' }}>Zak / Karung</option>
                            <option value="botol" {{ old('unit', $product->unit) == 'botol' ? 'selected' : '' }}>Botol</option>
                            <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>Pcs / Buah</option>
                            <option value="paket" {{ old('unit', $product->unit) == 'paket' ? 'selected' : '' }}>Paket</option>
                        </select>

                        @error('unit')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Kolom Kanan: Media & Setting -->
        <div class="space-y-6">
            
            <!-- Media Produk -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-4">
                    <i class="ph ph-image ph-fill w-5 h-5 text-blue-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Media Produk</h2>
                </div>
                
                <!-- Gambar Saat Ini -->
                <div class="mb-4 text-center">
                    <div class="relative w-full aspect-square rounded-2xl bg-gray-50 border border-gray-200 overflow-hidden group">
                        @if($product->getFirstImage())
                            <img loading="lazy" src="{{ $product->getFirstImage() }}" alt="{{ $product->name }}" class="w-full h-full object-cover mix-blend-multiply transition-transform group-hover:scale-105">
                            <button type="button" 
                                    @click="$dispatch('confirm-action', { 
                                        title: 'Hapus Gambar Produk?', 
                                        message: 'Apakah Anda yakin ingin menghapus gambar produk ini?', 
                                        confirmText: 'Ya, Hapus Gambar', 
                                        action: () => document.getElementById('delete-image-form-{{ $product->id }}').submit() 
                                    })"
                                    class="absolute top-2 right-2 bg-white text-red-600 p-2 rounded-lg shadow-sm hover:bg-red-50 hover:text-red-700 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" title="Hapus Gambar">
                                <i class="ph ph-trash ph-bold w-4 h-4"></i>
                            </button>
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ph ph-image ph-fill w-16 h-16 text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="w-full space-y-3">
                    <label for="product_images" class="btn-secondary w-full justify-center text-sm shadow-sm flex items-center gap-2 cursor-pointer">
                        <i class="ph ph-upload-simple ph-bold w-4 h-4"></i> Tambah / Ganti Gambar Produk
                    </label>
                    <input type="file" id="product_images" name="product_images[]" accept="image/*" multiple class="hidden">
                    <p id="selected-image-name" class="text-xs text-gray-500 text-center">Belum ada gambar baru dipilih</p>
                    <p class="text-xs text-amber-600 text-center">Maksimal total 5 gambar. Gambar pertama menjadi gambar utama setelah disimpan.</p>

                    @php
                        $existingImages = $product->getImages();
                        if (empty($existingImages) && $product->featured_image) {
                            $existingImages = [$product->featured_image];
                        }
                    @endphp

                    @if(!empty($existingImages))
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-3">
                            @foreach($existingImages as $index => $image)
                                <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                    <img src="{{ $image }}" alt="Gambar {{ $index + 1 }}" class="w-full h-full object-cover">
                                    <div class="absolute top-2 left-2 px-2 py-1 rounded bg-black/70 text-white text-[10px] font-bold">{{ $index === 0 ? 'Utama' : $index + 1 }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div id="image-preview-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-3 hidden"></div>
                </div>
            </div>
            
            <!-- Status Produk -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-4">
                    <i class="ph ph-toggle-right ph-fill w-5 h-5 text-primary-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Visibilitas Produk</h2>
                </div>
                
                <label class="inline-flex items-center cursor-pointer bg-primary-50 border border-primary-200 p-4 rounded-xl w-full hover:bg-primary-100/50 transition-colors">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    <div class="ms-4">
                        <span class="block text-sm font-bold text-gray-900">Aktifkan Produk</span>
                        <span class="block text-xs text-primary-700 mt-0.5 font-medium">{{ $product->is_active ? 'Sedang tampil di katalog' : 'Tidak tampil di katalog' }}</span>
                    </div>
                </label>

                <!-- Warning untuk Delete -->
                <div class="mt-6 border-t border-red-500/20 pt-4 text-center">
                    <button type="button" 
                            @click="$dispatch('confirm-action', { 
                                title: 'Hapus Produk Permanen?', 
                                message: 'Apakah Anda yakin ingin menghapus produk <strong>{{ $product->name }}</strong> secara permanen? Tindakan ini tidak dapat dibatalkan.', 
                                confirmText: 'Ya, Hapus Permanen', 
                                action: () => document.getElementById('delete-product-form-{{ $product->id }}').submit() 
                            })"
                            class="text-xs font-bold text-red-600 hover:text-red-700 hover:underline">
                        Hapus Permanen Produk Ini
                    </button>
                </div>
            </div>
            
        </div>
    </form>

    <form id="delete-image-form-{{ $product->id }}" action="{{ route('admin.produk.image.delete', $product) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <form id="delete-product-form-{{ $product->id }}" action="{{ route('admin.produk.destroy', $product) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <style>
        /* Hilangkan spinner di Chrome/Safari/Edge */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Hilangkan spinner di Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>

    <script>
    // Format harga dengan pemisah ribuan
    const priceDisplay = document.getElementById('price_display');
    const priceHidden = document.getElementById('price');

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatNumber(str) {
        return str.replace(/\./g, '');
    }

    if (priceDisplay) {
        let initialValue = priceDisplay.value;
        if (initialValue) {
            priceDisplay.value = formatNumber(unformatNumber(initialValue));
        }

        priceDisplay.addEventListener('input', function(e) {
            let cursorPos = this.selectionStart;
            let oldLength = this.value.length;
            let value = this.value.replace(/[^\d]/g, '');

            if (value) {
                this.value = formatNumber(value);
                priceHidden.value = value;
                let newLength = this.value.length;
                cursorPos += (newLength - oldLength);
                this.setSelectionRange(cursorPos, cursorPos);
            } else {
                this.value = '';
                priceHidden.value = '';
            }
        });

        priceDisplay.addEventListener('keydown', function(e) {
            if ([8, 46, 9, 27, 13].includes(e.keyCode) ||
                (e.ctrlKey === true && [65, 67, 86, 88].includes(e.keyCode)) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        priceDisplay.addEventListener('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = formatNumber(value);
            }
        });
    }

    const productImagesInput = document.getElementById('product_images');
    const selectedImageName = document.getElementById('selected-image-name');
    const previewGrid = document.getElementById('image-preview-grid');

    if (productImagesInput && selectedImageName && previewGrid) {
        productImagesInput.addEventListener('change', function() {
            previewGrid.innerHTML = '';
            const files = Array.from(this.files || []);

            if (files.length === 0) {
                selectedImageName.textContent = 'Belum ada gambar baru dipilih';
                selectedImageName.className = 'text-xs text-gray-500 text-center';
                previewGrid.classList.add('hidden');
                return;
            }

            if (files.length > 5) {
                alert('Maksimal pilih 5 gambar.');
                this.value = '';
                selectedImageName.textContent = 'Belum ada gambar baru dipilih';
                selectedImageName.className = 'text-xs text-gray-500 text-center';
                previewGrid.classList.add('hidden');
                return;
            }

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-50';
                    item.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-full object-cover">
                        <div class="absolute top-2 left-2 px-2 py-1 rounded bg-black/70 text-white text-[10px] font-bold">Baru ${index + 1}</div>
                    `;
                    previewGrid.appendChild(item);
                };
                reader.readAsDataURL(file);
            });

            selectedImageName.textContent = `${files.length} gambar baru dipilih`;
            selectedImageName.className = 'text-xs text-green-600 text-center font-medium';
            previewGrid.classList.remove('hidden');
        });
    }


    </script>
@endsection