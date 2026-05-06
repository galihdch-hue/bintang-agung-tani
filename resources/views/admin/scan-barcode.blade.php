@extends('layouts.admin')

@section('title', 'Scan QR Pengambilan')

@section('content')
<div class="p-4 md:p-8 max-w-5xl mx-auto">
    <!-- Header Section -->
    <div class="mb-10">
        <nav class="flex mb-3" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">
                <li>Transaksi</li>
                <li class="flex items-center space-x-2">
                    <i class="ph ph-caret-right text-[10px]"></i>
                    <span class="text-primary-600">Scan QR</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-4xl font-black text-gray-900 tracking-tight font-outfit">Scan QR Pengambilan</h1>
        <p id="instruction-text" class="text-sm text-gray-500 mt-2 font-medium">Arahkan kamera ke QR code pelanggan untuk memproses pengambilan pesanan</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Scanner Main Area -->
        <div class="lg:col-span-2">
            <div class="bg-black rounded-[2.5rem] overflow-hidden shadow-2xl relative aspect-video md:aspect-square lg:aspect-video flex items-center justify-center border-8 border-white group" id="scan-container">
                <!-- Overlay Controls (Floating) -->
                <div class="absolute top-0 left-0 right-0 p-6 z-[110] flex items-center justify-between pointer-events-none">
                    <div class="flex items-center gap-3 pointer-events-auto">
                        <div class="bg-black/40 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse" id="status-pulse"></div>
                            <span class="text-xs font-bold text-white uppercase tracking-widest" id="status-text">Kamera Siap</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pointer-events-auto">
                        <select id="camera-select" class="bg-black/40 backdrop-blur-md text-xs font-bold text-white border border-white/20 rounded-xl px-4 py-2.5 focus:outline-none h-11 appearance-none min-w-[140px]">
                            <option value="">Pilih Kamera...</option>
                        </select>
                    </div>
                </div>

                <!-- Scanner Area -->
                <div class="absolute inset-0 w-full h-full" id="scanner-area">
                    <div id="qr-reader" class="w-full h-full [&_video]:object-cover [&_video]:w-full [&_video]:h-full"></div>
                    
                    <!-- HUD Overlay -->
                    <div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none" id="hud-overlay">
                        <div class="w-64 h-64 md:w-80 md:h-80 relative">
                            <div class="absolute top-0 left-0 w-16 h-16 border-t-4 border-l-4 border-primary-500 rounded-tl-3xl shadow-[0_0_15px_rgba(16,185,129,0.3)]"></div>
                            <div class="absolute top-0 right-0 w-16 h-16 border-t-4 border-r-4 border-primary-500 rounded-tr-3xl shadow-[0_0_15px_rgba(16,185,129,0.3)]"></div>
                            <div class="absolute bottom-0 left-0 w-16 h-16 border-b-4 border-l-4 border-primary-500 rounded-bl-3xl shadow-[0_0_15_rgba(16,185,129,0.3)]"></div>
                            <div class="absolute bottom-0 right-0 w-16 h-16 border-b-4 border-r-4 border-primary-500 rounded-br-3xl shadow-[0_0_15px_rgba(16,185,129,0.3)]"></div>
                            
                            <!-- Scan Line Animation -->
                            <div class="absolute inset-x-8 top-0 h-1 bg-gradient-to-r from-transparent via-primary-400 to-transparent shadow-[0_0_20px_rgba(16,185,129,0.8)] animate-[scan_3s_ease-in-out_infinite]" id="scan-line"></div>
                        </div>
                    </div>
                </div>

                <!-- Status Overlays -->
                <div id="status-overlay" class="absolute inset-0 z-[120] hidden items-center justify-center p-8 backdrop-blur-2xl bg-black/40 transition-all duration-500">
                    <!-- Success State -->
                    <div id="success-state" class="hidden flex flex-col items-center text-center max-w-xs">
                        <div class="w-24 h-24 bg-primary-500 rounded-full flex items-center justify-center mb-6 shadow-2xl animate-bounce">
                            <i class="ph ph-check ph-bold text-4xl text-white"></i>
                        </div>
                        <h2 class="text-3xl font-black text-white mb-2 font-outfit uppercase tracking-tight">Berhasil!</h2>
                        <p class="text-white/80 text-lg mb-8 font-medium" id="order-number-display">ID: BAT-XXXXXX</p>
                        <div class="flex flex-col gap-3 w-full">
                            <a href="#" id="view-order-link" class="bg-white text-black py-4 rounded-2xl font-black uppercase tracking-widest hover:scale-105 transition-transform text-sm">
                                Detail Pesanan
                            </a>
                            <button onclick="resetScanner()" class="bg-white/10 text-white py-4 rounded-2xl font-black uppercase tracking-widest border border-white/20 hover:bg-white/20 transition-all text-sm">
                                Scan Lagi
                            </button>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div id="error-state" class="hidden flex flex-col items-center text-center max-w-xs">
                        <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center mb-6 shadow-2xl">
                            <i class="ph ph-x ph-bold text-4xl text-white"></i>
                        </div>
                        <h2 class="text-3xl font-black text-white mb-2 font-outfit uppercase tracking-tight">Gagal</h2>
                        <p class="text-white/80 text-lg mb-8 font-medium" id="error-message-display">Kode QR tidak valid.</p>
                        <button onclick="resetScanner()" class="w-full bg-white text-black py-4 rounded-2xl font-black uppercase tracking-widest hover:scale-105 transition-transform text-sm">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Scanner Controls Bar -->
            <div class="mt-6 flex items-center justify-center gap-4">
                <button id="start-btn" class="flex-1 max-w-[200px] bg-primary-600 hover:bg-primary-700 text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] transition-all shadow-xl shadow-primary-200 flex items-center justify-center gap-3 active:scale-95">
                    <i class="ph ph-play-fill"></i>
                    Mulai Scan
                </button>
                <button id="stop-btn" class="flex-1 max-w-[200px] bg-red-600 hover:bg-red-700 text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] transition-all shadow-xl shadow-red-200 hidden flex items-center justify-center gap-3 active:scale-95">
                    <i class="ph ph-stop-fill"></i>
                    Berhenti
                </button>
            </div>
        </div>

        <!-- Sidebar / Manual Input -->
        <div class="flex flex-col gap-6">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <i class="ph ph-keyboard text-xl"></i>
                    </div>
                    <h3 class="font-black text-gray-900 uppercase tracking-widest text-sm">Input Manual</h3>
                </div>
                
                <form id="manual-form" class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Kode Pesanan / QR Data</label>
                        <input type="text" name="qr_data" id="manual-input" placeholder="Contoh: BAT-12345"
                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all font-bold">
                    </div>
                    <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-black transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="ph ph-magnifying-glass font-bold"></i>
                        Verifikasi
                    </button>
                </form>
            </div>

            <div class="bg-primary-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-primary-100 relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="font-black text-lg mb-2 font-outfit uppercase tracking-tight">Tips Pindaian</h3>
                    <ul class="space-y-3 text-sm text-white/80 font-medium">
                        <li class="flex items-start gap-2">
                            <i class="ph ph-check-circle-fill mt-1"></i>
                            Pastikan pencahayaan cukup
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="ph ph-check-circle-fill mt-1"></i>
                            Posisikan QR di tengah bingkai
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="ph ph-check-circle-fill mt-1"></i>
                            Hindari pantulan cahaya pada layar
                        </li>
                    </ul>
                </div>
                <i class="ph ph-qr-code absolute -right-8 -bottom-8 text-9xl text-white/10 rotate-12"></i>
            </div>
        </div>
    </div>

    <!-- Hidden Submit Form -->
    <form id="scan-form" action="{{ route('admin.scan-qr.scan') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="qr_data" id="qr-data-input">
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    let isScanning = false;

    // DOM Elements
    const cameraSelect = document.getElementById('camera-select');
    const startBtn = document.getElementById('start-btn');
    const stopBtn = document.getElementById('stop-btn');
    const statusText = document.getElementById('status-text');
    const statusPulse = document.getElementById('status-pulse');
    const instructionText = document.getElementById('instruction-text');
    const statusOverlay = document.getElementById('status-overlay');
    const successState = document.getElementById('success-state');
    const errorState = document.getElementById('error-state');
    const hudOverlay = document.getElementById('hud-overlay');

    // Init Cameras
    async function initCameras() {
        if (typeof Html5Qrcode === 'undefined') {
            console.error('Html5Qrcode library not loaded');
            instructionText.textContent = 'Gagal memuat library scanner.';
            return;
        }

        try {
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                cameraSelect.innerHTML = '<option value="">Pilih Kamera...</option>';
                devices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.id;
                    option.text = device.label || `Kamera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });
                cameraSelect.value = devices[0].id;
            } else {
                instructionText.textContent = 'Tidak ada kamera ditemukan.';
            }
        } catch (err) {
            console.error('Error init cameras:', err);
            instructionText.textContent = 'Akses kamera gagal: ' + err.message;
        }
    }

    // Start
    async function startScanning() {
        const cameraId = cameraSelect.value;
        if (!cameraId) return alert('Pilih kamera!');

        if (typeof Html5Qrcode === 'undefined') {
            return alert('Scanner library belum siap. Silakan refresh halaman.');
        }

        try {
            if (html5QrCode) {
                await html5QrCode.stop().catch(() => {});
            }

            html5QrCode = new Html5Qrcode("qr-reader");
            await html5QrCode.start(
                cameraId,
                {
                    fps: 15,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        let qrboxSize = Math.floor(minEdge * 0.7);
                        return { width: qrboxSize, height: qrboxSize };
                    },
                    aspectRatio: 1.0 // Force square for consistency
                },
                onScanSuccess,
                onScanFailure
            );

            isScanning = true;
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            cameraSelect.disabled = true;
            statusText.textContent = 'Memindai...';
            statusPulse.className = 'w-2 h-2 rounded-full bg-green-500 animate-pulse';
            instructionText.textContent = 'Pastikan QR berada dalam bingkai';
        } catch (err) {
            console.error('Start failed:', err);
            alert('Gagal Memulai Kamera: ' + err.message);
        }
    }

    // Stop
    async function stopScanning() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
            } catch (err) {
                console.warn('Stop failed (probably already stopped):', err);
            }
            isScanning = false;
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            cameraSelect.disabled = false;
            statusText.textContent = 'Kamera Siap';
            statusPulse.className = 'w-2 h-2 rounded-full bg-red-500 animate-pulse';
        }
    }

    function onScanSuccess(decodedText) {
        stopScanning();
        submitQRData(decodedText);
    }

    function onScanFailure(err) {}

    async function submitQRData(qrData) {
        const form = document.getElementById('scan-form');
        const input = document.getElementById('qr-data-input');
        input.value = qrData;
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (response.redirected) {
                showSuccess(qrData);
                setTimeout(() => window.location.href = response.url, 1200);
            } else {
                const result = await response.json();
                if (result.success) {
                    showSuccess(result.order_number);
                } else {
                    showError(result.message);
                }
            }
        } catch (err) {
            showError('Server Error');
        }
    }

    function showSuccess(id) {
        statusOverlay.classList.remove('hidden');
        statusOverlay.classList.add('flex');
        successState.classList.remove('hidden');
        hudOverlay.classList.add('hidden');
        document.getElementById('order-number-display').textContent = 'ID: ' + id;
    }

    function showError(msg) {
        statusOverlay.classList.remove('hidden');
        statusOverlay.classList.add('flex');
        errorState.classList.remove('hidden');
        hudOverlay.classList.add('hidden');
        document.getElementById('error-message-display').textContent = msg;
    }

    function resetScanner() {
        statusOverlay.classList.add('hidden');
        statusOverlay.classList.remove('flex');
        successState.classList.add('hidden');
        errorState.classList.add('hidden');
        hudOverlay.classList.remove('hidden');
        document.getElementById('manual-input').value = '';
    }

    startBtn.addEventListener('click', startScanning);
    stopBtn.addEventListener('click', stopScanning);
    document.getElementById('manual-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const val = document.getElementById('manual-input').value.trim();
        if (val) submitQRData(val);
    });

    document.addEventListener('DOMContentLoaded', initCameras);
</script>
@endsection
