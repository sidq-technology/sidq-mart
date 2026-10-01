<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $products = Product::with(['category', 'variants'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'stock_quantity' => 'required|integer|min:0',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_flash_sale' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'has_variants' => 'nullable|boolean',
            'variants' => 'nullable|array',
            'variants.*.color' => 'nullable|string|max:100',
            'variants.*.color_code' => 'nullable|string|max:25',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['sku'] = $validated['sku'] ?: 'SIDQ-' . strtoupper(Str::random(6));
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_flash_sale'] = $request->has('is_flash_sale');
        $validated['is_active'] = $request->has('is_active');
        $validated['has_variants'] = $request->boolean('has_variants');

        // Handle Primary Image upload
        if ($request->hasFile('primary_image')) {
            $path = $request->file('primary_image')->store('products', 'public');
            $validated['primary_image'] = $path;
        }

        $product = Product::create($validated);

        // Handle Gallery Images upload
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $file) {
                $path = $file->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        // Handle Variants if enabled
        if ($product->has_variants && $request->has('variants')) {
            $totalVariantStock = 0;
            $rawVariants = $request->input('variants', []);

            foreach ($rawVariants as $index => $vData) {
                $color = trim((string) ($vData['color'] ?? ''));
                $size = trim((string) ($vData['size'] ?? ''));

                // Skip completely empty variant row
                if ($color === '' && $size === '') {
                    continue;
                }

                $colorCode = !empty($vData['color_code']) ? trim($vData['color_code']) : null;
                $vPrice = isset($vData['price']) && $vData['price'] !== '' ? (float) $vData['price'] : null;
                $vStock = isset($vData['stock_quantity']) && $vData['stock_quantity'] !== '' ? (int) $vData['stock_quantity'] : 0;
                $totalVariantStock += $vStock;

                $variantImagePath = null;
                if ($request->hasFile("variants.{$index}.image")) {
                    $variantImagePath = $request->file("variants.{$index}.image")->store('products/variants', 'public');
                }

                ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => $color ?: null,
                    'color_code' => $colorCode,
                    'size' => $size ?: null,
                    'sku' => $product->sku . '-' . strtoupper(Str::random(4)),
                    'price' => $vPrice,
                    'stock_quantity' => $vStock,
                    'image_path' => $variantImagePath,
                    'is_active' => true,
                ]);
            }

            // Sync product total stock with sum of variants
            if ($totalVariantStock > 0 || count($rawVariants) > 0) {
                $product->update(['stock_quantity' => $totalVariantStock]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'পণ্যটি সফলভাবে যুক্ত করা হয়েছে।');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load(['galleryImages', 'allVariants']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_flash_sale' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'has_variants' => 'nullable|boolean',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer',
            'variants.*.color' => 'nullable|string|max:100',
            'variants.*.color_code' => 'nullable|string|max:25',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_flash_sale'] = $request->has('is_flash_sale');
        $validated['is_active'] = $request->has('is_active');
        $validated['has_variants'] = $request->boolean('has_variants');

        if ($request->hasFile('primary_image')) {
            $path = $request->file('primary_image')->store('products', 'public');
            $validated['primary_image'] = $path;
        }

        $product->update($validated);

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $file) {
                $path = $file->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $product->galleryImages()->count() + $index,
                ]);
            }
        }

        // Handle Variants Update
        if ($product->has_variants && $request->has('variants')) {
            $totalVariantStock = 0;
            $rawVariants = $request->input('variants', []);
            $keptVariantIds = [];

            foreach ($rawVariants as $index => $vData) {
                $color = trim((string) ($vData['color'] ?? ''));
                $size = trim((string) ($vData['size'] ?? ''));

                if ($color === '' && $size === '') {
                    continue;
                }

                $colorCode = !empty($vData['color_code']) ? trim($vData['color_code']) : null;
                $vPrice = isset($vData['price']) && $vData['price'] !== '' ? (float) $vData['price'] : null;
                $vStock = isset($vData['stock_quantity']) && $vData['stock_quantity'] !== '' ? (int) $vData['stock_quantity'] : 0;
                $totalVariantStock += $vStock;

                $variantId = !empty($vData['id']) ? (int) $vData['id'] : null;
                $variant = $variantId ? ProductVariant::where('product_id', $product->id)->find($variantId) : null;

                $variantImagePath = $variant?->image_path;
                if ($request->hasFile("variants.{$index}.image")) {
                    $variantImagePath = $request->file("variants.{$index}.image")->store('products/variants', 'public');
                }

                if ($variant) {
                    $variant->update([
                        'color' => $color ?: null,
                        'color_code' => $colorCode,
                        'size' => $size ?: null,
                        'price' => $vPrice,
                        'stock_quantity' => $vStock,
                        'image_path' => $variantImagePath,
                        'is_active' => true,
                    ]);
                    $keptVariantIds[] = $variant->id;
                } else {
                    $newVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'color' => $color ?: null,
                        'color_code' => $colorCode,
                        'size' => $size ?: null,
                        'sku' => $product->sku . '-' . strtoupper(Str::random(4)),
                        'price' => $vPrice,
                        'stock_quantity' => $vStock,
                        'image_path' => $variantImagePath,
                        'is_active' => true,
                    ]);
                    $keptVariantIds[] = $newVariant->id;
                }
            }

            // Remove any variants that were deleted from the matrix
            $product->allVariants()->whereNotIn('id', $keptVariantIds)->delete();

            // Sync total stock quantity
            $product->update(['stock_quantity' => $totalVariantStock]);
        } elseif (!$product->has_variants) {
            // If variants disabled, delete existing variants
            $product->allVariants()->delete();
        }

        return redirect()->route('admin.products.index')->with('success', 'পণ্যটি সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'পণ্যটি মুছে ফেলা হয়েছে।');
    }

    public function deleteImage(ProductImage $image)
    {
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function uploadDescriptionImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products/description', 'public');
            $url = asset('storage/' . $path);
            return response()->json(['url' => $url, 'status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'ছবি আপলোড করতে ব্যর্থ হয়েছে।'], 400);
    }
}
