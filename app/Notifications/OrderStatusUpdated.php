<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    private Order $order;

    private string $previousStatus;

    private ?string $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $previousStatus, ?string $notes = null)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
        $this->notes = $notes;
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
        $statusLabel = $this->order->getStatusLabel();
        $orderNumber = $this->order->order_number;

        return (new MailMessage)
            ->subject("Status Pesanan #{$orderNumber} Diperbarui")
            ->greeting("Halo {$notifiable->name},")
            ->line('Status pesanan Anda telah diperbarui.')
            ->line("Nomor Pesanan: #{$orderNumber}")
            ->line("Status Baru: {$statusLabel}")
            ->when($this->notes, function ($message) {
                return $message->line("Catatan: {$this->notes}");
            })
            ->action('Lihat Pesanan', route('user.orders.show', $this->order))
            ->line('Terima kasih telah berbelanja di Bintang Agung Tani!');
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
            'previous_status' => $this->previousStatus,
            'current_status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
            'notes' => $this->notes,
            'updated_by' => auth()->id(),
            'updated_by_name' => auth()->user()?->name,
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'previous_status' => $this->previousStatus,
            'current_status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
            'notes' => $this->notes,
            'updated_by' => auth()->id(),
            'updated_by_name' => auth()->user()?->name,
            'action_url' => route('user.orders.show', $this->order),
            'action_text' => 'Lihat Pesanan',
        ]);
    }
}
