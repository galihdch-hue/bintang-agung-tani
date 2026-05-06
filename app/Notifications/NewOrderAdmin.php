<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderAdmin extends Notification
{
    use Queueable;

    private Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
        $customerName = optional($this->order->user)->name ?? 'Pelanggan';
        $total = number_format($this->order->total_amount, 0, ',', '.');

        return (new MailMessage)
            ->subject("Pesanan Baru Masuk - #{$orderNumber}")
            ->greeting('Halo Admin,')
            ->line("Ada pesanan baru dari pelanggan {$customerName}.")
            ->line("Nomor Pesanan: #{$orderNumber}")
            ->line("Total: Rp {$total}")
            ->action('Lihat Detail Pesanan', route('admin.orders.show', $this->order))
            ->line('Silakan cek detail pesanan dan pastikan stok tersedia.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'user_name' => optional($this->order->user)->name ?? 'Pelanggan',
            'total_amount' => $this->order->total_amount,
            'title' => 'Pesanan Baru Masuk',
            'message' => "Pesanan #{$this->order->order_number} dari " . (optional($this->order->user)->name ?? 'Pelanggan'),
            'action_url' => route('admin.orders.show', $this->order),
            'type' => 'new_order',
            'icon' => 'ph-shopping-cart-simple'
        ]);
    }
}
