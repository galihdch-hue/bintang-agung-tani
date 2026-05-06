<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->subtotal = $this->unit_price * $quantity;
        $this->save();

        $this->cart->recalculateTotals();
    }

    public function getTotalPrice(): float
    {
        $this->load('product');

        if (!$this->product) {
            return $this->unit_price * $this->quantity;
        }

        return $this->product->getCurrentPrice() * $this->quantity;
    }

    public function getProductNameAttribute(): string
    {
        return $this->product ? $this->product->name : 'Produk Tidak Tersedia';
    }

    public function getFormattedUnitPrice(): string
    {
        return 'Rp '.number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedTotal(): string
    {
        return 'Rp '.number_format($this->getTotalPrice(), 2, ',', '.');
    }
}
