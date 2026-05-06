<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->active()
            ->inStock()
            ->with('category');

        // Filter by category
        if ($request->has('kategori')) {
            $category = Category::where('slug', $request->input('kategori'))->first();
            if ($category) {
                $query->byCategory($category->id);
            }
        }

        // Price Range
        if ($request->filled('min_price')) {
            $query->where(function($q) use ($request) {
                $q->where('discount_price', '>=', $request->min_price)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereNull('discount_price')->where('price', '>=', $request->min_price);
                  });
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function($q) use ($request) {
                $q->where('discount_price', '<=', $request->max_price)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereNull('discount_price')->where('price', '<=', $request->max_price);
                  });
            });
        }

        // Search
        if ($request->has('search') && $request->input('search')) {
            $query->search($request->input('search'));
        }

        // Sorting
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'harga-terendah':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'harga-tertinggi':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'terlaris':
                $query->orderBy('view_count', 'desc');
                break;
            case 'terbaru':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Cache categories with product counts for 1 hour
        $categories = Cache::remember('active_categories', 3600, function () {
            return Category::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        // Cache category counts for 30 minutes
        $categoryCounts = Cache::remember('category_product_counts', 1800, function () use ($categories) {
            $counts = [];
            foreach ($categories as $category) {
                $counts[$category->slug] = Product::active()
                    ->inStock()
                    ->where('category_id', $category->id)
                    ->count();
            }

            return $counts;
        });

        $currentSort = $request->input('sort', 'terbaru');

        return view('user.produk', compact('products', 'categories', 'categoryCounts', 'currentSort'));
    }

    public function show(string $slug): View
    {
        // Cache individual product for 30 minutes
        $product = Cache::remember("product:{$slug}", 1800, function () use ($slug) {
            return Product::where('slug', $slug)
                ->active()
                ->with('category')
                ->firstOrFail();
        });

        // Cache related products for 30 minutes
        $relatedProducts = Cache::remember("related_products:{$product->category_id}:{$product->id}", 1800, function () use ($product) {
            return Product::active()
                ->inStock()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        });

        return view('user.detail-produk', compact('product', 'relatedProducts'));
    }
}
