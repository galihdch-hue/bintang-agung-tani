<?php

namespace App\Services;

use App\Exceptions\InvalidOrderStatusException;
use App\Jobs\LogActivity;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
  public function __construct(
    private CartService $cartService,
    private StockService $stockService,
    private QRCodeService $qrCodeService
  ) {}

  public function createFromCart(User $user, array $orderData): Order
  {
    return DB::transaction(function () use ($user, $orderData) {
      // Validate cart
      $validation = $this->cartService->validateForCheckout($user->id);

      if (! $validation['valid']) {
        throw new \App\Exceptions\CartOperationException(
          'Cart validation failed: ' . implode(', ', $validation['errors']),
          null,
          $user->id
        );
      }

      if ($validation['cart'] === null || $validation['cart']->isEmpty()) {
        throw new \App\Exceptions\CartOperationException(
          'Cart is empty. Please add items before checkout.',
          null,
          $user->id
        );
      }

      $cart = $validation['cart'];

      // Sync prices
      $this->cartService->syncPrices($user->id);

      // Reload cart to get updated prices
      $cart->refresh();
      $cart->load('items.product');

      // Calculate totals
      $subtotal = $cart->getTotal();
      $discount = 0; // No discount logic for now
      $shippingCost = $orderData['shipping_cost'] ?? 0;
      $total = $subtotal - $discount + $shippingCost;

      // Prepare shipping address snapshot
      $address = $user->addresses()->find($orderData['address_id'] ?? null);
      if ($address) {
        $shippingAddressSnapshot = $address->getCompleteAddressAttribute();
        $shippingPhone = $address->phone;
      } else {
        $shippingAddressSnapshot = $orderData['shipping_address'] ?? null;
        $shippingPhone = $orderData['shipping_phone'] ?? null;
      }

      // Create order
      $order = Order::create([
        'user_id' => $user->id,
        'address_id' => $orderData['address_id'] ?? null,
        'status' => Order::STATUS_PENDING,
        'subtotal' => $subtotal,
        'discount_amount' => $discount,
        'shipping_cost' => $shippingCost,
        'total_amount' => $total,
        'paid_amount' => 0,
        'shipping_courier' => $orderData['shipping_courier'] ?? null,
        'shipping_service' => $orderData['shipping_service'] ?? null,
        'shipping_address_snapshot' => $shippingAddressSnapshot,
        'shipping_phone' => $shippingPhone,
        'notes' => $orderData['notes'] ?? null,
      ]);

      // Create order items from cart items
      foreach ($cart->items as $cartItem) {
        $product = $cartItem->product;

        OrderItem::create([
          'order_id' => $order->id,
          'product_id' => $product->id,
          'product_name' => $product->name,
          'product_sku' => $product->sku,
          'quantity' => $cartItem->quantity,
          'unit_price' => $cartItem->unit_price,
          'discount_amount' => 0,
          'subtotal' => $cartItem->subtotal,
          'notes' => $cartItem->notes,
        ]);

        // Decrease product stock
        $product->decreaseStock($cartItem->quantity, 'Order created: ' . $order->order_number);
      }

      // Clear cart
      $this->cartService->clearCart($user->id);

      // Log status history (pending status is automatically logged by Order model boot)
      // But we can add additional notes
      $order->statusHistories()->create([
        'status' => Order::STATUS_PENDING,
        'previous_status' => null,
        'notes' => 'Pesanan dibuat dari keranjang belanja',
      ]);

      // Generate QR code for order
      try {
        $this->qrCodeService->generateForOrder($order);
      } catch (Exception $qrException) {
        // Log error but don't fail order creation
        Log::error('Failed to generate QR code for order', [
          'order_id' => $order->id,
          'error' => $qrException->getMessage(),
        ]);
      }

      // Load relationships for return and notifications
      $order->load(['items', 'user']);

      // Notify User
      try {
        $user->notify(new \App\Notifications\OrderCreated($order));
      } catch (Exception $e) {
        Log::error('Failed to notify user about order creation', [
          'order_id' => $order->id,
          'error' => $e->getMessage(),
        ]);
      }

      // Notify Admins
      try {
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
          $admin->notify(new \App\Notifications\NewOrderAdmin($order));
        }
      } catch (Exception $e) {
        Log::error('Failed to notify admins about new order', [
          'order_id' => $order->id,
          'error' => $e->getMessage(),
        ]);
      }

      // Load relationships for return
      $order->load(['items', 'user']);

      return $order;
    });
  }

  public function updateStatus(Order $order, string $newStatus, ?string $notes = null, ?int $changedBy = null): void
  {
    $previousStatus = $order->status;

    // Validate status transition
    $validTransitions = $this->getValidStatusTransitions($order->status);

    if (! in_array($newStatus, $validTransitions)) {
      throw new InvalidOrderStatusException($order, $newStatus);
    }

    DB::transaction(function () use ($order, $newStatus, $notes, $changedBy) {
      // Call order updateStatus method
      $order->updateStatus($newStatus, $notes, $changedBy);

      // Handle specific status actions
      switch ($newStatus) {
        case Order::STATUS_CANCELLED:
          $this->handleCancellation($order);
          break;
        case Order::STATUS_COMPLETED:
          $this->handleCompletion($order);
          break;
      }
    });

    // Notify user of status update
    if ($order->user) {
      $order->user->notify(new \App\Notifications\OrderStatusUpdated($order, $previousStatus, $notes));
    }
  }

  public function cancelOrder(Order $order, string $reason, ?int $changedBy = null): void
  {
    if (! $order->canBeCancelled()) {
      throw new InvalidOrderStatusException($order, Order::STATUS_CANCELLED);
    }

    $this->updateStatus($order, Order::STATUS_CANCELLED, $reason, $changedBy);

    // If cancelled by user, notify admins
    if ($changedBy === $order->user_id) {
        $admins = \App\Models\User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewOrderAdmin($order));
        }
    }
  }

  public function processPayment(Order $order, float $amount, string $paymentMethod, array $paymentData = [], ?int $changedBy = null): void
  {
    DB::transaction(function () use ($order, $amount, $paymentMethod, $changedBy) {
      // Mark order as paid
      $order->markAsPaid($amount, $paymentMethod, $changedBy);

      // If fully paid, update status to processing
      if ($order->getRemainingAmount() <= 0) {
        $this->updateStatus(
          $order,
          Order::STATUS_PROCESSING,
          'Pembayaran lunas, pesanan diproses',
          $changedBy
        );
      }
    });
  }

  public function addTracking(Order $order, string $courier, string $trackingNumber): void
  {
    $order->update([
      'shipping_courier' => $courier,
      'tracking_number' => $trackingNumber,
    ]);
  }

  public function getOrderStats(?int $userId = null): array
  {
    $query = Order::query();

    if ($userId !== null) {
      $query->where('user_id', $userId);
    }

    $totalOrders = $query->count();
    $pendingOrders = (clone $query)->where('status', Order::STATUS_MENUNGGU_VERIFIKASI)->count();
    $processingOrders = (clone $query)->where('status', Order::STATUS_PROCESSING)->count();
    $completedOrders = (clone $query)->where('status', Order::STATUS_COMPLETED)->count();
    $cancelledOrders = (clone $query)->where('status', Order::STATUS_CANCELLED)->count();

    // Calculate total revenue from completed orders
    $totalRevenue = (clone $query)
      ->where('status', Order::STATUS_COMPLETED)
      ->sum('total_amount');

    return [
      'total_orders' => $totalOrders,
      'pending_orders' => $pendingOrders,
      'processing_orders' => $processingOrders,
      'completed_orders' => $completedOrders,
      'cancelled_orders' => $cancelledOrders,
      'total_revenue' => (float) $totalRevenue,
    ];
  }

  public function handleCancellation(Order $order): void
  {
    // Loop through order items and restore product stock
    foreach ($order->items as $item) {
      $product = $item->product;
      if ($product) {
        $product->increaseStock($item->quantity, 'Order cancelled: ' . $order->order_number);
      }
    }
  }

  public function handleCompletion(Order $order): void
  {
    LogActivity::dispatch(
      'order_completed',
      'Order',
      $order->id,
      "Order #{$order->order_number} completed",
      [
        'order_number' => $order->order_number,
        'total_amount' => $order->total_amount,
        'user_id' => $order->user_id,
      ],
      $order->user_id,
      request()->ip(),
      request()->userAgent()
    );
  }

  public function getValidStatusTransitions(string $currentStatus): array
  {
    return match ($currentStatus) {
      Order::STATUS_PENDING => [
        Order::STATUS_MENUNGGU_VERIFIKASI,
        Order::STATUS_CANCELLED,
      ],
      Order::STATUS_MENUNGGU_VERIFIKASI => [
        Order::STATUS_PROCESSING,
        Order::STATUS_CANCELLED,
      ],
      Order::STATUS_PROCESSING => [
        Order::STATUS_COMPLETED,
        Order::STATUS_CANCELLED,
      ],
      Order::STATUS_COMPLETED => [],
      Order::STATUS_CANCELLED => [],
      default => [],
    };
  }
}
