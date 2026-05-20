<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): \Illuminate\View\View
    {
        $cart = $this->cartService->getCartSummary(Auth::id());

        return view('user.keranjang', compact('cart'));
    }

    public function add(AddToCartRequest $request): RedirectResponse
    {
        try {
            $product = Product::findOrFail($request->input('product_id'));
            $this->cartService->addToCart(
                Auth::user(),
                $product,
                $request->input('quantity', 1),
                $request->input('notes')
            );

            if ($request->input('redirect_to') === 'checkout') {
                return redirect()->route('user.checkout.index');
            }

            return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateCartRequest $request, CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $this->cartService->updateQuantity($cartItem, $request->input('quantity'));

            return redirect()->back()->with('success', 'Jumlah produk berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->cartService->removeItem($cartItem);

        // Clear cart cache for instant refresh
        Cache::forget('cart_summary_' . Auth::id());

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart(Auth::id());

        return redirect()->back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    public function getCartData(): JsonResponse
    {
        $cart = $this->cartService->getCartSummary(Auth::id());

        return response()->json($cart);
    }

    public function getCount(): JsonResponse
    {
        $cart = $this->cartService->getCartSummary(Auth::id());

        return response()->json([
            'count' => $cart['total_items'],
        ]);
    }
}
