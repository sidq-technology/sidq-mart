<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function shop(Request $request)
    {
        $query = Product::where('is_active', true)->with(['category', 'variants']);

        // Category Filter
        $selectedCategory = $request->input('category');
        if ($selectedCategory) {
            if (is_numeric($selectedCategory)) {
                $categoryIds = Category::where('id', $selectedCategory)
                    ->orWhere('parent_id', $selectedCategory)
                    ->pluck('id');
                $query->whereIn('category_id', $categoryIds);
            } else {
                $cat = Category::where('slug', $selectedCategory)->first();
                if ($cat) {
                    $categoryIds = Category::where('id', $cat->id)
                        ->orWhere('parent_id', $cat->id)
                        ->pluck('id');
                    $query->whereIn('category_id', $categoryIds);
                }
            }
        }

        // Price Filtering
        $minPrice = $request->filled('min_price') ? (float)$request->input('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float)$request->input('max_price') : null;

        if ($minPrice !== null) {
            $query->whereRaw('CAST(COALESCE(sale_price, regular_price) AS DECIMAL(10,2)) >= ?', [$minPrice]);
        }
        if ($maxPrice !== null) {
            $query->whereRaw('CAST(COALESCE(sale_price, regular_price) AS DECIMAL(10,2)) <= ?', [$maxPrice]);
        }

        // In-Stock Filter
        if ($request->boolean('in_stock')) {
            $query->where(function ($q) {
                $q->where('stock_quantity', '>', 0)
                  ->orWhereHas('variants', function ($vq) {
                      $vq->where('stock_quantity', '>', 0);
                  });
            });
        }

        // Keyword Search
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('short_description', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_low_high':
            case 'price_asc':
                $query->orderByRaw('CAST(COALESCE(sale_price, regular_price) AS DECIMAL(10,2)) ASC');
                break;
            case 'price_high_low':
            case 'price_desc':
                $query->orderByRaw('CAST(COALESCE(sale_price, regular_price) AS DECIMAL(10,2)) DESC');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderByDesc('order_items_count')->orderByDesc('is_featured')->latest('id');
                break;
            case 'name_asc':
                $query->orderBy('name', 'ASC');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        $perPage = 16;
        $products = $query->paginate($perPage);

        // AJAX / Load More response
        if ($request->ajax() || $request->wantsJson()) {
            $html = '';
            foreach ($products as $product) {
                $html .= '<div class="col">' . view('frontend.partials.product-card', compact('product'))->render() . '</div>';
            }

            return response()->json([
                'html' => $html,
                'count' => $products->count(),
                'total' => $products->total(),
                'currentPage' => $products->currentPage(),
                'hasMore' => $products->hasMorePages(),
                'nextPage' => $products->currentPage() + 1,
            ]);
        }

        // Categories list for sidebar
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->withCount(['products' => function ($pq) {
                    $pq->where('is_active', true);
                }]);
            }])
            ->withCount(['products' => function ($pq) {
                $pq->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();

        $categories->each(function ($parent) {
            $childrenCount = $parent->children->sum('products_count');
            $parent->total_products_count = $parent->products_count + $childrenCount;
        });

        // Price bounds
        $priceStats = Product::where('is_active', true)
            ->selectRaw('MIN(COALESCE(sale_price, regular_price)) as min_p, MAX(COALESCE(sale_price, regular_price)) as max_p')
            ->first();

        $dbMinPrice = (int) floor($priceStats->min_p ?? 0);
        $dbMaxPrice = (int) ceil($priceStats->max_p ?? 2000);

        return view('frontend.shop', compact(
            'products',
            'categories',
            'selectedCategory',
            'minPrice',
            'maxPrice',
            'dbMinPrice',
            'dbMaxPrice',
            'sort'
        ));
    }

    public function show(string $slug)
    {
        $product = Product::with(['category', 'galleryImages', 'variants'])
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
