@extends('layouts.app')

@section('title', 'Riwayat Pembelian')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('user.dashboard') }}" class="hover:text-gray-800 transition-colors">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <span class="text-gray-900 font-medium">Riwayat Pembelian</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Riwayat Pembelian</h1>
            <p class="text-gray-500 mt-2 text-sm">Kelola dan pantau status pesanan belanja Anda.</p>
        </div>
    </div>

    @php
        $currentStatus = request('status', 'semua');
    @endphp

    <!-- Filter Status Tabs -->
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 overflow-x-auto scrollbar-hide">
            <li class="me-2">
                <a href="{{ route('user.orders.index', ['status' => 'semua']) }}" class="inline-flex items-center justify-center p-4 {{ $currentStatus === 'semua' ? 'text-primary-600 border-b-2 border-primary-600 rounded-t-lg active bg-primary-50/50' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all' }}">
                    Semua Pesanan
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('user.orders.index', ['status' => 'menunggu-verifikasi']) }}" class="inline-flex items-center justify-center p-4 {{ $currentStatus === 'menunggu-verifikasi' ? 'text-primary-600 border-b-2 border-primary-600 rounded-t-lg active bg-primary-50/50' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all' }}">
                    Menunggu Verifikasi
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('user.orders.index', ['status' => 'diproses']) }}" class="inline-flex items-center justify-center p-4 {{ $currentStatus === 'diproses' ? 'text-primary-600 border-b-2 border-primary-600 rounded-t-lg active bg-primary-50/50' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all' }}">
                    Diproses
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('user.orders.index', ['status' => 'selesai']) }}" class="inline-flex items-center justify-center p-4 {{ $currentStatus === 'selesai' ? 'text-primary-600 border-b-2 border-primary-600 rounded-t-lg active bg-primary-50/50' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all' }}">
                    Selesai
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('user.orders.index', ['status' => 'dibatalkan']) }}" class="inline-flex items-center justify-center p-4 {{ $currentStatus === 'dibatalkan' ? 'text-primary-600 border-b-2 border-primary-600 rounded-t-lg active bg-primary-50/50' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all' }}">
                    Dibatalkan
                </a>
            </li>
        </ul>
    </div>

    <!-- Date Range & Search Filter Bar -->
    <div class="card p-3 md:p-4 flex flex-col md:flex-row gap-4 items-center mb-6">
        <div class="flex flex-1 w-full flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" placeholder="Dari Tanggal" class="form-input w-full pl-4 pr-10">
            </div>
            <div class="hidden sm:flex items-center justify-center bg-gray-50 border border-gray-200 rounded-lg px-3 mb-1 mt-1">
                <i class="ph ph-calendar-blank ph-bold w-4.5 h-4.5 text-gray-500"></i>
            </div>
            <div class="relative flex-1">
                <input type="text" placeholder="Sampai Tanggal" class="form-input w-full pl-4 pr-10">
            </div>
        </div>
        <div class="w-full md:w-auto shrink-0 flex gap-3">
            <input type="text" placeholder="Cari ID Pesanan, Nama Barang..." class="form-input flex-1 lg:w-64 hidden md:block">
            <button class="btn-primary w-full md:w-auto justify-center text-sm shadow-sm">
                Cari Pesanan <i class="ph ph-magnifying-glass ph-bold w-4 h-4 ml-1"></i>
            </button>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="card overflow-hidden">
        
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <i class="ph ph-receipt ph-bold w-5 h-5 text-primary-600"></i>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Status Pembelian</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                <thead class="bg-white font-medium text-gray-500 text-xs tracking-wide border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">ID Pesanan & Produk Utama</th>
                        <th class="px-6 py-4 flex items-center gap-1 cursor-pointer hover:text-gray-900 transition-colors">Tanggal <i class="ph ph-caret-down ph-bold w-3.5 h-3.5"></i></th>
                        <th class="px-6 py-4">Status Pesanan</th>
                        <th class="px-6 py-4 text-right">Total Belanja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $order)
                    <!-- Row {{ $loop->iteration }} -->
                    <tr class="hover:bg-primary-50/10 transition-colors">
                        <td class="px-6 py-5 align-top">
                            <a href="{{ route('user.orders.show', $order) }}" class="font-bold text-gray-900 hover:text-primary-600 block mb-3 hover:underline">{{ $order->order_number }}</a>
                            <!-- Product summary mini display -->
                            @if($order->items->count() > 0)
                            <div class="flex items-start gap-3 w-56 sm:w-auto">
                                <div class="w-12 h-12 shrink-0 bg-gray-50 border border-gray-100 rounded-lg p-1.5 flex items-center justify-center">
                                    @if($order->items->first()->product && $order->items->first()->product->getFirstImage())
                                        <img loading="lazy" src="{{ $order->items->first()->product->getFirstImage() }}" class="w-full h-full object-cover rounded mix-blend-multiply">
                                    @else
                                        <i class="ph ph-package w-6 h-6 text-gray-300"></i>
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-gray-900 leading-tight">{{ $order->items->first()->product_name }}</span>
                                    <span class="text-[10px] text-gray-500 flex items-center gap-1 mt-0.5"><i class="ph ph-package w-3 h-3 text-gray-400"></i> {{ $order->items->first()->quantity }} {{ $order->items->first()->unit }}</span>
                                    @if($order->items->count() > 1)
                                    <span class="text-xs text-primary-600 font-semibold mt-1">+ {{ $order->items->count() - 1 }} Produk Lainnya</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-5 align-top text-gray-600 font-medium">{{ $order->created_at->format('d F Y') }}</td>
                        <td class="px-6 py-5 align-top">
                            <span class="inline-flex py-1 px-3 text-xs font-bold rounded-full {{ $order->getStatusBadgeClass() }} border">
                                {{ $order->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-5 align-top text-right" x-data="{ showCancelModal: false }">
                            <div class="font-bold text-gray-900 text-base">{{ $order->getFormattedTotal() }}</div>
                            @if($order->canBeCancelled())
                                <button type="button" @click="showCancelModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 mt-3 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-300 text-xs font-bold shadow-sm hover:shadow-md group">
                                    <i class="ph ph-x-circle ph-bold w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i> Batalkan
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
                                             class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100 text-left">
                                            
                                            <div class="p-8 text-center">
                                                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 ring-4 ring-red-50/50">
                                                    <i class="ph ph-warning-circle ph-fill text-3xl"></i>
                                                </div>
                                                
                                                <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">Batalkan Pesanan?</h3>
                                                <p class="text-gray-500 text-sm leading-relaxed font-medium px-4">Apakah Anda yakin ingin membatalkan pesanan <span class="text-gray-900 font-bold">#{{ $order->order_number }}</span> ini? Tindakan ini permanen.</p>
                                                
                                                <div class="grid grid-cols-2 gap-3 mt-8">
                                                    <button @click="showCancelModal = false" type="button" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-all text-xs">
                                                        Kembali
                                                    </button>
                                                    <form action="{{ route('user.orders.cancel', $order) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-red-200 text-xs">
                                                            Ya, Batalkan
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            @elseif($order->canBePaid())
                            <a href="{{ route('user.payments.select-method', $order) }}" class="btn-primary mt-3 text-xs py-2 px-3 inline-flex items-center gap-1">
                                Bayar Sekarang
                                <i class="ph ph-arrow-right ph-bold"></i>
                            </a>
                            @elseif($order->isProcessing() || $order->isMenungguVerifikasi())
                            <a href="{{ route('user.orders.show', $order) }}" class="btn-secondary mt-3 text-xs py-2 px-3 inline-block">
                                Pantau Pesanan
                            </a>
                            @else
                            <a href="{{ route('user.orders.show', $order) }}" class="btn-secondary mt-3 text-xs py-2 px-3 inline-block">
                                Lihat Detail
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-receipt ph-bold w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                            <p class="font-medium">Belum ada pesanan</p>
                            <p class="text-sm mt-1">Mulai berbelanja untuk melihat riwayat pesanan Anda</p>
                            <a href="{{ route('user.produk.index') }}" class="btn-primary mt-4 inline-flex items-center gap-2">
                                <i class="ph ph-shopping-cart ph-bold"></i>
                                Belanja Sekarang
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50/50 border-t border-gray-100 p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            @if($orders->count() > 0)
            <span class="text-sm text-gray-500 font-medium">Menampilkan {{ $orders->firstItem() }}-{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan</span>
            @endif
            
            <nav aria-label="Page navigation" class="mx-auto sm:mx-0 w-full flex">
                {{ $orders->links('vendor.pagination.custom') }}
            </nav>

        </div>

    </div>

    <!-- Features Banner -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        <div class="card p-5 flex items-start gap-4">
            <div class="text-primary-600 bg-primary-50 p-2.5 rounded-full shrink-0 border border-primary-100">
                <i class="ph ph-truck ph-fill w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-1 text-sm">Pengiriman Terjamin</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Pengiriman cepat dan aman dengan JNE & J&T Express.</p>
            </div>
        </div>
        <div class="card p-5 flex items-start gap-4">
            <div class="text-primary-600 bg-primary-50 p-2.5 rounded-full shrink-0 border border-primary-100">
                <i class="ph ph-credit-card ph-fill w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-1 text-sm">Bayar Lebih Aman</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Transfer bank atau dompet digital yang terintegrasi secara aman.</p>
            </div>
        </div>
        <div class="card p-5 flex items-start gap-4">
            <div class="text-primary-600 bg-primary-50 p-2.5 rounded-full shrink-0 border border-primary-100">
                <i class="ph ph-clock ph-fill w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-1 text-sm">Lacak 24/7</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Pantau status pesanan kapan saja, di mana saja dengan mudah.</p>
            </div>
        </div>
    </div>

</div>
@endsection
