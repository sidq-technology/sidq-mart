<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();
        $topCategories = Category::where('is_active', true)->where('is_top', true)->orderBy('sort_order')->get();
        $flashSales = Product::where('is_active', true)->where('is_flash_sale', true)->latest()->take(12)->get();
        $featuredProducts = Product::where('is_active', true)->where('is_featured', true)->latest()->take(12)->get();
        $allProducts = Product::where('is_active', true)->latest()->paginate(16);

        return view('frontend.home', compact('banners', 'topCategories', 'flashSales', 'featuredProducts', 'allProducts'));
    }
}
