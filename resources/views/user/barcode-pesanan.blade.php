@extends('layouts.app')

@section('title', 'E-Ticket - ' . ($order->order_number ?? 'Pesanan'))

@section('content')
<!-- Import Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50/50">
    <div class="max-w-md w-full perspective-1000">
        <!-- Main Ticket Container -->
        <div class="relative bg-white rounded-[3rem] shadow-[0_20px_70px_-15px_rgba(0,0,0,0.1)] overflow-hidden border border-gray-100/50 flex flex-col transform-style-3d hover:shadow-[0_30px_100px_-20px_rgba(0,0,0,0.15)] transition-all duration-700">
            
            <!-- Header Section (Premium Gradient) -->
            <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 p-10 text-center text-white relative overflow-hidden">
                <!-- Glossy Overlay -->
                <div class="absolute top-0 left-0 w-full h-full bg-white/10 skew-y-12 translate-y-[-50%] pointer-events-none"></div>
                
                <!-- Brand Accent -->
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-xl rounded-3xl mb-6 ring-1 ring-white/30 shadow-2xl animate-float">
                    <i class="ph ph-qr-code text-white text-4xl"></i>
                </div>
                
                <h1 class="text-3xl font-black mb-2 tracking-tight font-outfit">E-TICKET</h1>
                <p class="text-white/70 text-sm font-medium tracking-wide font-outfit uppercase">Tunjukkan kode untuk pengambilan</p>
                
                <!-- Floating Orbs -->
                <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/5 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
            </div>

            <!-- Main Body (White Section) -->
            <div class="px-10 py-12 text-center bg-white relative">
                <!-- ID Section -->
                <div class="mb-10 group relative">
                    <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-3 font-outfit">Transaction ID</span>
                    <div class="inline-flex items-center gap-4 bg-gray-50/80 backdrop-blur-sm border border-gray-100 px-8 py-4 rounded-[2rem] hover:border-primary-200 hover:bg-primary-50/50 transition-all duration-500 cursor-pointer group/id"
                         onclick="navigator.clipboard.writeText('{{ $order->order_number }}'); alert('ID Pesanan disalin!')">
                        <span class="text-2xl font-black text-gray-900 tracking-wider font-outfit">{{ $order->order_number }}</span>
                        <div class="w-10 h-10 rounded-2xl bg-white border border-gray-200 flex items-center justify-center shadow-sm group-hover/id:scale-110 group-hover/id:shadow-md transition-all">
                            <i class="ph ph-copy ph-bold text-gray-400 group-hover/id:text-primary-600"></i>
                        </div>
                    </div>
                </div>

                <!-- QR Code Visualization -->
                <div class="relative mx-auto w-64 h-64 mb-10 group">
                    <!-- Glass Background -->
                    <div class="absolute inset-0 bg-gray-50 rounded-[3rem] -rotate-3 scale-[1.02] opacity-50 group-hover:rotate-0 group-hover:scale-105 transition-all duration-700"></div>
                    
                    <div class="relative w-full h-full bg-white p-6 rounded-[3rem] shadow-[0_15px_40px_-10px_rgba(0,0,0,0.1)] border border-gray-100 flex items-center justify-center z-10 group-hover:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.15)] transition-all duration-500">
                        <!-- Scanning Animation -->
                        <div class="absolute inset-6 border-t-2 border-primary-500 rounded-full opacity-0 group-hover:opacity-100 animate-scan-premium z-20 pointer-events-none"></div>
                        
                        <!-- Corner Markers -->
                        <div class="absolute top-6 left-6 w-10 h-10 border-t-4 border-l-4 border-primary-600/30 rounded-tl-3xl z-20 group-hover:border-primary-600 transition-all duration-500"></div>
                        <div class="absolute top-6 right-6 w-10 h-10 border-t-4 border-r-4 border-primary-600/30 rounded-tr-3xl z-20 group-hover:border-primary-600 transition-all duration-500"></div>
                        <div class="absolute bottom-6 left-6 w-10 h-10 border-b-4 border-l-4 border-primary-600/30 rounded-bl-3xl z-20 group-hover:border-primary-600 transition-all duration-500"></div>
                        <div class="absolute bottom-6 right-6 w-10 h-10 border-b-4 border-r-4 border-primary-600/30 rounded-br-3xl z-20 group-hover:border-primary-600 transition-all duration-500"></div>

                        @if($order && $order->qr_code_path)
                            <img src="{{ $order->getQrCodeUrl() }}" alt="QR" class="w-full h-full object-contain relative z-10 filter group-hover:brightness-105 transition-all">
                        @else
                            <div class="flex flex-col items-center opacity-30">
                                <i class="ph ph-qr-code text-6xl mb-2"></i>
                                <span class="text-[10px] font-black uppercase tracking-tighter">No QR Available</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-gray-50/50 border border-gray-100 p-5 rounded-[2rem] text-left hover:bg-white hover:shadow-xl hover:shadow-gray-100 transition-all group">
                        <div class="w-8 h-8 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="ph ph-user-circle ph-bold text-lg"></i>
                        </div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 font-outfit">Pelanggan</span>
                        <span class="text-xs font-black text-gray-900 truncate block">{{ $order->user->name ?? 'User' }}</span>
                    </div>
                    <div class="bg-gray-50/50 border border-gray-100 p-5 rounded-[2rem] text-left hover:bg-white hover:shadow-xl hover:shadow-gray-100 transition-all group">
                        <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="ph ph-shopping-cart ph-bold text-lg"></i>
                        </div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 font-outfit">Total Item</span>
                        <span class="text-xs font-black text-gray-900 block">{{ count($order->items ?? []) }} Produk</span>
                    </div>
                </div>

                <!-- Status Banner -->
                <div class="flex items-center justify-between bg-primary-50/50 border border-primary-100/30 p-5 rounded-[2rem]">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-3 h-3 bg-primary-500 rounded-full animate-ping"></div>
                            <div class="absolute inset-0 w-3 h-3 bg-primary-600 rounded-full"></div>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-primary-600/60 uppercase tracking-widest font-outfit">Status Pesanan</span>
                            <span class="text-sm font-black text-primary-900 uppercase font-outfit">{{ $order->getStatusLabel() }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-[9px] font-bold text-primary-600/60 uppercase tracking-widest font-outfit">Tanggal</span>
                        <span class="text-sm font-black text-primary-900 font-outfit">{{ $order->created_at->format('d/m/y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Perforation Bridge -->
            <div class="relative h-10 bg-white flex items-center justify-center">
                <div class="absolute -left-5 w-10 h-10 bg-gray-50/50 rounded-full border border-gray-100/50 shadow-inner z-20"></div>
                <div class="w-full border-t-2 border-dashed border-gray-200 px-6"></div>
                <div class="absolute -right-5 w-10 h-10 bg-gray-50/50 rounded-full border border-gray-100/50 shadow-inner z-20"></div>
            </div>

            <!-- Action Section -->
            <div class="p-10 bg-gray-50/50 flex flex-col gap-4">
                <div class="flex gap-4">
                    @if($order && $order->qr_code_path)
                        <a href="{{ route('user.payments.download-qr', $order) }}" 
                           class="flex-1 bg-white border-2 border-gray-100 hover:border-primary-200 py-4 rounded-[1.5rem] flex items-center justify-center gap-2 group transition-all hover:shadow-lg hover:shadow-gray-200">
                            <i class="ph ph-download-simple ph-bold text-lg text-gray-400 group-hover:text-primary-600 transition-colors"></i>
                            <span class="text-xs font-black text-gray-600 group-hover:text-primary-900 transition-colors">SAVE</span>
                        </a>
                    @endif
                    <a href="{{ route('user.orders.show', $order) }}" 
                       class="flex-[2] bg-primary-600 hover:bg-primary-700 py-4 rounded-[1.5rem] flex items-center justify-center gap-3 shadow-xl shadow-primary-200 hover:shadow-primary-300 transform hover:-translate-y-1 transition-all group">
                        <i class="ph ph-clipboard-text ph-bold text-lg text-white/90"></i>
                        <span class="text-xs font-black text-white tracking-widest uppercase font-outfit">Order Details</span>
                    </a>
                </div>
                
                <a href="{{ route('user.dashboard') }}" class="text-center group py-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] group-hover:text-primary-600 transition-colors">Back to Dashboard</span>
                </a>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-8 flex items-center justify-center gap-2 text-gray-400 px-6">
            <i class="ph ph-shield-check text-lg opacity-50"></i>
            <p class="text-[10px] font-medium leading-tight">Keamanan data Anda terjaga. Barcode ini hanya valid untuk satu kali proses pengambilan di toko resmi kami.</p>
        </div>
    </div>
</div>

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
    .perspective-1000 {
        perspective: 1000px;
    }
    .transform-style-3d {
        transform-style: preserve-3d;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0); }
        50% { transform: translateY(-10px) rotate(2deg); }
    }
    .animate-float {
        animation: float 5s ease-in-out infinite;
    }
    @keyframes scan-premium {
        0% { transform: translateY(0); opacity: 0; }
        20% { opacity: 1; }
        80% { opacity: 1; }
        100% { transform: translateY(180px); opacity: 0; }
    }
    .animate-scan-premium {
        animation: scan-premium 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
</style>
@endsection
