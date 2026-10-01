<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $page = (int) $request->input('page', 1);

        $banners = Cache::remember('home_banners', 3600, function () {
            return Banner::where('is_active', true)->orderBy('sort_order')->get();
        });

        $topCategories = Cache::remember('home_top_categories', 3600, function () {
            return Category::where('is_active', true)->where('is_top', true)->orderBy('sort_order')->get();
        });

        $flashSales = Cache::remember('home_flash_sales', 600, function () {
            return Product::where('is_active', true)->where('is_flash_sale', true)->latest()->take(12)->get();
        });

        $featuredProducts = Cache::remember('home_featured_products', 600, function () {
            return Product::where('is_active', true)->where('is_featured', true)->latest()->take(12)->get();
        });

        $allProducts = Cache::remember("home_all_products_p_{$page}", 300, function () {
            return Product::where('is_active', true)->latest()->paginate(16);
        });

        return view('frontend.home', compact('banners', 'topCategories', 'flashSales', 'featuredProducts', 'allProducts'));
    }
}
