<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with(['category', 'galleryImages'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(8)
            ->get();

        return view('frontend.product-detail', compact('product', 'relatedProducts'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->latest()
            ->paginate(20);

        return view('frontend.category', compact('category', 'products'));
    }

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        
        $products = Product::where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('short_description', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.partials.search-results', compact('products', 'query'))->render(),
                'count' => $products->total(),
            ]);
        }

        return view('frontend.search', compact('products', 'query'));
    }
}
