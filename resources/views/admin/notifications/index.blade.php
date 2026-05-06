@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
    <div class="p-4 md:p-8 max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">
                        <li>System</li>
                        <li class="flex items-center space-x-2">
                            <i class="ph ph-caret-right text-[10px]"></i>
                            <span class="text-primary-600">Notifikasi</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight font-outfit">Notifikasi</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Pusat informasi aktivitas sistem dan pesanan terbaru</p>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-gray-100 text-gray-700 rounded-2xl hover:bg-gray-50 hover:border-primary-100 hover:text-primary-600 transition-all text-sm font-black shadow-sm">
                        <i class="ph ph-check-circle w-5 h-5"></i>
                        Baca Semua
                    </button>
                </form>

                <a href="{{ route('admin.notifications.unread') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-2xl hover:bg-primary-700 hover:scale-[1.02] transition-all text-sm font-black shadow-xl shadow-primary-200">
                    <i class="ph ph-bell-simple-ringing w-5 h-5"></i>
                    Belum Dibaca
                </a>
            </div>
        </div>

        <!-- Notification Feed -->
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.05)] overflow-hidden">
            @if($notifications->count() > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($notifications as $notification)
                        <div class="group relative p-6 md:p-8 hover:bg-gray-50/50 transition-all duration-300 {{ $notification->unread() ? 'bg-primary-50/10' : '' }}">
                            <!-- New Badge Marker -->
                            @if($notification->unread())
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary-500 rounded-r-full"></div>
                            @endif

                            <div class="flex items-start gap-6">
                                <!-- Icon / Avatar -->
                                <div class="flex-shrink-0 relative">
                                    @php
                                        $icon = $notification->data['icon'] ?? 'ph-bell';
                                        $isOrder = str_contains(strtolower($notification->data['title'] ?? ''), 'pesanan') || str_contains(strtolower($notification->data['message'] ?? ''), 'order');
                                    @endphp
                                    <div class="w-14 h-14 rounded-2xl {{ $notification->unread() ? ($isOrder ? 'bg-amber-100 text-amber-600' : 'bg-primary-100 text-primary-600') : 'bg-gray-100 text-gray-400' }} flex items-center justify-center transition-all group-hover:rotate-12">
                                        <i class="ph {{ $icon }} text-2xl"></i>
                                    </div>
                                    @if($notification->unread())
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full animate-pulse"></span>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <h3 class="text-lg font-black text-gray-900 tracking-tight font-outfit">{{ $notification->data['title'] ?? 'System Update' }}</h3>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed max-w-2xl">
                                        {{ $notification->data['message'] ?? 'Informasi sistem terbaru telah diterima.' }}
                                    </p>
                                    
                                    @php
                                        $actionUrl = $notification->data['action_url'] ?? $notification->data['url'] ?? null;
                                    @endphp
                                    @if($actionUrl)
                                        <div class="mt-4">
                                            <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 text-xs font-black text-primary-600 hover:text-primary-800 transition-colors uppercase tracking-widest">
                                                Detail Lengkap
                                                <i class="ph ph-arrow-right"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Inline Actions -->
                                <div class="flex items-center gap-2 md:opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    @if($notification->unread())
                                        <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="w-10 h-10 flex items-center justify-center bg-white border border-gray-100 text-gray-400 hover:text-primary-600 hover:border-primary-200 hover:shadow-sm rounded-xl transition-all"
                                                title="Tandai Dibaca">
                                                <i class="ph ph-check-bold text-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button" 
                                        @click="$dispatch('confirm-action', { 
                                            title: 'Hapus Notifikasi?', 
                                            message: 'Apakah Anda yakin ingin menghapus notifikasi ini secara permanen?', 
                                            confirmText: 'Ya, Hapus', 
                                            action: () => document.getElementById('delete-notif-{{ $notification->id }}').submit() 
                                        })"
                                        class="w-10 h-10 flex items-center justify-center bg-white border border-gray-100 text-gray-400 hover:text-red-600 hover:border-red-200 hover:shadow-sm rounded-xl transition-all"
                                        title="Hapus">
                                        <i class="ph ph-trash-simple text-lg"></i>
                                    </button>
                                    
                                    <form id="delete-notif-{{ $notification->id }}" action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="py-24 text-center">
                    <div class="relative inline-block mb-8">
                        <div class="w-32 h-32 bg-gray-50 rounded-[3rem] flex items-center justify-center mx-auto ring-8 ring-gray-50/50">
                            <i class="ph ph-bell-simple-slash text-5xl text-gray-300"></i>
                        </div>
                        <div class="absolute -right-2 -top-2 w-12 h-12 bg-white rounded-2xl shadow-xl flex items-center justify-center border border-gray-50 animate-bounce">
                            <i class="ph ph-check-circle-fill text-emerald-500 text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2 tracking-tight font-outfit">Semua Beres!</h3>
                    <p class="text-gray-500 font-medium max-w-sm mx-auto">Tidak ada notifikasi baru untuk saat ini. Kotak masuk Anda bersih dan teratur.</p>
                </div>
            @endif
        </div>

        <!-- Global Cleanup -->
        @if($notifications->count() > 0)
            <div class="mt-10 flex justify-center">
                <button type="button"
                    @click="$dispatch('confirm-action', { 
                        title: 'Bersihkan Semua?', 
                        message: 'Tindakan ini akan menghapus seluruh riwayat notifikasi secara permanen. Lanjutkan?', 
                        confirmText: 'Ya, Bersihkan Riwayat', 
                        action: () => document.getElementById('delete-all-notifications').submit() 
                    })"
                    class="group flex items-center gap-2 text-[10px] font-black text-gray-400 hover:text-red-600 uppercase tracking-[0.2em] transition-all">
                    <i class="ph ph-broom text-xl transition-transform group-hover:-rotate-12"></i>
                    Bersihkan Semua Riwayat
                </button>
                
                <form id="delete-all-notifications" action="{{ route('admin.notifications.delete-all') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @endif
    </div>
@endsection
