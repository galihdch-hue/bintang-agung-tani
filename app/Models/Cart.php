<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'total_items',
        'expires_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'total_amount' => 0,
                'total_items' => 0,
            ]
        );
    }

    public function recalculateTotals(): void
    {
        $this->load('items');

        $this->total_amount = $this->items->sum('subtotal');
        $this->total_items = $this->items->sum('quantity');

        $this->save();
    }

    public function getTotal(): float
    {
        $this->load('items.product');

        return (float) $this->items->sum(function ($item) {
            if (! $item->product) {
                return 0;
            }

            return $item->product->getCurrentPrice() * $item->quantity;
        });
    }

    public function getTotalItems(): int
    {
        return $this->items()->sum('quantity');
    }

    public function clear(): void
    {
        $this->items()->delete();

        $this->total_amount = 0;
        $this->total_items = 0;
        $this->save();
    }

    public function hasItems(): bool
    {
        return $this->items()->exists();
    }

    public function isEmpty(): bool
    {
        return ! $this->hasItems();
    }

    public function getFormattedSubtotal(): string
    {
        return 'Rp '.number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedShipping(): string
    {
        // Cart doesn't store shipping cost, will be calculated during checkout
        return 'Rp 0';
    }

    public function getFormattedTotal(): string
    {
        $total = $this->total_amount; // Add shipping when applicable

        return 'Rp '.number_format($total, 0, ',', '.');
    }
}
