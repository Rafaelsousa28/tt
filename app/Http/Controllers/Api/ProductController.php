<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category')->active()->inStock();

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('short_description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('care_level')) {
            $query->where('care_level', $request->care_level);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $sortMap = [
            'price_asc'   => ['price', 'asc'],
            'price_desc'  => ['price', 'desc'],
            'newest'      => ['created_at', 'desc'],
            'name_asc'    => ['name', 'asc'],
        ];
        [$col, $dir] = $sortMap[$request->sort ?? 'newest'];
        $query->orderBy($col, $dir);

        $products = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'data'  => $products->items(),
            'meta'  => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::with('category')
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::with('category')
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return response()->json([
            'product' => $product->append(['image_urls', 'current_price', 'is_on_sale']),
            'related' => $related->map->append(['first_image_url', 'current_price', 'is_on_sale']),
        ]);
    }

    public function featured(): JsonResponse
    {
        $products = Product::with('category')
            ->active()
            ->featured()
            ->inStock()
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map->append(['first_image_url', 'current_price', 'is_on_sale']);

        return response()->json(['data' => $products]);
    }
}
