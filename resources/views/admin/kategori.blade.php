@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0" x-data="{ showDeleteModal: false, showAddModal: false, showEditModal: false, deleteTarget: '', editTarget: '' }">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Kelola Kategori</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Kelola Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori produk yang tersedia di katalog toko.</p>
        </div>
        <button @click="showAddModal = true" class="btn-primary text-sm self-end h-10 shadow-md">
            <i class="ph ph-plus ph-bold w-4 h-4"></i> Tambah Kategori
        </button>
    </div>

    <!-- Category Stats (Icon Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($categories->take(4) as $category)
        <div class="card p-5 group flex flex-col justify-between hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white transition-colors duration-300">
                    <i class="ph ph-plant ph-duotone w-6 h-6"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $category->products_count ?? $category->products->count() ?? 0 }}</h3>
                <p class="text-sm font-medium text-gray-500">{{ $category->name }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-4 card p-8 text-center">
            <p class="text-gray-500">Belum ada kategori</p>
        </div>
        @endforelse
    </div>

    <!-- Main Container Card -->
    <div class="card p-0 overflow-hidden w-full">

        <!-- Top Action Bar -->
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center bg-white">
            <div>
                <h2 class="text-lg font-bold text-gray-900 tracking-tight">Daftar Kategori Induk</h2>
                <p class="text-xs text-gray-500 mt-1">{{ $categories->total() }} kategori tersedia</p>
            </div>
            <div class="relative w-full sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                </div>
                <input type="text" placeholder="Cari kategori..." 
                       class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[600px]">
                <thead class="bg-gray-50/80 text-gray-600 text-xs uppercase font-bold tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Jumlah Produk</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($categories as $category)
                    <tr class="hover:bg-primary-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 border border-primary-100 flex items-center justify-center shrink-0">
                                    <i class="ph {{ $category->icon ?? 'ph-tag' }} ph-duotone w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 block text-base leading-tight">{{ $category->name }}</span>
                                    <span class="text-xs text-gray-500 mt-0.5 block">{{ $category->description ?? 'Kategori produk' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($category->is_active)
                                <span class="badge-success">Aktif</span>
                            @else
                                <span class="badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-base">{{ $category->products_count ?? 0 }}</span>
                                <span class="text-xs text-gray-500">Item</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ url('/admin/kategori/' . $category->id . '/edit') }}" class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-100 border-b-2 hover:bg-blue-100 rounded-lg transition-colors flex items-center gap-1.5 focus:ring-2 focus:ring-blue-500/20 active:border-b active:translate-y-px">
                                    <i class="ph ph-pencil-simple ph-bold w-3.5 h-3.5"></i> Edit
                                </a>
                                <form id="delete-form-{{ $category->id }}" action="{{ url('/admin/kategori/' . $category->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" 
                                        @click="$dispatch('confirm-action', { 
                                            title: 'Hapus Kategori?', 
                                            message: 'Apakah Anda yakin ingin menghapus kategori <strong>{{ $category->name }}</strong>? Produk yang terhubung ke kategori ini akan kehilangan referensi kategorinya.', 
                                            confirmText: 'Ya, Hapus', 
                                            action: () => document.getElementById('delete-form-{{ $category->id }}').submit() 
                                        })"
                                        class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-100 border-b-2 hover:bg-red-100 rounded-lg transition-colors flex items-center gap-1.5 focus:ring-2 focus:ring-red-500/20 active:border-b active:translate-y-px">
                                    <i class="ph ph-trash ph-bold w-3.5 h-3.5"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada kategori yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 border-t border-gray-100 flex items-center justify-between text-sm bg-white">
            <div class="w-full">
                {{ $categories->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div x-show="showAddModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-300 delay-75" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-8 scale-95">
            
            <form action="{{ url('/admin/kategori') }}" method="POST">
                @csrf
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Kategori Baru</h3>
                    <button type="button" @click="showAddModal = false" class="text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <i class="ph ph-x ph-bold w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-5">
                    <div>
                        <label class="form-label block mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="name" placeholder="Contoh: Herbisida" class="form-input w-full" required>
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1.5">Ikon Kategori</label>
                        <input type="text" name="icon" placeholder="Contoh: ph-tag" class="form-input w-full">
                    </div>
                    
                    <div>
                        <label class="form-label block mb-1.5">Deskripsi Singkat <span class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                        <textarea name="description" rows="3" placeholder="Tuliskan keterangan untuk kategori ini..." class="form-input w-full resize-none"></textarea>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showAddModal = false" class="btn-secondary text-sm">Batal</button>
                    <button type="submit" class="btn-primary shadow-md text-sm">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection