@extends('layouts.admin')

@section('title', 'Kelola User')

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
                                <span class="text-gray-900 font-medium">Kelola User</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Kelola User</h1>
                <p class="text-gray-500 mt-1 text-sm">Kelola data pengguna dan memberikan akses ke sistem.</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-start gap-3">
                <i class="ph ph-check-circle w-5 h-5 mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3">
                <i class="ph ph-warning-circle w-5 h-5 mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Top Action Bar -->
        <div class="card p-4 sm:p-5 flex flex-col sm:flex-row gap-4 justify-between items-center w-full">
            <div class="relative w-full sm:max-w-md">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                </div>
                <input type="text" placeholder="Cari nama, email, atau telepon..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
            </div>

            <a href="/admin/users/create" class="btn-primary w-full sm:w-auto text-sm justify-center shadow-md">
                <i class="ph ph-plus ph-bold w-4 h-4"></i> Tambah User
            </a>
        </div>

        <!-- Main Container Card -->
        <div class="card overflow-hidden border-primary-100">

            <div
                class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
                <i class="ph ph-users w-5 h-5 text-primary-600 ph-fill"></i>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Daftar Pengguna</h2>
            </div>

            <!-- Table Toolbar -->
            <div class="px-6 py-3 border-b border-primary-100 flex flex-wrap gap-4 items-center justify-between bg-white">
                <span class="text-xs text-gray-500 font-medium">Menampilkan {{ $users->count() }} user</span>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap lg:whitespace-normal">
                    <thead
                        class="bg-gradient-to-r from-primary-50/50 to-primary-50/20 text-primary-700 text-xs font-bold uppercase tracking-wide border-b-2 border-primary-100">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Telepon</th>
                            <th class="px-6 py-4 text-center">Bergabung</th>
                            <th class="px-6 py-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-primary-50/10 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img loading="lazy" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                            class="w-10 h-10 rounded-full object-cover ring-2 ring-primary-100">
                                        <div>
                                            <div
                                                class="font-bold text-gray-900 group-hover:text-primary-600 transition-colors">
                                                {{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">ID: #{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700">{{ $user->phone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-gray-600 text-xs">{{ $user->created_at->locale('id')->isoFormat('D MMM YYYY') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="/admin/users/{{ $user->id }}/edit"
                                            class="icon-button text-gray-500 hover:text-amber-600 hover:bg-amber-50 bg-white border border-gray-200 rounded-lg shadow-sm transition-colors"
                                            title="Edit">
                                            <i class="ph ph-pencil-simple ph-bold w-4 h-4"></i>
                                        </a>
                                        <form id="delete-user-{{ $user->id }}" action="/admin/users/{{ $user->id }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" 
                                            @click="$dispatch('confirm-action', { 
                                                title: 'Hapus User?', 
                                                message: 'Apakah Anda yakin ingin menghapus user <strong>{{ $user->name }}</strong>? Tindakan ini tidak dapat dibatalkan.', 
                                                confirmText: 'Ya, Hapus', 
                                                action: () => document.getElementById('delete-user-{{ $user->id }}').submit() 
                                            })"
                                            class="icon-button text-gray-500 hover:text-red-600 hover:bg-red-50 bg-white border border-gray-200 rounded-lg shadow-sm transition-colors"
                                            title="Hapus">
                                            <i class="ph ph-trash ph-bold w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                            <i class="ph ph-users text-gray-300 w-8 h-8"></i>
                                        </div>
                                        <p class="text-gray-500">Belum ada user terdaftar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="bg-gradient-to-r from-primary-50/30 to-primary-50/10 border-t border-primary-100 p-5 flex w-full">
                    {{ $users->links('vendor.pagination.custom') }}
                </div>
            @endif

        </div>

    </div>
@endsection
