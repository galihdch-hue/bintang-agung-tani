@extends('layouts.admin')

@section('title', 'Scan QR Pengambilan')

@section('content')
<div class="p-4 md:p-8 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Scan QR Pengambilan</h1>
        <p id="instruction-text" class="text-sm text-gray-500 mt-1">Arahkan kamera ke QR code pelanggan untuk memproses pengambilan pesanan</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Scanner -->
        <div class="lg:col-span-2">
            <div class="bg-black rounded-xl overflow-hidden relative aspect-video flex items-center justify-center" id="scan-container">
                <!-- Controls -->
                <div class="absolute top-0 left-0 right-0 p-4 z-[110] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="bg-black/60 px-3 py-1.5 rounded-lg flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse" id="status-pulse"></div>
                            <span class="text-xs font-medium text-white" id="status-text">Kamera Siap</span>
                        </div>
                    </div>
                    <select id="camera-select" class="bg-black/60 text-xs text-white border border-white/20 rounded-lg px-3 py-1.5 focus:outline-none">
                        <option value="">Pilih Kamera...</option>
                    </select>
                </div>

                <!-- Scanner Area -->
                <div class="absolute inset-0" id="scanner-area">
                    <div id="qr-reader" class="w-full h-full [&_video]:object-cover [&_video]:w-full [&_video]:h-full"></div>
                    
                    <!-- HUD -->
                    <div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none" id="hud-overlay">
                        <div class="w-48 h-48 md:w-64 md:h-64 relative">
                            <div class="absolute top-0 left-0 w-10 h-10 border-t-2 border-l-2 border-primary-500 rounded-tl-lg"></div>
                            <div class="absolute top-0 right-0 w-10 h-10 border-t-2 border-r-2 border-primary-500 rounded-tr-lg"></div>
                            <div class="absolute bottom-0 left-0 w-10 h-10 border-b-2 border-l-2 border-primary-500 rounded-bl-lg"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 border-b-2 border-r-2 border-primary-500 rounded-br-lg"></div>
                            <div class="absolute inset-x-4 top-0 h-0.5 bg-primary-400 shadow-[0_0_10px_rgba(16,185,129,0.6)] animate-[scan_3s_ease-in-out_infinite]" id="scan-line"></div>
                        </div>
                    </div>
                </div>

                <!-- Status Overlay -->
                <div id="status-overlay" class="absolute inset-0 z-[120] hidden items-center justify-center p-8 bg-black/60">
                    <div id="success-state" class="hidden flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-primary-500 rounded-full flex items-center justify-center mb-4">
                            <i class="ph ph-check ph-bold text-2xl text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-1">Berhasil!</h2>
                        <p class="text-white/70 text-sm mb-6" id="order-number-display">ID: BAT-XXXXXX</p>
                        <div class="flex flex-col gap-2 w-full max-w-xs">
                            <a href="#" id="view-order-link" class="bg-white text-gray-900 py-3 rounded-lg font-semibold text-sm text-center">
                                Detail Pesanan
                            </a>
                            <button onclick="resetScanner()" class="bg-white/10 text-white py-3 rounded-lg text-sm border border-white/20">
                                Scan Lagi
                            </button>
                        </div>
                    </div>
                    <div id="error-state" class="hidden flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mb-4">
                            <i class="ph ph-x ph-bold text-2xl text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-1">Gagal</h2>
                        <p class="text-white/70 text-sm mb-6" id="error-message-display">Kode QR tidak valid.</p>
                        <button onclick="resetScanner()" class="w-full max-w-xs bg-white text-gray-900 py-3 rounded-lg font-semibold text-sm">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="mt-4 flex items-center justify-center gap-3">
                <button id="start-btn" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <i class="ph ph-play-fill"></i>
                    Mulai Scan
                </button>
                <button id="stop-btn" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-medium transition-colors hidden flex items-center gap-2">
                    <i class="ph ph-stop-fill"></i>
                    Berhenti
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="flex flex-col gap-4">
            <div class="bg-white p-6 rounded-xl border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-4">Input Manual</h3>
                <form id="manual-form" class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Kode Pesanan / QR Data</label>
                        <input type="text" name="qr_data" id="manual-input" placeholder="Contoh: BAT-12345"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/10">
                    </div>
                    <button type="submit" class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-medium hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
                        <i class="ph ph-magnifying-glass"></i>
                        Verifikasi
                    </button>
                </form>
            </div>

            <div class="bg-primary-50 p-6 rounded-xl border border-primary-100">
                <h3 class="font-semibold text-gray-900 mb-3">Tips Pindaian</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="ph ph-check-circle text-primary-600 mt-0.5"></i>
                        Pastikan pencahayaan cukup
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ph ph-check-circle text-primary-600 mt-0.5"></i>
                        Posisikan QR di tengah bingkai
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ph ph-check-circle text-primary-600 mt-0.5"></i>
                        Hindari pantulan cahaya pada layar
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Hidden Form -->
    <form id="scan-form" action="{{ route('admin.scan-qr.scan') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="qr_data" id="qr-data-input">
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    let isScanning = false;

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

    async function initCameras() {
        if (typeof Html5Qrcode === 'undefined') {
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
            instructionText.textContent = 'Akses kamera gagal: ' + err.message;
        }
    }

    async function startScanning() {
        const cameraId = cameraSelect.value;
        if (!cameraId) return alert('Pilih kamera!');
        if (typeof Html5Qrcode === 'undefined') return alert('Scanner library belum siap.');

        try {
            if (html5QrCode) await html5QrCode.stop().catch(() => {});
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
                    aspectRatio: 1.0
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
            alert('Gagal Memulai Kamera: ' + err.message);
        }
    }

    async function stopScanning() {
        if (html5QrCode && isScanning) {
            try { await html5QrCode.stop(); } catch (err) {}
            isScanning = false;
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            cameraSelect.disabled = false;
            statusText.textContent = 'Kamera Siap';
            statusPulse.className = 'w-2 h-2 rounded-full bg-red-500 animate-pulse';
        }
    }

    let isProcessing = false;

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;
        stopScanning();
        let cleanData = decodedText.trim();
        if (cleanData.startsWith('{') || cleanData.startsWith('%7B')) {
            try { cleanData = decodeURIComponent(cleanData); } catch(e) {}
        }
        statusText.textContent = 'QR Terdeteksi!';
        statusPulse.className = 'w-2 h-2 rounded-full bg-blue-500 animate-pulse';
        submitQRData(cleanData);
    }

    function onScanFailure(err) {}

    async function submitQRData(qrData) {
        const form = document.getElementById('scan-form');
        document.getElementById('qr-data-input').value = qrData;
        try {
            const token = document.querySelector('input[name="_token"]').value;
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: qrData })
            });
            let result;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                result = { success: false, message: 'Server error' };
            }
            if (result.success) {
                showSuccess(result.order_number || qrData);
                const viewLink = document.getElementById('view-order-link');
                if (viewLink && result.order_id) viewLink.href = '/admin/pesanan/' + result.order_id;
            } else {
                showError(result.message || 'QR code tidak valid.');
            }
        } catch (err) {
            showError('Koneksi gagal: ' + err.message);
        } finally {
            isProcessing = false;
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
        isProcessing = false;
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
