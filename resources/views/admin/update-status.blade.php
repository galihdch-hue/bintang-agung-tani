@extends('layouts.admin')

@section('title', 'Update Status Pesanan Massal')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0" x-data="{ selectedOrders: [], selectAll: false }">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Update Status Massal</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Update Status Massal</h1>
            <p class="text-sm text-gray-500 mt-1">Pilih multiple pesanan dan update statusnya sekaligus.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-primary-50 border border-primary-200 text-primary-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="ph ph-check-circle w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="ph ph-x-circle w-5 h-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Bulk Action Form -->
    <form action="{{ route('admin.orders.bulk-update-status') }}" method="POST" id="bulkUpdateForm">
        @csrf
        @method('PATCH')
        
        <!-- Toolbar -->
        <div class="card p-4 mb-4 bg-gradient-to-r from-primary-50/60 to-primary-50/30 border-primary-200">
            <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" 
                               x-model="selectAll" 
                               @change="selectedOrders = selectAll ? {{ json_encode($orders->pluck('id')) }} : []"
                               class="w-5 h-5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <span class="font-medium text-gray-700">Pilih Semua</span>
                    </div>
                    <span class="text-sm text-gray-500" x-show="selectedOrders.length > 0">
                        (<span x-text="selectedOrders.length"></span> pesanan dipilih)
                    </span>
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select name="status" required
                            class="form-input h-11 bg-white border-gray-200 py-0 text-sm font-medium min-w-[200px]">
                        <option value="">-- Pilih Status Baru --</option>
                        <option value="pending">Belum Bayar</option>
                        <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    <button type="submit" 
                            :disabled="selectedOrders.length === 0"
                            :class="selectedOrders.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="btn-primary h-11 px-6 shadow-md flex items-center gap-2 whitespace-nowrap">
                        <i class="ph ph-arrows-clockwise w-5 h-5"></i>
                        <span>Update Massal</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card p-0 overflow-hidden w-full border-primary-100">
            <div class="table-responsive">
                <table class="w-full text-left text-sm whitespace-nowrap lg:min-w-[1000px]">
                    <thead class="bg-gradient-to-r from-primary-50/60 to-primary-50/30 text-primary-700 text-xs uppercase font-bold tracking-wider border-b-2 border-primary-100">
                        <tr>
                            <th class="px-4 py-4 w-12 text-center"></th>
                            <th class="px-4 py-4 w-12 text-center">No</th>
                            <th class="px-4 py-4">ID Pesanan</th>
                            <th class="px-4 py-4">Pelanggan</th>
                            <th class="px-4 py-4">Tanggal</th>
                            <th class="px-4 py-4 text-right">Total</th>
                            <th class="px-4 py-4 text-center">Status Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($orders as $index => $order)
                        <tr class="hover:bg-primary-50/30 transition-colors" :class="selectedOrders.includes({{ $order->id }}) ? 'bg-primary-50/50' : ''">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" 
                                       name="order_ids[]" 
                                       value="{{ $order->id }}"
                                       x-model="selectedOrders"
                                       class="w-5 h-5 text-primary-600 rounded border-gray-300 focus:ring-primary-500 order-checkbox">
                            </td>
                            <td class="px-4 py-4 text-gray-500 text-center text-sm font-medium">{{ $orders->firstItem() + $index }}</td>
                            <td class="px-4 py-4">
                                <span class="font-bold text-gray-900 border-b border-gray-900/20 hover:border-primary-500 hover:text-primary-600 cursor-pointer transition-colors">
                                    {{ $order->order_number }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($order->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm leading-tight">{{ $order->user->name }}</p>
                                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $order->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $order->created_at->format('d M Y') }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $order->created_at->format('H:i') }} WIB</p>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="font-bold text-gray-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @php
                                    $statusLabels = [
                                        'pending' => 'Belum Bayar',
                                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                        'processing' => 'Diproses',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                    $statusColors = [
                                        'pending' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        'menunggu_verifikasi' => 'bg-orange-100 text-orange-700 border-orange-200',
                                        'processing' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'completed' => 'bg-green-100 text-green-700 border-green-200',
                                        'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $currentStatus = $order->status;
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusColors[$currentStatus] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                    {{ $statusLabels[$currentStatus] ?? $currentStatus }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ph ph-package text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada pesanan</h3>
                                    <p class="text-gray-500 mb-4">Belum ada pesanan yang bisa diupdate.</p>
                                    <a href="/admin/pesanan" class="btn-primary">
                                        Ke Halaman Pesanan
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
