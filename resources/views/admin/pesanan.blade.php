@extends('layouts.admin')

@section('title', 'Pesanan Masuk')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Manajemen Pesanan</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Pesanan Pelanggan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pesanan masuk dan perbarui status utama pesanan secara konsisten.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-download-simple ph-bold w-4 h-4"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-orange-500 bg-orange-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">Menunggu Verifikasi</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $stats['verification'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="ph ph-receipt ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-blue-500 bg-blue-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Diproses</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $stats['processing'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="ph ph-gear-six ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-red-500 bg-red-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Dibatalkan</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $stats['cancelled'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i class="ph ph-x-circle ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-green-500 bg-green-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-1">Selesai (Bulan Ini)</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $stats['completed_this_month'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="ph ph-check-circle ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="card p-0 overflow-hidden w-full border-primary-100">

        <!-- Toolbar -->
        <div class="p-5 border-b border-primary-100 flex flex-col md:flex-row gap-4 justify-between md:items-center bg-white">
            <div class="flex items-center gap-3">
                <div class="relative min-w-[280px]">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="text" placeholder="Cari ID Pesanan / Nama Pelanggan..." 
                           class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                </div>
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
            <table class="w-full text-left text-sm whitespace-nowrap" style="min-width: 1000px;">
                <thead class="bg-gradient-to-r from-primary-50/60 to-primary-50/30 text-primary-700 text-xs uppercase font-bold tracking-wider border-b-2 border-primary-100">
                    <tr>
                        <th class="px-4 py-4 w-12 text-center">No</th>
                        <th class="px-4 py-4 min-w-[120px]">ID Pesanan</th>
                        <th class="px-4 py-4 min-w-[160px]">Pelanggan</th>
                        <th class="px-4 py-4 min-w-[100px]">Tanggal</th>
                        <th class="px-4 py-4 text-right min-w-[120px]">Total</th>
                        <th class="px-4 py-4 text-center min-w-[100px]">Status</th>
                        <th class="px-4 py-4 text-right min-w-[200px]">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $index => $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-4 text-gray-500 text-center text-sm font-medium">{{ $orders->firstItem() + $index }}</td>
                        <td class="px-4 py-4">
                            <span class="font-bold text-gray-900 border-gray-900/20 hover:border-primary-500 hover:text-primary-600 border-b pb-0.5 cursor-pointer transition-colors">
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
                            <span class="inline-flex px-2.5 py-1 text-[11px] font-bold rounded-full {{ $order->getStatusBadgeClass() }} border">
                                {{ $order->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ showActions: false }">
                                <!-- View Detail -->
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn-secondary h-8 w-8 p-0 rounded-lg flex items-center justify-center text-gray-500 hover:text-primary-600 hover:bg-primary-50 border-gray-200 shrink-0" title="Lihat Detail">
                                    <i class="ph ph-eye ph-bold w-4 h-4"></i>
                                </a>
                                
                                <!-- Quick Actions Based on Status -->
                                @if($order->status === 'menunggu_verifikasi')
                                    <a href="{{ route('admin.verifikasi.index') }}?order={{ $order->id }}" class="btn-primary h-8 px-2 text-xs border-orange-500 bg-orange-500 hover:bg-orange-600 hover:border-orange-600 shadow-sm focus:ring-orange-500/30 flex items-center gap-1 whitespace-nowrap">
                                        <i class="ph ph-receipt w-3.5 h-3.5"></i> Verifikasi
                                    </a>
                                @elseif($order->status === 'processing')
                                @elseif($order->status === 'processing')
                                    <form id="complete-order-{{ $order->id }}" action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                    </form>
                                    <button type="button" 
                                            @click="$dispatch('confirm-action', { 
                                                title: 'Selesaikan Pesanan?', 
                                                message: 'Apakah Anda yakin ingin menandai pesanan <strong>{{ $order->order_number }}</strong> sebagai selesai?', 
                                                confirmText: 'Ya, Selesai', 
                                                isDestructive: false,
                                                action: () => document.getElementById('complete-order-{{ $order->id }}').submit() 
                                            })"
                                            class="btn-primary h-8 px-2 text-xs border-green-600 bg-green-600 hover:bg-green-700 hover:border-green-700 shadow-sm focus:ring-green-500/30 flex items-center gap-1 whitespace-nowrap">
                                        <i class="ph ph-check-circle w-3.5 h-3.5"></i> Selesai
                                    </button>
                                @elseif($order->status === 'completed')
                                    <button class="btn-secondary h-8 px-2 text-xs bg-gray-50 text-gray-400 border-transparent cursor-default pointer-events-none flex items-center gap-1 whitespace-nowrap">
                                        <i class="ph ph-check-circle ph-fill w-3.5 h-3.5 text-green-500"></i> Berhasil
                                    </button>
                                @endif
                                
                                <!-- More Actions Dropdown -->
                                <div class="relative">
                                    <button @click="showActions = !showActions" @click.away="showActions = false" class="btn-secondary h-8 w-8 p-0 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 border-gray-200" title="Aksi Lainnya">
                                        <i class="ph ph-dots-three-vertical ph-bold w-4 h-4"></i>
                                    </button>
                                    
                                    <div x-show="showActions" x-cloak x-transition class="absolute right-0 top-full mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 py-2">
                                        <!-- QR Code -->
                                        <a href="{{ route('user.payments.qr-code', $order) }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="ph ph-qr-code w-4 h-4 text-primary-600"></i>
                                            <span>Lihat QR Code</span>
                                        </a>
                                        
                                        <!-- Print Invoice -->
                                        <a href="{{ route('admin.orders.show', $order) }}?print=true" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="ph ph-printer w-4 h-4 text-gray-500"></i>
                                            <span>Cetak Invoice</span>
                                        </a>
                                        
                                        <!-- Update Status -->
                                        @if(in_array($order->status, ['menunggu_verifikasi', 'processing']))
                                            <button onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors text-left">
                                                <i class="ph ph-arrows-clockwise w-4 h-4 text-blue-600"></i>
                                                <span>Update Status</span>
                                            </button>
                                        @endif
                                        
                                        <hr class="my-2 border-gray-100">
                                        
                                        <!-- Cancel Order -->
                                        @if($order->canBeCancelled())
                                            <form id="cancel-order-{{ $order->id }}" action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="hidden">
                                                @csrf
                                            </form>
                                            <button type="button" 
                                                    @click="$dispatch('confirm-action', { 
                                                        title: 'Batalkan Pesanan?', 
                                                        message: 'Apakah Anda yakin ingin membatalkan pesanan <strong>{{ $order->order_number }}</strong>?', 
                                                        confirmText: 'Ya, Batalkan', 
                                                        action: () => document.getElementById('cancel-order-{{ $order->id }}').submit() 
                                                    })"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                                <i class="ph ph-x-circle w-4 h-4"></i>
                                                <span>Batalkan Pesanan</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <i class="ph ph-package w-12 h-12 text-gray-300"></i>
                                <p class="text-sm font-medium">Tidak ada pesanan ditemukan</p>
                                <p class="text-xs text-gray-400">Coba ubah filter pencarian Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gradient-to-r from-primary-50/30 to-primary-50/10 border-t border-primary-100 p-5 flex w-full">
            {{ $orders->links('vendor.pagination.custom') }}
        </div>
    </div>

