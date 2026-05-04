<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = $user->orders()
            ->with(['items.product'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $order = $request->user()->orders()
            ->with(['items.product', 'address'])
            ->findOrFail($id);

        return response()->json($order);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'address_id' => 'required|exists:addresses,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        return DB::transaction(function () use ($request) {
            $user = $request->user();
            
            $order = $user->orders()->create([
                'order_number' => Order::generateOrderNumber(),
                'status' => Order::STATUS_PENDING,
                'address_id' => $request->address_id,
                'payment_method_id' => $request->payment_method_id,
                'subtotal' => 0,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->getCurrentPrice() * $item['quantity'];
                
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $product->getCurrentPrice(),
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'subtotal' => $total,
                'total_amount' => $total, // plus shipping etc if implemented
            ]);

            return response()->json([
                'message' => 'Pesanan berhasil dibuat',
                'order' => $order->load('items.product')
            ], 201);
        });
    }
}
