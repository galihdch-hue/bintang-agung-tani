@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

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
                            <span class="text-gray-900 font-medium">Tambah Baru</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Tambah Produk Baru</h1>
            <p class="text-gray-500 mt-1 text-sm">Masukkan detail produk lengkap untuk ditampilkan di katalog.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/produk" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
            <button type="submit" form="create-product-form" class="btn-primary text-sm h-10 shadow-md">
                <i class="ph ph-floppy-disk ph-bold w-4 h-4"></i> Simpan Produk
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <form id="create-product-form" action="{{ route('admin.produk.store') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-3 gap-6" enctype="multipart/form-data">
        @csrf
        
        <!-- Kolom Kiri: Informasi Utama & Harga -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Informasi Umum -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <i class="ph ph-info ph-fill w-5 h-5 text-primary-600"></i>
                    <h2 class="text-lg font-bold text-gray-900">Informasi Umum</h2>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label for="name" class="form-label mb-1.5 block">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" class="form-input w-full" value="{{ old('name') }}" placeholder="Misal: Pupuk NPK Phonska 15-15-15" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="category_id" class="form-label mb-1.5 block">Kategori <span class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id" class="form-input w-full bg-gray-50/50" required>
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            <input type="text" id="brand" name="brand" class="form-input w-full" value="{{ old('brand') }}" placeholder="Misal: Petrokimia Gresik">
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="form-label mb-1.5 block">Deskripsi Produk <span class="text-red-500">*</span></label>
                        <!-- Fake Rich Text Editor Toolbar -->
                        <div class="w-full mb-1 border border-gray-200 rounded-t-xl bg-gray-50 flex items-center p-2 gap-1 overflow-x-auto">
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-b ph-bold w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-italic w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-underline w-4 h-4"></i></button>
                            <div class="w-px h-4 bg-gray-300 mx-1"></div>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-bullets w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-numbers w-4 h-4"></i></button>
                        </div>
                        <textarea id="description" name="description" rows="6" class="form-input w-full rounded-t-none resize-y" placeholder="Tuliskan keterangan lengkap produk, manfaat, dan cara penggunaan..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">Pastikan deskripsi menarik dan menjelaskan detail spesifik produk.</p>
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
                                value="{{ old('price') }}"
                                placeholder="0"
                                required>
                            <input type="hidden" id="price" name="price" value="{{ old('price') }}">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Diskon -->
                    <div class="md:col-span-1">
                        <label for="discount_price_display" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Diskon (Rp) <span class="text-gray-400 text-xs font-normal ml-1">(Opsional)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-semibold text-sm">Rp</span>
                            </div>
                            <input type="text" id="discount_price_display"
                                class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 text-sm"
                                value="{{ old('discount_price') }}"
                                placeholder="0">
                            <input type="hidden" id="discount_price" name="discount_price" value="{{ old('discount_price') }}">
                        </div>
                        @error('discount_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-slate-500 flex items-start gap-1.5 mt-2 bg-slate-50 p-2 rounded-lg border border-slate-100">
                            <i class="ph-fill ph-info text-indigo-500 mt-0.5"></i>
                            <span>Jika diisi, harga ini akan muncul sebagai <strong>harga promo</strong> dan harga normal akan <strong>dicoret</strong> di halaman depan.</span>
                        </p>
                    </div>

                    <div class="md:col-span-1">
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Awal <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stock" name="stock"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 text-sm"
                            style="-moz-appearance: textfield;"
                            value="{{ old('stock') }}"
                            min="0"
                            required>
                        @error('stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                            Berat/Volume (Pengiriman) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <input type="number" id="weight" name="weight" step="0.01"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 text-sm"
                                style="-moz-appearance: textfield;"
                                value="{{ old('weight') }}"
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
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Satuan Jual <span class="text-red-500">*</span>
                        </label>
                        <select id="unit" name="unit"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white text-gray-900 text-sm"
                            required>
                            <option value="zak" {{ old('unit') == 'zak' ? 'selected' : '' }}>Zak / Karung</option>
                            <option value="botol" {{ old('unit') == 'botol' ? 'selected' : '' }}>Botol</option>
                            <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs / Buah</option>
                            <option value="paket" {{ old('unit') == 'paket' ? 'selected' : '' }}>Paket</option>
                        </select>
                        @error('unit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
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
                
                <label for="product_images" class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center bg-gray-50 hover:bg-primary-50 hover:border-primary-300 group cursor-pointer transition-all block">
                    <div class="bg-white w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md group-hover:scale-110 transition-transform">
                        <i class="ph ph-upload-simple ph-bold w-6 h-6 text-primary-600"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-700 mb-1 group-hover:text-primary-700">Klik untuk unggah maksimal 5 foto</p>
                    <p class="text-xs text-gray-500">Pilih beberapa gambar sekaligus untuk galeri produk</p>
                    <div class="mt-4 flex items-center justify-center">
                        <span class="text-[10px] font-medium tracking-wide text-gray-400 uppercase bg-gray-200/50 px-2 py-1 rounded">PNG, JPG, WEBP, Max 5 file × 5MB</span>
                    </div>
                    <input type="file" id="product_images" name="product_images[]" accept="image/*" multiple class="hidden">
                </label>

                <div id="image-preview-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 hidden"></div>
                <p id="image-count-info" class="text-xs text-gray-500 mt-3 text-center">Belum ada gambar dipilih</p>
                @error('product_images')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
                @error('product_images.*')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Status Produk -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-4">
                    <i class="ph ph-toggle-right ph-fill w-5 h-5 text-[#10b981]"></i>
                    <h2 class="text-lg font-bold text-gray-900">Visibilitas Produk</h2>
                </div>
                
                <label class="inline-flex items-center cursor-pointer bg-gray-50 border border-gray-200 p-4 rounded-xl w-full">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    <div class="ms-4">
                        <span class="block text-sm font-bold text-gray-900">Aktifkan Produk</span>
                        <span class="block text-xs text-gray-500">Produk akan tampil di katalog publik</span>
                    </div>
                </label>
            </div>
            
        </div>
    </form>

    <style>
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>

    <script>
        const priceDisplay = document.getElementById('price_display');
        const priceHidden = document.getElementById('price');
        const discountPriceDisplay = document.getElementById('discount_price_display');
        const discountPriceHidden = document.getElementById('discount_price');

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

            priceDisplay.addEventListener('input', function() {
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

        // Logic for Discount Price
        if (discountPriceDisplay) {
            let initialValue = discountPriceDisplay.value;
            if (initialValue) {
                discountPriceDisplay.value = formatNumber(unformatNumber(initialValue));
            }

            discountPriceDisplay.addEventListener('input', function() {
                let cursorPos = this.selectionStart;
                let oldLength = this.value.length;
                let value = this.value.replace(/[^\d]/g, '');

                if (value) {
                    this.value = formatNumber(value);
                    discountPriceHidden.value = value;
                    let newLength = this.value.length;
                    cursorPos += (newLength - oldLength);
                    this.setSelectionRange(cursorPos, cursorPos);
                } else {
                    this.value = '';
                    discountPriceHidden.value = '';
                }
            });

            discountPriceDisplay.addEventListener('keydown', function(e) {
                if ([8, 46, 9, 27, 13].includes(e.keyCode) ||
                    (e.ctrlKey === true && [65, 67, 86, 88].includes(e.keyCode)) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            discountPriceDisplay.addEventListener('blur', function() {
                let value = this.value.replace(/[^\d]/g, '');
                if (value) {
                    this.value = formatNumber(value);
                }
            });
        }

        const imageInput = document.getElementById('product_images');
        const previewGrid = document.getElementById('image-preview-grid');
        const imageCountInfo = document.getElementById('image-count-info');

        if (imageInput && previewGrid && imageCountInfo) {
            imageInput.addEventListener('change', function() {
                previewGrid.innerHTML = '';

                const files = Array.from(this.files || []);

                if (files.length === 0) {
                    previewGrid.classList.add('hidden');
                    imageCountInfo.textContent = 'Belum ada gambar dipilih';
                    imageCountInfo.className = 'text-xs text-gray-500 mt-3 text-center';
                    return;
                }

                if (files.length > 5) {
                    alert('Maksimal 5 gambar. Silakan pilih ulang.');
                    this.value = '';
                    previewGrid.classList.add('hidden');
                    imageCountInfo.textContent = 'Belum ada gambar dipilih';
                    imageCountInfo.className = 'text-xs text-gray-500 mt-3 text-center';
                    return;
                }

                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const item = document.createElement('div');
                        item.className = 'relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-50';
                        item.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-full object-cover">
                            <div class="absolute top-2 left-2 px-2 py-1 rounded bg-black/70 text-white text-[10px] font-bold">${index === 0 ? 'Utama' : index + 1}</div>
                        `;
                        previewGrid.appendChild(item);
                    };
                    reader.readAsDataURL(file);
                });

                previewGrid.classList.remove('hidden');
                imageCountInfo.textContent = `${files.length} gambar dipilih. Gambar pertama akan menjadi gambar utama.`;
                imageCountInfo.className = 'text-xs text-primary-600 mt-3 text-center font-medium';
            });
        }
    </script>
</div>
@endsection
