<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\PaymentProofService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentProofService $paymentProofService) {}

    public function methods(): JsonResponse
    {
        $methods = PaymentMethod::active()->ordered()->get()->map(fn(PaymentMethod $method) => [
            'id' => $method->id,
            'name' => $method->name,
            'bank_name' => $method->bank_name,
            'account_number' => $method->account_number,
            'account_name' => $method->account_name,
            'logo_url' => $method->getLogoUrl(),
        ]);

        return response()->json($methods);
    }

    public function selectMethod(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403, 'Unauthorized');

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $order->update([
            'payment_method_id' => $request->input('payment_method_id'),
        ]);

        return response()->json([
            'message' => 'Metode pembayaran berhasil dipilih.',
        ]);
    }

    public function uploadProof(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403, 'Unauthorized');

        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        if (empty($order->payment_method_id)) {
            return response()->json([
                'message' => 'Silakan pilih metode pembayaran terlebih dahulu.',
            ], 422);
        }

        $this->paymentProofService->upload(
            $order,
            $request->user(),
            $order->paymentMethod,
            $request->file('proof_image'),
            $request->input('notes')
        );

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diupload. Tim kami akan memverifikasi dalam 1x24 jam.',
        ]);
    }
}
