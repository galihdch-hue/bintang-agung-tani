@extends('layouts.admin')

@section('title', 'Verifikasi Bukti Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Verifikasi Pembayaran</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Verifikasi Bukti Pembayaran</h1>
            <p class="text-sm text-gray-500 mt-1">Verifikasi dan kelola bukti pembayaran dari pelanggan.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-blue-500 bg-blue-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Total Bukti</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentProofs->total() }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="ph ph-receipt ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-amber-500 bg-amber-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Menunggu</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentProofs->where('status', 'pending')->count() }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="ph ph-clock ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-primary-500 bg-primary-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-primary-600 uppercase tracking-wider mb-1">Terverifikasi</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentProofs->where('status', 'verified')->count() }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <i class="ph ph-check-circle ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-red-500 bg-red-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Ditolak</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $paymentProofs->where('status', 'rejected')->count() }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i class="ph ph-x-circle ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card p-6 border-primary-100">
        <form action="{{ route('admin.payment-proofs.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="form-input w-full">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order ID</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text" name="order_number" value="{{ request('order_number') }}" 
                               placeholder="Cari order..."
                               class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary h-11 px-4 whitespace-nowrap">
                    <i class="ph ph-funnel w-4 h-4 mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.payment-proofs.index') }}" class="btn-secondary h-11 px-4 whitespace-nowrap">
                    <i class="ph ph-arrow-counter-clockwise w-4 h-4 mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card p-0 overflow-hidden w-full border-primary-100">
        <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
            <i class="ph ph-receipt w-5 h-5 text-primary-600 ph-fill"></i>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Daftar Bukti Pembayaran</h2>
        </div>
        
        <div class="table-responsive">
            <table class="w-full text-left text-sm whitespace-nowrap" role="table" aria-label="Daftar bukti pembayaran">
                <thead class="bg-gradient-to-r from-primary-50/60 to-primary-50/30 text-primary-700 text-xs uppercase font-bold tracking-wider border-b-2 border-primary-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">Order</th>
                        <th scope="col" class="px-6 py-4 text-left">Pengguna</th>
                        <th scope="col" class="px-6 py-4 text-left">Metode</th>
                        <th scope="col" class="px-6 py-4 text-left">Tanggal Upload</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($paymentProofs as $proof)
                    <tr class="hover:bg-primary-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-primary-600 bg-primary-50 px-2 py-1 rounded border border-primary-100">{{ $proof->order->order_number ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ substr($proof->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $proof->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $proof->paymentMethod->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $proof->created_at->format('d M Y') }}</span>
                            <span class="text-xs text-gray-500 block">{{ $proof->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($proof->isPending())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border bg-amber-100 text-amber-700 border-amber-200">
                                    <i class="ph ph-clock w-3.5 h-3.5"></i>
                                    Menunggu
                                </span>
                            @elseif($proof->isVerified())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border bg-primary-100 text-primary-700 border-primary-200">
                                    <i class="ph ph-check-circle w-3.5 h-3.5"></i>
                                    Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border bg-red-100 text-red-700 border-red-200">
                                    <i class="ph ph-x-circle w-3.5 h-3.5"></i>
                                    Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.payment-proofs.show', $proof) }}" class="btn-primary text-xs h-8 px-3 inline-flex items-center gap-1.5">
                                <i class="ph ph-eye w-4 h-4"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="ph ph-receipt text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Bukti</h3>
                                <p class="text-gray-500 mb-4">Belum ada bukti pembayaran yang perlu diverifikasi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($paymentProofs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $paymentProofs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
