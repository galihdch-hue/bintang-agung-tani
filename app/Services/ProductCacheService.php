<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

final class ProductCacheService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const FEATURED_PRODUCTS_KEY = 'featured_products';
    private const ACTIVE_CATEGORIES_KEY = 'active_categories';
    private const CATEGORY_PRODUCT_COUNTS_KEY = 'category_product_counts';

    /**
     * Get featured products (cached)
     */
    public function getFeatured(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::FEATURED_PRODUCTS_KEY, self::CACHE_TTL, function () use ($limit) {
            return Product::active()
                ->inStock()
                ->where('is_featured', true)
                ->with('category')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get active categories with product counts
     */
    public function getActiveCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::ACTIVE_CATEGORIES_KEY, self::CACHE_TTL, function () {
            return Category::active()
                ->withCount(['products' => function ($query) {
                    $query->active()->inStock();
                }])
                ->get();
        });
    }

    /**
     * Clear all product-related caches
     */
    public function clearAll(): void
    {
        Cache::forget(self::FEATURED_PRODUCTS_KEY);
        Cache::forget(self::ACTIVE_CATEGORIES_KEY);
        Cache::forget(self::CATEGORY_PRODUCT_COUNTS_KEY);
        
        // Clear product-specific caches
        $this->clearPattern('product:*');
        $this->clearPattern('related_products:*');
    }

    /**
     * Clear cache for specific product
     */
    public function clearForProduct(Product $product): void
    {
        Cache::forget("product:{$product->slug}");
        Cache::forget("related_products:{$product->category_id}:{$product->id}");
        Cache::forget(self::FEATURED_PRODUCTS_KEY);
        Cache::forget(self::ACTIVE_CATEGORIES_KEY);
        Cache::forget(self::CATEGORY_PRODUCT_COUNTS_KEY);
    }

    /**
     * Clear cache for specific category
     */
    public function clearForCategory(Category $category): void
    {
        Cache::forget(self::ACTIVE_CATEGORIES_KEY);
        Cache::forget(self::CATEGORY_PRODUCT_COUNTS_KEY);
        
        // Clear all products in this category
        $this->clearPattern("related_products:{$category->id}:*");
    }

    /**
     * Clear cache by pattern (Redis/Memcached only)
     */
    private function clearPattern(string $pattern): void
    {
        // For file/array cache, we can't use patterns easily
        // This is a placeholder for Redis implementation
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Remember callback result in cache
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        return Cache::remember($key, $ttl ?? self::CACHE_TTL, $callback);
    }

    /**
     * Get cache TTL
     */
    public function getCacheTtl(): int
    {
        return self::CACHE_TTL;
    }
}
