<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidOrderStatusException;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderAdmin;
use App\Notifications\OrderCreated;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
  public function __construct(private OrderService $orderService, private CartService $cartService) {}

  public function index(Request $request): JsonResponse
  {
    $user = $request->user();

    $query = $user->orders()->with(['items.product', 'address', 'paymentMethod']);

    if ($request->filled('status')) {
      $status = $this->normalizeStatusFilter($request->string('status')->toString());

      if ($status !== null) {
        $query->where('status', $status);
      }
    }

    $limit = max(1, min(50, (int) $request->input('limit', 10)));
    $orders = $query->latest()->paginate($limit);
    $orders->getCollection()->transform(fn(Order $order) => $this->formatOrder($order, false));

    return response()->json($orders);
  }

  public function show(Request $request, Order $order): JsonResponse
  {
    $this->ensureOrderOwnership($request, $order);

    $order->load(['items.product', 'address', 'paymentMethod', 'statusHistories']);

    return response()->json($this->formatOrder($order));
  }

  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'items' => 'required|array|min:1',
      'items.*.product_id' => 'required|exists:products,id',
      'items.*.quantity' => 'required|integer|min:1',
      'address_id' => 'required|exists:addresses,id',
      'payment_method_id' => 'nullable|exists:payment_methods,id',
      'shipping_cost' => 'nullable|numeric|min:0',
      'shipping_courier' => 'nullable|string|max:50',
      'shipping_service' => 'nullable|string|max:100',
      'notes' => 'nullable|string|max:500',
    ]);

    return DB::transaction(function () use ($request) {
      $user = $request->user();
      $address = $user->addresses()->find($request->address_id);

      if (! $address) {
        throw ValidationException::withMessages([
          'address_id' => ['Alamat yang dipilih tidak valid untuk akun ini.'],
        ]);
      }

      $order = $user->orders()->create([
        'order_number' => Order::generateOrderNumber(),
        'status' => Order::STATUS_PENDING,
        'address_id' => $address->id,
        'payment_method_id' => $request->payment_method_id,
        'subtotal' => 0,
        'shipping_cost' => (float) $request->input('shipping_cost', 0),
        'discount_amount' => 0,
        'total_amount' => 0,
        'shipping_courier' => $request->input('shipping_courier'),
        'shipping_service' => $request->input('shipping_service'),
        'shipping_address_snapshot' => $address->complete_address,
        'shipping_phone' => $address->phone,
        'notes' => $request->input('notes'),
      ]);

      $total = 0;
      foreach ($request->input('items', []) as $item) {
        $quantity = (int) $item['quantity'];
        $product = Product::query()
          ->active()
          ->whereKey($item['product_id'])
          ->lockForUpdate()
          ->first();

        if (! $product) {
          throw ValidationException::withMessages([
            'items' => ["Produk dengan ID {$item['product_id']} tidak ditemukan atau sudah tidak aktif."],
          ]);
        }

        if (! $product->isAvailableForOrder($quantity)) {
          throw ValidationException::withMessages([
            'items' => [$product->getAvailabilityMessage($quantity)],
          ]);
        }

        $unitPrice = $product->getCurrentPrice();
        $subtotal = $unitPrice * $quantity;

        $order->items()->create([
          'product_id' => $product->id,
          'product_name' => $product->name,
          'product_sku' => $product->sku ?? '',
          'quantity' => $quantity,
          'unit_price' => $unitPrice,
          'discount_amount' => 0,
          'subtotal' => $subtotal,
        ]);

        if (! $product->decreaseStock($quantity, 'Order created via API: ' . $order->order_number, $order->id)) {
          throw ValidationException::withMessages([
            'items' => ["Stok produk {$product->name} tidak mencukupi."],
          ]);
        }

        $total += $subtotal;
      }

      $order->update([
        'subtotal' => $total,
        'total_amount' => $total + (float) $request->input('shipping_cost', 0),
      ]);

      // Clear cart after successful order
      $this->cartService->clearCart($request->user()->id);

      $order->load(['items.product', 'address', 'paymentMethod', 'user']);

      // Notify User
      try {
        $user->notify(new OrderCreated($order));
      } catch (\Exception $e) {
        Log::error('Failed to notify user about order creation', [
          'order_id' => $order->id,
          'error' => $e->getMessage(),
        ]);
      }

      // Notify Admins
      try {
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
          $admin->notify(new NewOrderAdmin($order));
        }
      } catch (\Exception $e) {
        Log::error('Failed to notify admins about new order', [
          'order_id' => $order->id,
          'error' => $e->getMessage(),
        ]);
      }

      return response()->json([
        'message' => 'Pesanan berhasil dibuat',
        'order' => $this->formatOrder($order),
      ], 201);
    });
  }

  public function cancel(Request $request, Order $order): JsonResponse
  {
    $this->ensureOrderOwnership($request, $order);

    try {
      $this->orderService->cancelOrder($order, 'Dibatalkan oleh pengguna melalui API', $request->user()->id);

      $order->load(['items.product', 'address', 'paymentMethod', 'statusHistories']);

      return response()->json([
        'message' => 'Pesanan berhasil dibatalkan.',
        'order' => $this->formatOrder($order),
      ]);
    } catch (InvalidOrderStatusException $exception) {
      return response()->json([
        'message' => $exception->getMessage(),
      ], 422);
    }
  }

  private function ensureOrderOwnership(Request $request, Order $order): void
  {
    abort_unless($order->user_id === $request->user()->id, 403, 'Anda tidak memiliki akses untuk pesanan ini.');
  }

  private function normalizeStatusFilter(string $status): ?string
  {
    return match (strtolower($status)) {
      'semua', 'all', 'active', 'berjalan' => null,
      'pending', 'belum_bayar' => Order::STATUS_PENDING,
      'menunggu_verifikasi', 'menunggu-verifikasi' => Order::STATUS_MENUNGGU_VERIFIKASI,
      'processing', 'diproses' => Order::STATUS_PROCESSING,
      'completed', 'selesai' => Order::STATUS_COMPLETED,
      'cancelled', 'dibatalkan' => Order::STATUS_CANCELLED,
      default => $status,
    };
  }

  private function formatOrder(Order $order, bool $includeHistories = true): array
  {
    $order->loadMissing(['items.product', 'address', 'paymentMethod']);

    if ($includeHistories) {
      $order->loadMissing('statusHistories');
    }

    return [
      'id' => $order->id,
      'order_number' => $order->order_number,
      'status' => $order->status,
      'status_label' => $order->getStatusLabel(),
      'status_color' => $order->getStatusColor(),
      'payment_status_label' => $order->getPaymentStatusLabel(),
      'subtotal' => (float) $order->subtotal,
      'discount_amount' => (float) $order->discount_amount,
      'shipping_cost' => (float) $order->shipping_cost,
      'total_amount' => (float) $order->total_amount,
      'paid_amount' => (float) $order->paid_amount,
      'remaining_amount' => (float) $order->getRemainingAmount(),
      'shipping_courier' => $order->shipping_courier,
      'shipping_service' => $order->shipping_service,
      'tracking_number' => $order->tracking_number,
      'notes' => $order->notes,
      'admin_notes' => $order->admin_notes,
      'can_be_cancelled' => $order->canBeCancelled(),
      'can_view_barcode' => $order->canViewBarcode(),
      'qr_code_data' => $order->qr_code_data,
      'created_at' => $order->created_at?->toIso8601String(),
      'updated_at' => $order->updated_at?->toIso8601String(),
      'address' => $this->formatAddress($order->address),
      'payment_method' => $order->paymentMethod ? [
        'id' => $order->paymentMethod->id,
        'name' => $order->paymentMethod->name ?? $order->paymentMethod->method_name ?? null,
      ] : null,
      'items' => $order->items->map(fn($item) => $this->formatOrderItem($item))->values()->all(),
      'status_histories' => $includeHistories ? $order->statusHistories->map(fn($history) => [
        'id' => $history->id,
        'status' => $history->status,
        'previous_status' => $history->previous_status,
        'notes' => $history->notes,
        'created_at' => $history->created_at?->toIso8601String(),
      ])->values()->all() : [],
    ];
  }

  private function formatOrderItem($item): array
  {
    return [
      'id' => $item->id,
      'product_id' => $item->product_id,
      'product_name' => $item->product_name,
      'quantity' => $item->quantity,
      'price' => (float) $item->unit_price,
      'subtotal' => (float) $item->subtotal,
      'product' => $item->product ? [
        'id' => $item->product->id,
        'name' => $item->product->name,
        'featured_image' => $item->product->getFirstImage() ? asset($item->product->getFirstImage()) : null,
        'slug' => $item->product->slug,
      ] : null,
    ];
  }

  private function formatAddress(?Address $address): ?array
  {
    if (! $address) {
      return null;
    }

    return [
      'id' => $address->id,
      'label' => $address->label,
      'title' => $address->label,
      'recipient_name' => $address->recipient_name,
      'receiver_name' => $address->recipient_name,
      'phone' => $address->phone,
      'receiver_phone' => $address->phone,
      'full_address' => $address->full_address,
      'detail' => $address->complete_address,
      'province' => $address->province,
      'city' => $address->city,
      'district' => $address->district,
      'postal_code' => $address->postal_code,
      'is_default' => $address->is_default,
      'notes' => $address->notes,
      'complete_address' => $address->complete_address,
    ];
  }
}
