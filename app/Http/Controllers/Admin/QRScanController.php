<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\QRCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QRScanController extends Controller
{
    public function __construct(
        private QRCodeService $qrCodeService
    ) {}

    public function index(): View
    {
        // Check if there's a scanned order in session
        $order = null;
        if (session()->has('scanned_order_id')) {
            $order = Order::find(session()->get('scanned_order_id'));
        }

        return view('admin.scan-barcode', compact('order'));
    }

    public function scan(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
        ]);

        $qrData = $validated['qr_data'];

        // Try to get order from QR data
        $order = $this->qrCodeService->getOrderFromQrData($qrData);

        if (! $order) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data QR code tidak valid atau pesanan tidak ditemukan.'], 422);
            }
            return redirect()
                ->back()
                ->with('error', 'Data QR code tidak valid atau pesanan tidak ditemukan.');
        }

        // Validate QR data matches order (only for JSON format)
        $isJson = json_decode($qrData) !== null;
        if ($isJson && ! $this->qrCodeService->isValidOrderData($qrData, $order)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data QR code tidak cocok dengan pesanan.'], 422);
            }
            return redirect()
                ->back()
                ->with('error', 'Data QR code tidak cocok dengan pesanan.');
        }

        // Check if order can be marked as completed
        if ($order->status !== Order::STATUS_PROCESSING) {
            $msg = "Pesanan tidak dapat diselesaikan. Status saat ini: {$order->getStatusLabel()}. Hanya pesanan yang sedang diproses yang dapat diselesaikan.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()
                ->back()
                ->with('error', $msg);
        }

        // Mark order as completed
        $order->updateStatus(
            Order::STATUS_COMPLETED,
            'Pesanan diselesaikan via QR scan',
            auth()->id()
        );

        // Store order ID in session for reference
        session()->flash('order_id', $order->id);
        session()->flash('scanned_order_id', $order->id);
        session()->flash('order_completed', true);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan {$order->order_number} berhasil diselesaikan! Status: Selesai.",
                'order_number' => $order->order_number,
                'order_id' => $order->id,
            ]);
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', "Pesanan {$order->order_number} berhasil diselesaikan! Status: Selesai.");
    }
}
