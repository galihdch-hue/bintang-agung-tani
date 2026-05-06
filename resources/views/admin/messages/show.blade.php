@extends('layouts.admin')

@section('title', 'Detail Pesan')

@section('content')
    <div class="p-4 md:p-6 max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.messages.index') }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="ph ph-arrow-left w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pesan</h1>
                <p class="text-sm text-gray-500 mt-1">Pesan dari {{ $message->name }}</p>
            </div>
        </div>

        <!-- Message Detail -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            <div class="p-6">
                <!-- Sender Info -->
                <div class="flex items-start gap-4 pb-6 border-b border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                        <i class="ph ph-user w-6 h-6"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-lg font-bold text-gray-900">{{ $message->name }}</p>
                        <p class="text-sm text-gray-500">{{ $message->email }}</p>
                        <p class="text-xs text-gray-400 mt-1">Dikirim {{ $message->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @if(!$message->is_read)
                        <span class="px-2 py-1 text-xs font-semibold bg-amber-100 text-amber-700 rounded-full">Baru</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Dibaca</span>
                    @endif
                </div>

                <!-- Message Content -->
                <div class="py-6">
                    <p class="text-sm font-medium text-gray-700 mb-3">{{ $message->subject }}</p>
                    <div class="prose prose-sm max-w-none text-gray-600 whitespace-pre-wrap">{{ $message->message }}</div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                    <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
                        <i class="ph ph-reply w-4 h-4"></i>
                        Balas via Email
                    </a>

                    <form id="delete-message-form" action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" 
                            @click="$dispatch('confirm-action', { 
                                title: 'Hapus Pesan?', 
                                message: 'Apakah Anda yakin ingin menghapus pesan ini?', 
                                confirmText: 'Ya, Hapus', 
                                action: () => document.getElementById('delete-message-form').submit() 
                            })"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-red-600 rounded-xl hover:bg-red-50 transition-colors text-sm font-medium">
                        <i class="ph ph-trash w-4 h-4"></i>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
