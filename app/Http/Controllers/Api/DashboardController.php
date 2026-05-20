<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index(Request $request): JsonResponse
  {
    $user = $request->user();
    $userId = $user->id;

    $cart = Cart::getOrCreateForUser($userId);

    $pendingPaymentCount = Order::byUser($userId)
      ->where('status', Order::STATUS_PENDING)
      ->count();

    $processingCount = Order::byUser($userId)
      ->where('status', Order::STATUS_PROCESSING)
      ->count();

    $totalSpentThisMonth = Order::byUser($userId)
      ->where('status', Order::STATUS_COMPLETED)
      ->whereMonth('created_at', Carbon::now()->month)
      ->whereYear('created_at', Carbon::now()->year)
      ->sum('total_amount');

    $pendingPaymentTotal = Order::byUser($userId)
      ->where('status', Order::STATUS_PENDING)
      ->sum('total_amount');

    $recentOrders = Order::byUser($userId)
      ->with(['items.product', 'address', 'paymentMethod'])
      ->orderByDesc('created_at')
      ->take(3)
      ->get()
      ->map(fn(Order $order) => $this->formatOrderSummary($order))
      ->values();

    $weeklyPurchases = [];
    $weekLabels = [];
    $now = Carbon::now();

    for ($i = 5; $i >= 0; $i--) {
      $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
      $weekEnd = $now->copy()->subWeeks($i)->endOfWeek();

      $weekTotal = Order::byUser($userId)
        ->where('status', Order::STATUS_COMPLETED)
        ->whereBetween('created_at', [$weekStart, $weekEnd])
        ->sum('total_amount');

      $weeklyPurchases[] = (float) $weekTotal;
      $weekLabels[] = 'Minggu ' . ($i + 1);
    }

    $currentMonth = Carbon::now();
    $previousMonth = Carbon::now()->subMonth();

    $currentMonthTotal = Order::byUser($userId)
      ->where('status', Order::STATUS_COMPLETED)
      ->whereMonth('created_at', $currentMonth->month)
      ->whereYear('created_at', $currentMonth->year)
      ->sum('total_amount');

    $previousMonthTotal = Order::byUser($userId)
      ->where('status', Order::STATUS_COMPLETED)
      ->whereMonth('created_at', $previousMonth->month)
      ->whereYear('created_at', $previousMonth->year)
      ->sum('total_amount');

    $growthPercentage = $previousMonthTotal > 0
      ? round((($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100, 1)
      : ($currentMonthTotal > 0 ? 100 : 0);

    $recommendedProducts = Product::active()
      ->inStock()
      ->where(function ($query) {
        $query->whereNotNull('images')
          ->orWhereNotNull('featured_image');
      })
      ->inRandomOrder()
      ->take(4)
      ->get();

    if ($recommendedProducts->count() < 4) {
      $additionalProducts = Product::active()
        ->inStock()
        ->whereNotIn('id', $recommendedProducts->pluck('id'))
        ->inRandomOrder()
        ->take(4 - $recommendedProducts->count())
        ->get();

      $recommendedProducts = $recommendedProducts->merge($additionalProducts);
    }

    $categories = Category::active()
      ->ordered()
      ->whereHas('activeProducts')
      ->withCount('activeProducts')
      ->take(8)
      ->get();

    $bestSellers = Product::active()
      ->inStock()
      ->orderByDesc('view_count')
      ->take(10)
      ->get();

    $newArrivals = Product::active()
      ->inStock()
      ->latest()
      ->take(10)
      ->get();

    return response()->json([
      'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'profile_photo_url' => $user->profile_photo_url,
        'monthly_spending' => $user->getFormattedMonthlySpending(),
      ],
      'cart_count' => $cart->getTotalItems(),
      'pending_payment_count' => $pendingPaymentCount,
      'processing_count' => $processingCount,
      'total_spent_this_month' => (float) $totalSpentThisMonth,
      'pending_payment_total' => (float) $pendingPaymentTotal,
      'recent_orders' => $recentOrders,
      'recommended_products' => $recommendedProducts->map(fn(Product $product) => $this->formatProduct($product))->values(),
      'weekly_purchases' => $weeklyPurchases,
      'week_labels' => $weekLabels,
      'growth_percentage' => $growthPercentage,
      'categories' => $categories->map(fn(Category $category) => [
        'id' => $category->id,
        'name' => $category->name,
        'slug' => $category->slug,
        'icon' => $category->icon,
        'active_products_count' => $category->active_products_count,
      ])->values(),
      'best_sellers' => $bestSellers->map(fn(Product $product) => $this->formatProduct($product))->values(),
      'new_arrivals' => $newArrivals->map(fn(Product $product) => $this->formatProduct($product))->values(),
    ]);
  }

  private function formatProduct(Product $product): array
  {
    $featuredImage = $product->getFirstImage();
    return [
      'id' => $product->id,
      'name' => $product->name,
      'slug' => $product->slug,
      'description' => $product->description,
      'short_description' => $product->short_description,
      'price' => (float) $product->price,
      'discount_price' => $product->discount_price !== null ? (float) $product->discount_price : null,
      'current_price' => (float) $product->getCurrentPrice(),
      'featured_image' => $featuredImage ? asset($featuredImage) : null,
      'stock' => $product->stock,
      'unit' => $product->unit,
      'category' => $product->category ? [
        'id' => $product->category->id,
        'name' => $product->category->name,
        'slug' => $product->category->slug ?? null,
      ] : null,
    ];
  }

  private function formatOrderSummary(Order $order): array
  {
    $order->loadMissing(['items.product', 'address']);

    return [
      'id' => $order->id,
      'order_number' => $order->order_number,
      'status' => $order->status,
      'status_label' => $order->getStatusLabel(),
      'total_amount' => (float) $order->total_amount,
      'created_at' => $order->created_at?->toIso8601String(),
      'items' => $order->items->take(1)->map(fn($item) => [
        'name' => $item->product_name,
        'quantity' => $item->quantity,
        'price' => (float) $item->price,
      ])->values(),
      'address' => $order->address ? [
        'id' => $order->address->id,
        'label' => $order->address->label,
        'recipient_name' => $order->address->recipient_name,
        'phone' => $order->address->phone,
        'detail' => $order->address->complete_address,
      ] : null,
    ];
  }
}