</div>

<!-- Modal for Status Update -->
<div id="statusModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeStatusModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                <h3 class="text-lg font-bold text-white">Update Status Pesanan</h3>
                <p class="text-primary-100 text-sm">Pilih status baru untuk pesanan ini</p>
            </div>
            <form id="statusForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="order_id" id="statusOrderId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Baru</label>
                    <select name="status" id="statusSelect" required class="form-input w-full h-11">
                        <option value="">Pilih Status</option>
                        <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" class="form-input w-full" placeholder="Tambahkan catatan untuk perubahan status..."></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeStatusModal()" class="flex-1 btn-secondary h-11">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 btn-primary h-11">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTrackingModal(orderId) {
    document.getElementById('trackingOrderId').value = orderId;
    document.getElementById('trackingForm').action = `/admin/orders/${orderId}/tracking`;
    document.getElementById('trackingModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTrackingModal() {
    document.getElementById('trackingModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('trackingForm').reset();
}

function openStatusModal(orderId, currentStatus) {
    document.getElementById('statusOrderId').value = orderId;
    document.getElementById('statusForm').action = `/admin/orders/${orderId}/status`;
    
    // Set current status in dropdown
    const statusSelect = document.getElementById('statusSelect');
    statusSelect.value = '';
    
    // Disable current status option
    Array.from(statusSelect.options).forEach(option => {
        option.disabled = option.value === currentStatus;
    });
    
    document.getElementById('statusModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('statusForm').reset();
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTrackingModal();
        closeStatusModal();
    }
});
</script>
@endsection
