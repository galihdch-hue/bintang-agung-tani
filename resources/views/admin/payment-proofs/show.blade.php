@extends('layouts.admin')

@section('title', 'Detail Verifikasi Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i></div></li>
                    <li><a href="{{ route('admin.payment-proofs.index') }}" class="hover:text-primary-600 transition-colors">Verifikasi Pembayaran</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i></div></li>
                    <li class="text-gray-900 font-medium">Detail</li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Detail Verifikasi Pembayaran</h1>
            <p class="text-sm text-gray-500 mt-1">Verifikasi bukti pembayaran order <strong class="text-primary-600">{{ $paymentProof->order->order_number ?? '-' }}</strong>.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payment-proofs.index') }}" class="btn-secondary flex items-center gap-2 h-10 px-4">
                <i class="ph ph-arrow-left w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="card p-4 {{ $paymentProof->isPending() ? 'bg-amber-50 border-amber-200' : ($paymentProof->isVerified() ? 'bg-primary-50 border-primary-200' : 'bg-red-50 border-red-200') }}">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl {{ $paymentProof->isPending() ? 'bg-amber-100 text-amber-600' : ($paymentProof->isVerified() ? 'bg-primary-100 text-primary-600' : 'bg-red-100 text-red-600') }} flex items-center justify-center">
                @if($paymentProof->isPending())
                    <i class="ph ph-clock text-2xl"></i>
                @elseif($paymentProof->isVerified())
                    <i class="ph ph-check-circle text-2xl"></i>
                @else
                    <i class="ph ph-x-circle text-2xl"></i>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500">Status Verifikasi</p>
                <p class="text-xl font-bold {{ $paymentProof->isPending() ? 'text-amber-700' : ($paymentProof->isVerified() ? 'text-primary-700' : 'text-red-700') }}">
                    {{ $paymentProof->getStatusLabel() }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Payment Info -->
        <div class="space-y-6">
            <!-- Bukti Pembayaran Card -->
            <div class="card p-0 overflow-hidden border-primary-100">
                <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
                    <i class="ph ph-receipt w-5 h-5 text-primary-600 ph-fill"></i>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informasi Bukti Pembayaran</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600">Order ID</span>
                            <span class="font-bold text-primary-600 bg-primary-50 px-2 py-1 rounded border border-primary-100">{{ $paymentProof->order->order_number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600">Pengguna</span>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($paymentProof->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $paymentProof->user->name ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600">Metode Pembayaran</span>
                            <span class="font-medium text-gray-900">{{ $paymentProof->paymentMethod->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600">Tanggal Upload</span>
                            <span class="font-medium text-gray-900">{{ $paymentProof->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($paymentProof->verified_at)
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600">Diverifikasi Oleh</span>
                            <span class="font-medium text-gray-900">{{ $paymentProof->verifier->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600">Tanggal Verifikasi</span>
                            <span class="font-medium text-gray-900">{{ $paymentProof->verified_at->format('d M Y H:i') }}</span>
                        </div>
                        @endif
                        @if($paymentProof->notes)
                        <div class="py-3 border-b border-gray-100">
                            <span class="text-gray-600 block mb-1">Catatan Pengguna</span>
                            <span class="font-medium text-gray-900">{{ $paymentProof->notes }}</span>
                        </div>
                        @endif
                        @if($paymentProof->admin_notes)
                        <div class="py-3 border-b border-gray-100">
                            <span class="text-gray-600 block mb-1">Catatan Admin</span>
                            <span class="font-medium text-gray-900">{{ $paymentProof->admin_notes }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Gambar Bukti -->
            <div class="card p-0 overflow-hidden border-primary-100">
                <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
                    <i class="ph ph-image w-5 h-5 text-primary-600 ph-fill"></i>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Gambar Bukti Pembayaran</h2>
                </div>
                <div class="p-6">
                    @if($paymentProof->image_path)
                        <img src="{{ Storage::url($paymentProof->image_path) }}" alt="Bukti Pembayaran" class="max-w-full rounded-lg shadow-lg border border-gray-200">
                    @else
                        <div class="flex flex-col items-center justify-center py-8 bg-gray-50 rounded-lg border border-gray-200 border-dashed">
                            <i class="ph ph-image text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Tidak ada gambar bukti pembayaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Order Detail & Actions -->
        <div class="space-y-6">
            <!-- Detail Order Card -->
            <div class="card p-0 overflow-hidden border-primary-100">
                <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
                    <i class="ph ph-shopping-cart w-5 h-5 text-primary-600 ph-fill"></i>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Detail Order</h2>
                </div>
                <div class="p-6">
                    @if($paymentProof->order)
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-600">Total Order</span>
                                <span class="font-bold text-primary-600 text-lg">{{ $paymentProof->order->getFormattedTotal() }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-600">Status Order</span>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $paymentProof->order->getStatusBadgeClass() }}">
                                    {{ $paymentProof->order->getStatusLabel() }}
                                </span>
                            </div>
                        </div>
                        
                        <h3 class="font-semibold text-gray-900 mb-3">Items</h3>
                        <div class="border rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Produk</th>
                                        <th class="px-4 py-2 text-center font-semibold text-gray-700">Qty</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Harga</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($paymentProof->order->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $item->product->name ?? ($item->product_name ?? '-') }}</td>
                                        <td class="px-4 py-3 text-center font-medium">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8">
                            <i class="ph ph-package text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Order tidak ditemukan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Verifikasi Actions -->
            @if($paymentProof->isPending())
                <div class="card p-0 overflow-hidden border-primary-100">
                    <div class="bg-gradient-to-r from-primary-50/40 to-primary-50/10 px-6 py-4 border-b border-primary-100 flex items-center gap-2">
                        <i class="ph ph-check-circle w-5 h-5 text-primary-600 ph-fill"></i>
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Verifikasi Pembayaran</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Approve Form -->
                        <form action="{{ route('admin.payment-proofs.verify', $paymentProof) }}" method="POST">
                            @csrf
                            <div class="space-y-3">
                                <label for="verify_notes" class="block text-sm font-semibold text-gray-900">Catatan Verifikasi (opsional)</label>
                                <textarea name="notes" id="verify_notes" rows="3" class="form-input w-full" placeholder="Tambahkan catatan untuk verifikasi ini..."></textarea>
                            </div>
                            <button type="submit" class="btn-primary w-full mt-4 h-12 shadow-lg bg-primary-600 hover:bg-primary-700 border-primary-600">
                                <i class="ph ph-check-circle w-5 h-5 mr-1"></i>
                                Verifikasi Pembayaran
                            </button>
                        </form>

                        <hr class="border-gray-100">

                        <!-- Reject Form -->
                        <form action="{{ route('admin.payment-proofs.reject', $paymentProof) }}" method="POST">
                            @csrf
                            <div class="space-y-3">
                                <label for="reject_reason" class="block text-sm font-semibold text-gray-900">
                                    Alasan Penolakan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="reason" id="reject_reason" rows="3" class="form-input w-full" required placeholder="Jelaskan alasan penolakan..."></textarea>
                            </div>
                            <button type="submit" class="btn-primary w-full mt-4 h-12 shadow-lg bg-red-600 hover:bg-red-700 border-red-600">
                                <i class="ph ph-x-circle w-5 h-5 mr-1"></i>
                                Tolak Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card p-6 border-gray-100 bg-gray-50">
                    <div class="text-center">
                        <i class="ph ph-lock-key text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500">Pembayaran sudah {{ $paymentProof->isVerified() ? 'diverifikasi' : 'ditolak' }}. Tidak dapat diubah.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
