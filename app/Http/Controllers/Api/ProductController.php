<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
  public function index(Request $request): JsonResponse
  {
    $query = Product::query()
      ->active()
      ->inStock()
      ->with('category');

    if ($request->filled('kategori')) {
      $query->whereHas('category', function ($categoryQuery) use ($request) {
        $categoryQuery->where('slug', $request->input('kategori'));
      });
    } elseif ($request->filled('category_id')) {
      $query->byCategory($request->category_id);
    }

    if ($request->filled('search')) {
      $query->search($request->search);
    }

    if ($request->boolean('featured')) {
      $query->featured();
    }

    if ($request->filled('min_price') || $request->filled('max_price')) {
      $minPrice = (float) $request->input('min_price', 0);
      $maxPrice = (float) $request->input('max_price', PHP_FLOAT_MAX);

      $query->where(function ($priceQuery) use ($minPrice, $maxPrice) {
        $priceQuery->where(function ($discountQuery) use ($minPrice, $maxPrice) {
          $discountQuery->whereBetween('discount_price', [$minPrice, $maxPrice]);
        })->orWhere(function ($regularQuery) use ($minPrice, $maxPrice) {
          $regularQuery->whereNull('discount_price')
            ->whereBetween('price', [$minPrice, $maxPrice]);
        });
      });
    }

    $sort = $request->input('sort', 'terbaru');

    match ($sort) {
      'harga-terendah' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
      'harga-tertinggi' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
      'terlaris' => $query->orderBy('view_count', 'desc'),
      default => $query->latest(),
    };

    $limit = max(1, min(50, (int) $request->input('limit', 12)));
    $products = $query->paginate($limit)->withQueryString();
    $products->getCollection()->transform(fn(Product $product) => $this->formatProduct($product));

    return response()->json($products);
  }

  public function show(Product $product): JsonResponse
  {
    abort_unless($product->is_active, 404);

    $product->load('category');
    $product->increment('view_count');

    return response()->json($this->formatProduct($product->fresh(['category'])));
  }

  public function showBySlug(string $slug): JsonResponse
  {
    $product = Product::active()
      ->with('category')
      ->where('slug', $slug)
      ->firstOrFail();

    $product->increment('view_count');

    return response()->json($this->formatProduct($product->fresh(['category'])));
  }

  public function featured(Request $request): JsonResponse
  {
    $limit = max(1, min(20, (int) $request->input('limit', 8)));

    $products = Product::active()
      ->featured()
      ->with('category')
      ->latest()
      ->take($limit)
      ->get()
      ->map(fn(Product $product) => $this->formatProduct($product))
      ->values();

    return response()->json($products);
  }

  public function categories(): JsonResponse
  {
    $categories = Category::query()
      ->active()
      ->ordered()
      ->withCount([
        'products as active_products_count' => fn($query) => $query->active()->inStock(),
      ])
      ->get();

    return response()->json($categories);
  }

  private function formatProduct(Product $product): array
  {
    $featuredImage = $product->getFirstImage();
    $images = $product->getImages();

    return [
      'id' => $product->id,
      'name' => $product->name,
      'slug' => $product->slug,
      'description' => $product->description,
      'short_description' => $product->short_description,
      'price' => (float) $product->price,
      'discount_price' => $product->discount_price !== null ? (float) $product->discount_price : null,
      'current_price' => (float) $product->getCurrentPrice(),
      'has_discount' => $product->hasDiscount(),
      'discount_percentage' => $product->getDiscountPercentage(),
      'featured_image' => $featuredImage ? asset($featuredImage) : null,
      'images' => array_map(fn($img) => asset($img), $images),
      'category_id' => $product->category_id,
      'category' => $product->category ? [
        'id' => $product->category->id,
        'name' => $product->category->name,
        'slug' => $product->category->slug ?? null,
      ] : null,
      'stock' => $product->stock,
      'unit' => $product->unit,
      'weight' => $product->weight !== null ? (float) $product->weight : null,
      'min_order' => $product->min_order,
      'max_order' => $product->max_order,
      'is_featured' => (bool) $product->is_featured,
      'is_active' => (bool) $product->is_active,
      'view_count' => $product->view_count,
      'availability_message' => $product->getAvailabilityMessage(1),
      'created_at' => $product->created_at?->toIso8601String(),
      'updated_at' => $product->updated_at?->toIso8601String(),
    ];
  }
}
