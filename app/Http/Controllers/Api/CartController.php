<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
  public function __construct(private CartService $cartService) {}

  public function index(Request $request): JsonResponse
  {
    return response()->json($this->cartService->getCartSummary($request->user()->id));
  }

  public function count(Request $request): JsonResponse
  {
    $cart = $this->cartService->getCartSummary($request->user()->id);

    return response()->json([
      'count' => $cart['total_items'],
    ]);
  }

  public function store(AddToCartRequest $request): JsonResponse
  {
    $product = Product::query()->active()->findOrFail($request->input('product_id'));

    $cartItem = $this->cartService->addToCart(
      $request->user(),
      $product,
      (int) $request->input('quantity', 1),
      $request->input('notes')
    );

    return response()->json([
      'message' => 'Produk berhasil ditambahkan ke keranjang.',
      'cart_item' => $this->formatCartItem($cartItem->fresh(['product'])),
      'cart' => $this->cartService->getCartSummary($request->user()->id),
    ], 201);
  }

  public function update(UpdateCartRequest $request, CartItem $cartItem): JsonResponse
  {
    $this->ensureOwnership($request, $cartItem);

    $updatedItem = $this->cartService->updateQuantity($cartItem, (int) $request->input('quantity'));
    $freshItem = $updatedItem->fresh(['product']);

    return response()->json([
      'message' => 'Jumlah produk berhasil diperbarui.',
      'cart_item' => $freshItem ? $this->formatCartItem($freshItem) : null,
      'cart' => $this->cartService->getCartSummary($request->user()->id),
    ]);
  }

  public function destroy(Request $request, CartItem $cartItem): JsonResponse
  {
    $this->ensureOwnership($request, $cartItem);

    $this->cartService->removeItem($cartItem);

    return response()->json([
      'message' => 'Produk berhasil dihapus dari keranjang.',
      'cart' => $this->cartService->getCartSummary($request->user()->id),
    ]);
  }

  public function clear(Request $request): JsonResponse
  {
    $this->cartService->clearCart($request->user()->id);

    return response()->json([
      'message' => 'Keranjang berhasil dikosongkan.',
      'cart' => $this->cartService->getCartSummary($request->user()->id),
    ]);
  }

  private function ensureOwnership(Request $request, CartItem $cartItem): void
  {
    abort_unless($cartItem->cart?->user_id === $request->user()->id, 403, 'Anda tidak memiliki akses untuk item keranjang ini.');
  }

  private function formatCartItem(CartItem $item): array
  {
    $product = $item->product;

    return [
      'id' => $item->id,
      'product_id' => $item->product_id,
      'name' => $product?->name ?? $item->product_name,
      'slug' => $product?->slug,
      'image' => $product?->getFirstImage() ? asset($product->getFirstImage()) : null,
      'unit_price' => (float) $item->unit_price,
      'original_price' => $product && $product->hasDiscount() ? (float) $product->price : null,
      'quantity' => $item->quantity,
      'subtotal' => (float) $item->subtotal,
      'max_quantity' => $product ? min($product->stock, $product->max_order ?? PHP_INT_MAX) : 0,
      'notes' => $item->notes,
      'is_available' => $product ? $product->isAvailableForOrder($item->quantity) : false,
    ];
  }
}
