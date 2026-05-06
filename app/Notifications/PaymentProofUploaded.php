<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentProofUploaded extends Notification implements ShouldQueue
{
    use Queueable;

    private Order $order;

    private PaymentProof $paymentProof;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, PaymentProof $paymentProof)
    {
        $this->order = $order;
        $this->paymentProof = $paymentProof;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->order->order_number;
        $userName = $this->order->user->name;

        return (new MailMessage)
            ->subject("Bukti Pembayaran Baru - Pesanan #{$orderNumber}")
            ->greeting('Halo Admin,')
            ->line("User {$userName} telah mengupload bukti pembayaran.")
            ->line("Nomor Pesanan: #{$orderNumber}")
            ->line('Status: Menunggu Verifikasi')
            ->action('Verifikasi Pembayaran', route('admin.payment-proofs.show', $this->paymentProof))
            ->line('Silakan verifikasi bukti pembayaran ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'payment_proof_id' => $this->paymentProof->id,
            'user_id' => $this->order->user_id,
            'user_name' => $this->order->user->name,
            'total_amount' => $this->order->total_amount,
            'status' => 'menunggu_verifikasi',
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $userName = optional($this->order->user)->name ?? 'Pelanggan';
        
        return new DatabaseMessage([
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'payment_proof_id' => $this->paymentProof->id,
            'user_id' => $this->order->user_id,
            'user_name' => $userName,
            'total_amount' => $this->order->total_amount,
            'title' => 'Bukti Pembayaran Baru',
            'message' => "Bukti pembayaran baru telah diunggah oleh {$userName} untuk pesanan #{$this->order->order_number}",
            'action_url' => route('admin.verifikasi.show', $this->paymentProof),
            'type' => 'payment_proof',
            'icon' => 'ph-currency-dollar-simple'
        ]);
    }
}
