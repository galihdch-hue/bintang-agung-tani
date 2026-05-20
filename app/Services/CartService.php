<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(int $userId): Cart
    {
        return Cart::getOrCreateForUser($userId);
    }

    public function addToCart(User $user, Product $product, int $quantity = 1, ?string $notes = null): CartItem
    {
        return DB::transaction(function () use ($user, $product, $quantity, $notes) {
            /** @var Product $product */
            if (! $product->hasStock($quantity)) {
                throw new Exception("Product '{$product->name}' is out of stock. Only {$product->stock} item(s) available.");
            }

            if (! $product->isAvailableForOrder($quantity)) {
                throw new Exception($product->getAvailabilityMessage($quantity));
            }

            $cart = Cart::getOrCreateForUser($user->id);

            $existingItem = $cart->items()
                ->where('product_id', $product->id)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $quantity;

                if (! $product->hasStock($newQuantity)) {
                    throw new Exception("Cannot add {$quantity} more item(s). Product '{$product->name}' only has {$product->stock} item(s) in stock. Current cart quantity: {$existingItem->quantity}");
                }

                if (! $product->isAvailableForOrder($newQuantity)) {
                    throw new Exception($product->getAvailabilityMessage($newQuantity));
                }

                $existingItem->updateQuantity($newQuantity);
                $cart->recalculateTotals();

                return $existingItem;
            }

            $unitPrice = $product->getCurrentPrice();
            $subtotal = $unitPrice * $quantity;

            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'notes' => $notes,
            ]);

            $cart->recalculateTotals();

            return $cartItem;
        });
    }

    public function updateQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cartItem, $quantity) {
            /** @var Product|null $product */
            $product = $cartItem->product;
            /** @var Cart $cart */
            $cart = $cartItem->cart;

            if ($quantity <= 0) {
                $cartItem->delete();
                $cart->recalculateTotals();

                return $cartItem;
            }

            if (! $product) {
                $cartItem->delete();
                $cart->recalculateTotals();
                throw new Exception("Produk sudah tidak tersedia dan telah dihapus dari keranjang.");
            }

            if (! $product->hasStock($quantity)) {
                throw new Exception("Stok produk '{$product->name}' tidak mencukupi. Tersedia {$product->stock} item.");
            }

            if (! $product->isAvailableForOrder($quantity)) {
                throw new Exception($product->getAvailabilityMessage($quantity));
            }

            $cartItem->updateQuantity($quantity);
            $cart->recalculateTotals();

            return $cartItem;
        });
    }

    public function removeItem(CartItem $cartItem): void
    {
        $cart = $cartItem->cart;
        $cartItem->delete();
        $cart->recalculateTotals();
    }

    public function clearCart(int $userId): void
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->clear();
        }
    }

    public function getCartSummary(int $userId): array
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', $userId)
            ->first();

        if (! $cart || $cart->isEmpty()) {
            return [
                'items' => [],
                'total' => 0,
                'total_items' => 0,
                'is_empty' => true,
            ];
        }

        $items = $cart->items->map(function ($item) {
            $product = $item->product;

            if (! $product) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product_name,
                    'slug' => null,
                    'image' => null,
                    'unit_price' => (float) $item->unit_price,
                    'original_price' => null,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'max_quantity' => 0,
                    'notes' => $item->notes,
                    'is_available' => false,
                ];
            }

            $maxQuantity = min($product->stock, $product->max_order ?? PHP_INT_MAX);

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->getFirstImage() ? asset($product->getFirstImage()) : null,
                'unit_price' => (float) $item->unit_price,
                'original_price' => $product->hasDiscount() ? (float) $product->price : null,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
                'max_quantity' => $maxQuantity,
                'notes' => $item->notes,
                'is_available' => $product->isAvailableForOrder($item->quantity),
            ];
        })->toArray();

        return [
            'items' => $items,
            'total' => $cart->getTotal(),
            'total_items' => $cart->getTotalItems(),
            'is_empty' => false,
        ];
    }

    public function validateForCheckout(int $userId): array
    {
        return DB::transaction(function () use ($userId) {
            /** @var Cart|null $cart */
            $cart = Cart::with(['items.product'])
                ->where('user_id', $userId)
                ->first();

            $errors = [];
            $warnings = [];
            $itemsRemoved = false;

            if (! $cart || $cart->isEmpty()) {
                $errors[] = 'Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.';

                return [
                    'valid' => false,
                    'errors' => $errors,
                    'warnings' => $warnings,
                    'cart' => null,
                ];
            }

            foreach ($cart->items as $item) {
                $product = $item->product;

                // 1. Check if product exists in database
                if (! $product) {
                    $item->delete();
                    $itemsRemoved = true;
                    $warnings[] = "Produk sudah tidak tersedia dan telah dihapus dari keranjang.";
                    continue;
                }

                // 2. Check if product is active
                if (! $product->is_active) {
                    $item->delete();
                    $itemsRemoved = true;
                    $warnings[] = "Produk '{$product->name}' sedang tidak aktif dan telah dihapus dari keranjang.";
                    continue;
                }

                // 3. Check stock availability
                if (! $product->hasStock($item->quantity)) {
                    if ($product->stock <= 0) {
                        $errors[] = "Stok produk '{$product->name}' habis.";
                    } else {
                        $errors[] = "Stok produk '{$product->name}' tidak mencukupi. Tersedia {$product->stock} item (Anda memiliki {$item->quantity} di keranjang).";
                    }
                }

                // 4. Check order constraints
                if (! $product->isAvailableForOrder($item->quantity)) {
                    $errors[] = $product->getAvailabilityMessage($item->quantity) . " untuk '{$product->name}'.";
                }

                // 5. Automatic price synchronization
                $currentPrice = $product->getCurrentPrice();
                if ((float) $item->unit_price !== (float) $currentPrice) {
                    $item->unit_price = $currentPrice;
                    $item->subtotal = $currentPrice * $item->quantity;
                    $item->save();
                    $warnings[] = "Harga untuk '{$product->name}' telah berubah menjadi Rp " . number_format($currentPrice, 0, ',', '.') . ".";
                }
            }

            if ($itemsRemoved) {
                $cart->recalculateTotals();
                $errors[] = "Beberapa produk yang tidak tersedia telah otomatis dihapus dari keranjang. Silakan periksa kembali pesanan Anda.";
            }

            return [
                'valid' => empty($errors),
                'errors' => $errors,
                'warnings' => $warnings,
                'cart' => $cart->fresh(),
            ];
        });
    }

    public function syncPrices(int $userId): void
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', $userId)
            ->first();

        if (! $cart) {
            return;
        }

        foreach ($cart->items as $item) {
            /** @var CartItem $item */
            $product = $item->product;
            
            if (!$product) {
                $item->delete();
                continue;
            }

            /** @var Product $product */
            $currentPrice = $product->getCurrentPrice();

            if ((float) $item->unit_price !== (float) $currentPrice) {
                $item->unit_price = $currentPrice;
                $item->subtotal = $currentPrice * $item->quantity;
                $item->save();
            }
        }

        $cart->recalculateTotals();
    }
}
