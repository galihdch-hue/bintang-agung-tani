@extends('layouts.admin')

@section('title', 'Kelola Metode Pembayaran')

@section('content')
<div class="container-main space-y-6 fade-in">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Metode Pembayaran</span></div></li>
                </ol>
            </nav>
            <h1 class="heading-page">Metode Pembayaran</h1>
            <p class="text-gray-500 mt-1 body-small">Kelola metode pembayaran untuk transaksi pelanggan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payment-methods.create') }}" class="btn-primary flex items-center gap-2 h-10 px-4 shadow-soft">
                <i class="ph ph-plus-circle w-5 h-5"></i>
                Tambah Metode
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-primary-500 bg-primary-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-primary-600 uppercase tracking-wider mb-1">Metode Aktif</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentMethods->where('is_active', true)->count() }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <i class="ph ph-check-circle ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-red-500 bg-red-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Nonaktif</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentMethods->where('is_active', false)->count() }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i class="ph ph-x-circle ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-blue-500 bg-blue-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Total Order</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentMethods->sum('orders_count') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="ph ph-shopping-cart ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-amber-500 bg-amber-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Total Metode</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentMethods->count() }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="ph ph-credit-card ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card p-0 overflow-hidden w-full border-primary-100">
        <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
            <i class="ph ph-credit-card w-5 h-5 text-primary-600 ph-fill"></i>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Daftar Metode Pembayaran</h2>
        </div>
        
        <div class="table-responsive">
            <table class="w-full text-left text-sm whitespace-nowrap" role="table" aria-label="Daftar metode pembayaran">
                <thead class="bg-gradient-to-r from-primary-50/60 to-primary-50/30 text-primary-700 text-xs uppercase font-bold tracking-wider border-b-2 border-primary-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">Nama</th>
                        <th scope="col" class="px-6 py-4 text-left">Bank</th>
                        <th scope="col" class="px-6 py-4 text-left">No. Rekening</th>
                        <th scope="col" class="px-6 py-4 text-left">Pemilik</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Order</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($paymentMethods as $method)
                    <tr class="hover:bg-primary-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                                    <i class="ph ph-credit-card text-lg"></i>
                                </div>
                                <span class="font-bold text-gray-900">{{ $method->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $method->bank_name }}</td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm bg-gray-50 px-2 py-1 rounded border border-gray-200">{{ $method->account_number }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $method->account_name }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($method->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border bg-primary-100 text-primary-700 border-primary-200">
                                    <i class="ph ph-check-circle w-3.5 h-3.5"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border bg-red-100 text-red-700 border-red-200">
                                    <i class="ph ph-x-circle w-3.5 h-3.5"></i>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-gray-900">{{ $method->orders_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.payment-methods.edit', $method) }}" class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1" aria-label="Edit {{ $method->name }}">
                                    <i class="ph ph-pencil-simple w-4 h-4"></i>
                                    <span class="hidden sm:inline">Edit</span>
                                </a>
                                <form action="{{ route('admin.payment-methods.toggle', $method) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-amber-600 hover:text-amber-800 hover:bg-amber-50 px-2 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1" aria-label="{{ $method->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $method->name }}">
                                        <i class="ph ph-toggle-{{ $method->is_active ? 'right' : 'left' }} w-4 h-4"></i>
                                        <span class="hidden sm:inline">{{ $method->is_active ? 'Matikan' : 'Aktifkan' }}</span>
                                    </button>
                                </form>
                                <form id="delete-payment-method-{{ $method->id }}" action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" 
                                    @click="$dispatch('confirm-action', { 
                                        title: 'Hapus Metode Pembayaran?', 
                                        message: 'Apakah Anda yakin ingin menghapus metode pembayaran <strong>{{ $method->name }}</strong>?', 
                                        confirmText: 'Ya, Hapus', 
                                        action: () => document.getElementById('delete-payment-method-{{ $method->id }}').submit() 
                                    })"
                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1" aria-label="Hapus {{ $method->name }}">
                                    <i class="ph ph-trash w-4 h-4"></i>
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="ph ph-credit-card text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Metode</h3>
                                <p class="text-gray-500 mb-4">Tambahkan metode pembayaran pertama Anda.</p>
                                <a href="{{ route('admin.payment-methods.create') }}" class="btn-primary">
                                    <i class="ph ph-plus-circle w-4 h-4 mr-1"></i>
                                    Tambah Metode
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
