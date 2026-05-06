@php
use App\Models\Setting;
@endphp

@extends('layouts.app')

@section('title', 'Detail Riwayat Pesanan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4 md:mt-0 relative z-10 w-full">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="{{ route('user.dashboard') }}" class="hover:text-primary-600 transition-colors">Beranda</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="{{ route('user.orders.index') }}" class="hover:text-primary-600 transition-colors">Pesanan Saya</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Inv. {{ $order->order_number }}</span></div></li>
                </ol>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Detail Transaksi</h1>
                @php
                    $statusBadgeClass = $order->getStatusBadgeClass();
                @endphp
                <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $statusBadgeClass }} border mt-1.5 shadow-sm">
                    {{ $order->getStatusLabel() }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">No. Invoice: <span class="font-bold text-gray-800 tracking-wider">{{ $order->order_number }}</span></p>
        </div>
        <div class="flex items-center gap-3" x-data="{ showCancelModal: false }">
            <a href="{{ route('user.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-sm font-semibold shadow-sm hover:shadow-md">
                <i class="ph ph-arrow-left ph-bold w-4 h-4 text-gray-400"></i> Kembali
            </a>
            
            @if($order->canBeCancelled())
                <button type="button" @click="showCancelModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-300 text-sm font-bold shadow-sm hover:shadow-red-200/50 hover:shadow-lg group">
                    <i class="ph ph-x-circle ph-bold w-4 h-4 group-hover:scale-110 transition-transform"></i> Batalkan Pesanan
                </button>

                <!-- Modal Konfirmasi Pembatalan -->
                <template x-teleport="body">
                    <div x-show="showCancelModal" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                        
                        <div x-show="showCancelModal"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                             @click.away="showCancelModal = false"
                             class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100">
                            
                            <div class="p-8 text-center">
                                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-red-50/50 animate-pulse">
                                    <i class="ph ph-warning-circle ph-fill text-4xl"></i>
                                </div>
                                
                                <h3 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">Batalkan Pesanan?</h3>
                                <p class="text-gray-500 leading-relaxed font-medium">Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin membatalkan pesanan <span class="text-gray-900 font-bold">#{{ $order->order_number }}</span> ini?</p>
                                
                                <div class="grid grid-cols-2 gap-4 mt-8">
                                    <button @click="showCancelModal = false" type="button" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-bold transition-all text-sm">
                                        Tidak, Kembali
                                    </button>
                                    <form action="{{ route('user.orders.cancel', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-red-200 text-sm">
                                            Ya, Batalkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex items-center justify-center gap-2">
                                <i class="ph ph-info ph-bold text-gray-400 w-4 h-4"></i>
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Bintang Agung Tani Safety Protocol</span>
                            </div>
                        </div>
                    </div>
                </template>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Order Warning / Important Info - Only show when verified -->
            @if($order->canViewBarcode())
            <div class="bg-gradient-to-r from-green-50 to-green-100/50 border border-green-200 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100/80 text-green-600 flex items-center justify-center shrink-0 border border-green-200/50 shadow-inner">
                        <i class="ph ph-check-circle ph-duotone w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-green-800 text-lg mb-1 tracking-tight">Barang Siap Diambil!</h3>
                        <p class="text-sm text-green-700 font-medium leading-relaxed">Silakan datang ke toko dengan menunjukkan Barcode QR pengambilan kepada admin kami untuk memproses pesanan ini.</p>
                    </div>
                </div>
                <a href="{{ route('user.barcode-pesanan', $order) }}" class="btn-primary shrink-0 bg-primary-600 hover:bg-primary-700 border-primary-600 w-full sm:w-auto text-sm shadow-md">
                    <i class="ph ph-qr-code ph-bold w-5 h-5 mr-1.5 text-white"></i> Lihat Barcode QR
                </a>
            </div>
            @elseif($order->isMenungguVerifikasi())
            <!-- Waiting for admin verification -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 border border-orange-200 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100/80 text-orange-600 flex items-center justify-center shrink-0 border border-orange-200/50 shadow-inner">
                        <i class="ph ph-clock ph-duotone w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-orange-800 text-lg mb-1 tracking-tight">Menunggu Verifikasi</h3>
                        <p class="text-sm text-orange-700 font-medium leading-relaxed">Bukti pembayaran Anda sedang diverifikasi oleh admin. Barcode QR akan tersedia setelah verifikasi selesai.</p>
                    </div>
                </div>
                <div class="shrink-0">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-orange-100 text-orange-800 text-sm font-bold">
                        <i class="ph ph-hourglass ph-duotone w-4 h-4 mr-2"></i>
                        Diproses
                    </span>
                </div>
            </div>
            @elseif($order->isPaymentPending())
            <!-- Waiting for payment -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 border border-orange-200 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100/80 text-orange-600 flex items-center justify-center shrink-0 border border-orange-200/50 shadow-inner">
                        <i class="ph ph-credit-card ph-duotone w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-orange-800 text-lg mb-1 tracking-tight">Belum Bayar</h3>
                        <p class="text-sm text-orange-700 font-medium leading-relaxed">Silakan lakukan pembayaran dan upload bukti pembayaran untuk melanjutkan pesanan Anda.</p>
                    </div>
                </div>
                <a href="{{ route('user.payments.show-upload', $order) }}" class="btn-primary shrink-0 bg-primary-600 hover:bg-primary-700 border-primary-600 w-full sm:w-auto text-sm shadow-md">
                    <i class="ph ph-upload ph-bold w-5 h-5 mr-1.5 text-white"></i> Upload Pembayaran
                </a>
            </div>
            @endif

            <!-- Ordered Items -->
            <div class="card p-0 overflow-hidden border border-gray-100">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-200/50 text-gray-600 flex items-center justify-center">
                        <i class="ph ph-shopping-bag ph-fill w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-bold text-gray-900">Produk yang Dipesan</h2>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @forelse($order->items as $item)
                    <div class="p-6 flex flex-col sm:flex-row items-start gap-5 hover:bg-gray-50/30 transition-colors">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-gray-100 border border-gray-200 shrink-0 overflow-hidden shadow-sm">
                            <img loading="lazy" src="{{ $item->product ? ($item->product->getFirstImage() ?? asset('images/placeholder-product.png')) : asset('images/placeholder-product.png') }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow flex flex-col justify-between w-full">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-2">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base sm:text-lg leading-tight mb-1">{{ $item->product_name }}</h3>
                                    <p class="text-gray-500 text-xs sm:text-sm font-medium">{{ $item->product ? ($item->product->category->name ?? '-') : '-' }}</p>
                                </div>
                                <div class="text-left sm:text-right mt-1 sm:mt-0">
                                    <p class="font-black text-gray-900 text-lg">{{ $item->getFormattedSubtotal() }}</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center gap-3 w-full border-t border-gray-100 pt-3">
                                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded uppercase tracking-wider">{{ $item->quantity }} Item</span>
                                <span class="text-xs text-gray-500 font-medium">× {{ $item->getFormattedUnitPrice() }} / item</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-500">
                        <p>Tidak ada item dalam pesanan ini</p>
                    </div>
                    @endforelse
                </div>
                
                <div class="bg-gray-50 p-6 border-t border-gray-200 mt-2">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-medium text-gray-500">Subtotal Produk</span>
                        <span class="font-bold text-gray-800">{{ $order->getFormattedSubtotal() }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-5 pb-5 border-b border-gray-200 border-dashed">
                        <span class="text-sm font-medium text-gray-500">Biaya Pengiriman</span>
                        <span class="font-bold text-gray-800">{{ $order->getFormattedShippingCost() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900 uppercase tracking-widest text-sm">Total Pembayaran</span>
                        <span class="text-2xl font-black text-primary-600">{{ $order->getFormattedTotal() }}</span>
                    </div>
                </div>
            </div>

            <!-- Delivery/Pickup Info -->
            <div class="card p-0 overflow-hidden border border-gray-100">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-200/50 text-gray-600 flex items-center justify-center">
                        <i class="ph ph-map-pin ph-fill w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-bold text-gray-900">Informasi Pengambilan</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6">
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2 mb-3 border-b border-gray-200 pb-2">
                            <i class="ph ph-storefront ph-fill w-4 h-4 text-gray-400"></i>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Titik Lokasi Toko</h3>
                        </div>
                        <p class="font-bold text-gray-900 mb-1">{{ Setting::get('store_name', 'Bintang Agung Tani Utama') }}</p>
                        <p class="text-xs text-gray-500 leading-relaxed font-medium">{{ Setting::get('store_address', 'Jl. Raya Pertanian No.12, Kabupaten Sukabumi, Jawa Barat') }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2 mb-3 border-b border-gray-200 pb-2">
                            <i class="ph ph-user ph-fill w-4 h-4 text-gray-400"></i>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Data Pengambil</h3>
                        </div>
                        <p class="font-bold text-gray-900 mb-1">{{ $order->user->name }}</p>
                        <div class="space-y-1">
                            <p class="text-xs text-gray-500 font-medium flex items-center gap-1.5"><i class="ph ph-phone w-3.5 h-3.5"></i> {{ $order->user->phone ?? '-' }}</p>
                            <p class="text-xs text-gray-500 font-medium flex items-center gap-1.5"><i class="ph ph-envelope-simple w-3.5 h-3.5"></i> {{ $order->user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Order Summary Sidebar Detail -->
            <div class="card p-6 border-t-4 border-t-primary-500 shadow-sm border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-5 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <i class="ph ph-receipt w-5 h-5 text-gray-400"></i> Info Dokumen
                </h2>
                
                <div class="space-y-4 text-sm">
                    <div class="flex flex-col gap-1 pb-3 border-b border-gray-50">
                        <span class="text-gray-500 font-medium text-xs uppercase tracking-wider">No. Invoice</span>
                        <span class="font-black text-gray-900 font-mono tracking-wider">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex flex-col gap-1 pb-3 border-b border-gray-50">
                        <span class="text-gray-500 font-medium text-xs uppercase tracking-wider">Waktu Pemesanan</span>
                        <span class="font-bold text-gray-800">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                        <span class="text-gray-500 font-medium">Status Pembayaran</span>
                        <span class="inline-flex py-1 px-2.5 text-[10px] font-black uppercase tracking-wider rounded {{ $order->getPaymentStatusClass() }}">
                            {{ $order->getPaymentStatusLabel() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Kanal Pembayaran</span>
                        <span class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] uppercase font-black italic border border-blue-100">{{ $order->payment_method ?? '-' }}</span> {{ $order->paymentMethod->name ?? '-' }}
                        </span>
                    </div>
                </div>
                
                @php
                    $latestProof = $order->latestPaymentProof;
                @endphp

                @if($latestProof)
                    <!-- Bukti Pembayaran Info -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-700">Bukti Pembayaran</span>
                            <span class="inline-flex py-1 px-2.5 text-[10px] font-bold uppercase tracking-wider rounded
                                @if($latestProof->status === 'pending') bg-yellow-100 text-yellow-700 border border-yellow-200
                                @elseif($latestProof->status === 'verified') bg-green-100 text-green-700 border border-green-200
                                @else bg-red-100 text-red-700 border border-red-200 @endif">
                                {{ $latestProof->getStatusLabel() }}
                            </span>
                        </div>
                        <a href="{{ $latestProof->getImageUrl() }}" target="_blank" class="block relative group">
                            <img src="{{ $latestProof->getImageUrl() }}" alt="Bukti Pembayaran" class="w-full h-40 object-cover rounded-xl border border-gray-200">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center">
                                <span class="text-white font-medium text-sm flex items-center gap-2">
                                    <i class="ph ph-eye w-4 h-4"></i> Lihat Detail
                                </span>
                            </div>
                        </a>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <i class="ph ph-clock w-3 h-3"></i>
                            Diupload {{ $latestProof->created_at->diffForHumans() }}
                        </p>
                        @if($latestProof->admin_notes)
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg text-xs text-gray-600">
                                <span class="font-semibold">Catatan Admin:</span> {{ $latestProof->admin_notes }}
                            </div>
                        @endif
                    </div>
                @elseif($order->canUploadProof())
                    <a href="{{ route('user.payments.show-upload', $order) }}" class="btn-primary w-full mt-6 text-sm font-semibold h-11">
                        <i class="ph ph-upload w-4 h-4 mr-1.5"></i> Upload Bukti Pembayaran
                    </a>
                @endif
            </div>
            
            <!-- Tracking Log Widget -->
            <div class="card p-0 overflow-hidden shadow-sm border border-gray-100">
                <div class="bg-gray-50/80 px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-[15px] flex items-center gap-2">
                        <i class="ph ph-magnifying-glass-plus ph-bold w-4 h-4 text-gray-400"></i> Log Aktivitas
                    </h3>
                </div>
                
                <div class="p-6 bg-white max-h-80 overflow-y-auto">
                    <div class="relative border-l-2 border-primary-200 ml-2 space-y-6">
                        @forelse($order->statusHistories as $history)
                        <div class="relative pl-5 {{ !$loop->first ? 'opacity-60' : '' }}">
                            <div class="absolute w-3 h-3 bg-white rounded-full border-2 {{ $loop->first ? 'border-primary-500' : 'border-gray-300' }} shadow-sm -left-[7px] top-1"></div>
                            <h4 class="font-bold {{ $loop->first ? 'text-gray-900' : 'text-gray-700' }} text-sm">{{ $history->getStatusLabel() }}</h4>
                            <span class="text-[10px] text-gray-500 font-bold tracking-wider uppercase mt-1 block">{{ $history->created_at->format('d M, H:i') }} WIB</span>
                            @if($history->notes)
                            <p class="text-xs text-gray-400 mt-1">{{ $history->notes }}</p>
                            @endif
                        </div>
                        @empty
                        <div class="relative pl-5">
                            <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
