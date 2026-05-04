<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active();

        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('featured')) {
            $query->featured();
        }

        $products = $query->with('category')->latest()->paginate($request->get('limit', 10));

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json($product);
    }

    public function categories()
    {
        $categories = Category::withCount('products')->get();

        return response()->json($categories);
    }
}
