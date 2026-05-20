<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Services\PaymentProofService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function __construct(
        private PaymentProofService $paymentProofService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $query = PaymentProof::with(['order', 'user' => function($q) {
            $q->withTrashed();
        }, 'paymentMethod'])
            ->orderBy('created_at', 'desc');

        if (in_array($status, ['pending', 'verified', 'rejected'])) {
            $statusMap = [
                'pending' => PaymentProof::STATUS_PENDING,
                'verified' => PaymentProof::STATUS_VERIFIED,
                'rejected' => PaymentProof::STATUS_REJECTED,
            ];
            $query->where('status', $statusMap[$status]);
        }

        $payments = $query->paginate(20);

        return view('admin.verifikasi', compact('payments', 'status'));
    }

    public function show(PaymentProof $payment): View
    {
        $payment->load(['order', 'order.items', 'order.items.product', 'user' => function($q) {
            $q->withTrashed();
        }, 'paymentMethod', 'verifier' => function($q) {
            $q->withTrashed();
        }]);

        return view('admin.detail-verifikasi', compact('payment'));
    }

    public function approve(Request $request, int $payment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $paymentModel = Payment::find($payment);
        $proofModel = PaymentProof::find($payment);

        if ($paymentModel instanceof Payment) {
            if (! $paymentModel->isPending()) {
                return redirect()->back()->with('error', 'Pembayaran sudah diproses sebelumnya.');
            }

            try {
                $proof = $paymentModel->order?->latestPaymentProof;

                if ($proof instanceof PaymentProof && $proof->isPending()) {
                    $this->paymentProofService->verify($proof, auth()->user(), $validated['notes'] ?? null);
                } else {
                    $previousStatus = $paymentModel->order->status;

                    $paymentModel->status = Payment::STATUS_SUCCESS;
                    $paymentModel->paid_at = now();
                    $paymentModel->notes = ($paymentModel->notes ? $paymentModel->notes."\n" : '').($validated['notes'] ?? 'Pembayaran diverifikasi oleh admin');
                    $paymentModel->save();

                    $paymentModel->order->paid_amount = $paymentModel->order->total_amount;
                    $paymentModel->order->payment_method = $paymentModel->payment_method;
                    $paymentModel->order->paid_at = now();
                    $paymentModel->order->status = Order::STATUS_PROCESSING;
                    $paymentModel->order->save();

                    // Generate QR code for pickup
                    if (empty($paymentModel->order->qr_code_path)) {
                        app(\App\Services\QRCodeService::class)->generateForOrder($paymentModel->order);
                    }

                    $paymentModel->order->statusHistories()->create([
                        'status' => Order::STATUS_PROCESSING,
                        'previous_status' => $previousStatus,
                        'notes' => 'Pembayaran terverifikasi oleh admin',
                        'changed_by' => auth()->id(),
                    ]);
                }

                ActivityLog::log(
                    'payment_verified',
                    $paymentModel->order::class,
                    $paymentModel->order->id,
                    "Pembayaran untuk pesanan {$paymentModel->order->order_number} diverifikasi oleh admin",
                    [
                        'payment_id' => $paymentModel->id,
                        'verified_by' => auth()->id(),
                    ]
                );

                return redirect()->route('admin.verifikasi.index')->with('success', 'Pembayaran berhasil diverifikasi.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memverifikasi pembayaran: '.$e->getMessage());
            }
        }

        if ($proofModel instanceof PaymentProof) {
            if (! $proofModel->isPending()) {
                return redirect()->back()->with('error', 'Bukti pembayaran sudah diproses sebelumnya.');
            }

            try {
                $this->paymentProofService->verify($proofModel, auth()->user(), $validated['notes'] ?? null);

                ActivityLog::log(
                    'payment_verified',
                    $proofModel->order::class,
                    $proofModel->order->id,
                    "Pembayaran untuk pesanan {$proofModel->order->order_number} diverifikasi oleh admin",
                    [
                        'payment_proof_id' => $proofModel->id,
                        'verified_by' => auth()->id(),
                    ]
                );

                return redirect()->route('admin.verifikasi.index')->with('success', 'Bukti pembayaran berhasil diverifikasi.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memverifikasi bukti pembayaran: '.$e->getMessage());
            }
        }

        abort(404);
    }

    public function reject(Request $request, int $payment): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $paymentModel = Payment::find($payment);
        $proofModel = PaymentProof::find($payment);

        if ($paymentModel instanceof Payment) {
            if (! $paymentModel->isPending()) {
                return redirect()->back()->with('error', 'Pembayaran sudah diproses sebelumnya.');
            }

            try {
                $proof = $paymentModel->order?->latestPaymentProof;

                if ($proof instanceof PaymentProof && $proof->isPending()) {
                    $this->paymentProofService->reject($proof, auth()->user(), $validated['reason']);
                }

                $paymentModel->status = Payment::STATUS_FAILED;
                $paymentModel->notes = ($paymentModel->notes ? $paymentModel->notes."\n" : '').'Ditolak: '.$validated['reason'];
                $paymentModel->save();

                ActivityLog::log(
                    'payment_rejected',
                    $paymentModel->order::class,
                    $paymentModel->order->id,
                    "Pembayaran untuk pesanan {$paymentModel->order->order_number} ditolak: {$validated['reason']}",
                    [
                        'payment_id' => $paymentModel->id,
                        'rejected_by' => auth()->id(),
                        'reason' => $validated['reason'],
                    ]
                );

                return redirect()->route('admin.verifikasi.index')->with('success', 'Pembayaran berhasil ditolak.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menolak pembayaran: '.$e->getMessage());
            }
        }

        if ($proofModel instanceof PaymentProof) {
            if (! $proofModel->isPending()) {
                return redirect()->back()->with('error', 'Bukti pembayaran sudah diproses sebelumnya.');
            }

            try {
                $this->paymentProofService->reject($proofModel, auth()->user(), $validated['reason']);

                ActivityLog::log(
                    'payment_rejected',
                    $proofModel->order::class,
                    $proofModel->order->id,
                    "Pembayaran untuk pesanan {$proofModel->order->order_number} ditolak: {$validated['reason']}",
                    [
                        'payment_proof_id' => $proofModel->id,
                        'rejected_by' => auth()->id(),
                        'reason' => $validated['reason'],
                    ]
                );

                return redirect()->route('admin.verifikasi.index')->with('success', 'Bukti pembayaran berhasil ditolak.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menolak bukti pembayaran: '.$e->getMessage());
            }
        }

        abort(404);
    }
}
