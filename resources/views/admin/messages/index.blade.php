@extends('layouts.admin')

@section('title', 'Pesan Masuk')

@section('content')
    <div class="p-4 md:p-6 max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pesan Masuk</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pesan dari pengunjung dan pelanggan</p>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <form action="{{ route('admin.messages.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
                    <i class="ph ph-check-circle w-4 h-4"></i>
                    Tandai Semua Dibaca
                </button>
            </form>

            <a href="{{ route('admin.messages.unread') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">
                <i class="ph ph-envelope-simple w-4 h-4"></i>
                Belum Dibaca
            </a>
        </div>

        <!-- Messages List -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            @if($messages->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($messages as $message)
                        <div class="p-4 md:p-5 hover:bg-gray-50 transition-colors {{ !$message->is_read ? 'bg-blue-50/30' : '' }}">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-xl {{ $message->is_read ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center">
                                        <i class="ph ph-envelope{{ $message->is_read ? '' : '-simple' }} w-5 h-5"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ $message->name }}
                                        </p>
                                        @if(!$message->is_read)
                                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded-full">Baru</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 mt-0.5">
                                        {{ $message->subject }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                        {{ Str::limit($message->message, 100) }}
                                    </p>
                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                        <span>{{ $message->email }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $message->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.messages.show', $message->id) }}"
                                        class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                        title="Lihat detail">
                                        <i class="ph ph-eye w-4 h-4"></i>
                                    </a>
                                    @if(!$message->is_read)
                                        <form action="{{ route('admin.messages.mark-read', $message->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                                title="Tandai dibaca">
                                                <i class="ph ph-check w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form id="delete-message-{{ $message->id }}" action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" 
                                        @click="$dispatch('confirm-action', { 
                                            title: 'Hapus Pesan?', 
                                            message: 'Apakah Anda yakin ingin menghapus pesan dari <strong>{{ $message->name }}</strong>?', 
                                            confirmText: 'Ya, Hapus', 
                                            action: () => document.getElementById('delete-message-{{ $message->id }}').submit() 
                                        })"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus">
                                        <i class="ph ph-trash w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ph ph-envelope-slash w-8 h-8 text-gray-400"></i>
                    </div>
                    <p class="text-gray-500">Belum ada pesan masuk</p>
                </div>
            @endif
        </div>
    </div>
@endsection
