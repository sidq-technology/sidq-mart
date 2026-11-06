<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * List all media files from storage disk and database
     */
    public function index(Request $request): JsonResponse
    {
        $disk = Storage::disk('public');
        $allFiles = $disk->allFiles();

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        $folderFilter = $request->input('folder', 'all');
        $search = strtolower(trim((string) $request->input('search', '')));

        $items = [];
        $seenPaths = [];

        // 1. Collect files from Storage disk
        foreach ($allFiles as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) {
                continue;
            }

            // Exclude system files if any
            if (str_starts_with($file, '.') || str_contains($file, '/.')) {
                continue;
            }

            // Apply folder filter
            if ($folderFilter !== 'all' && !str_starts_with($file, $folderFilter . '/')) {
                continue;
            }

            $basename = basename($file);

            // Apply search filter
            if ($search !== '' && !str_contains(strtolower($basename), $search) && !str_contains(strtolower($file), $search)) {
                continue;
            }

            $sizeBytes = 0;
            $timestamp = 0;
            try {
                $sizeBytes = $disk->size($file);
                $timestamp = $disk->lastModified($file);
            } catch (\Throwable $e) {}

            $items[] = [
                'path' => $file,
                'url' => asset('storage/' . $file),
                'name' => $basename,
                'size' => $this->formatBytes($sizeBytes),
                'timestamp' => $timestamp,
                'folder' => explode('/', $file)[0] ?? 'other',
                'is_external' => false,
            ];
            $seenPaths[$file] = true;
        }

        // 2. Collect any external URLs stored in Product / ProductImage
        $externalProducts = Product::where('primary_image', 'LIKE', 'http%')->pluck('primary_image');
        $externalGallery = ProductImage::where('image_path', 'LIKE', 'http%')->pluck('image_path');

        foreach ($externalProducts->merge($externalGallery)->unique() as $extUrl) {
            if (isset($seenPaths[$extUrl])) continue;

            $basename = basename(parse_url($extUrl, PHP_URL_PATH) ?: 'image.jpg');
            if ($search !== '' && !str_contains(strtolower($basename), $search)) {
                continue;
            }

            $items[] = [
                'path' => $extUrl,
                'url' => $extUrl,
                'name' => $basename,
                'size' => 'External',
                'timestamp' => time(),
                'folder' => 'external',
                'is_external' => true,
            ];
            $seenPaths[$extUrl] = true;
        }

        // Sort newest first
        usort($items, function ($a, $b) {
            return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
        });

        return response()->json([
            'success' => true,
            'count' => count($items),
            'items' => array_values($items),
        ]);
    }

    /**
     * Upload an image to media storage (via file or via URL)
     */
    public function upload(Request $request): JsonResponse
    {
        $folder = $request->input('folder', 'products');
        if (!in_array($folder, ['products', 'banners', 'categories', 'general'])) {
            $folder = 'products';
        }

        // Case 1: Physical File Upload
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            ]);

            $path = $request->file('file')->store($folder, 'public');

            return response()->json([
                'success' => true,
                'message' => 'ছবি সফলভাবে আপলোড হয়েছে!',
                'path' => $path,
                'url' => asset('storage/' . $path),
                'name' => basename($path),
            ]);
        }

        // Case 2: Upload from Image URL
        if ($request->filled('url')) {
            $url = trim($request->input('url'));
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'একটি সঠিক ইমেজ ইউআরএল (Valid Image URL) প্রদান করুন।',
                ], 422);
            }

            $downloadedPath = $this->downloadAndSaveUrl($url, $folder);

            if ($downloadedPath) {
                return response()->json([
                    'success' => true,
                    'message' => 'ইউআরএল থেকে ছবি সফলভাবে ডাউনলোড ও সংরক্ষণ করা হয়েছে!',
                    'path' => $downloadedPath,
                    'url' => asset('storage/' . $downloadedPath),
                    'name' => basename($downloadedPath),
                ]);
            }

            // Fallback: Use direct external URL if download timed out
            return response()->json([
                'success' => true,
                'message' => 'বাহ্যিক ইমেজ ইউআরএল সফলভাবে যুক্ত হয়েছে!',
                'path' => $url,
                'url' => $url,
                'name' => basename(parse_url($url, PHP_URL_PATH) ?: 'external-image.jpg'),
                'is_external' => true,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'কোনো ফাইল বা ইমেজ ইউআরএল প্রদান করা হয়নি।',
        ], 400);
    }

    /**
     * Download an external image URL and store it locally
     */
    public function downloadAndSaveUrl(string $url, string $folder = 'products'): ?string
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 8,
                    'follow_location' => 1,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SIDQ Mart Image Downloader',
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);

            $contents = @file_get_contents($url, false, $context);
            if ($contents && strlen($contents) > 200) {
                $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                    $ext = 'jpg';
                }

                $filename = $folder . '/' . Str::random(40) . '.' . $ext;
                Storage::disk('public')->put($filename, $contents);
                return $filename;
            }
        } catch (\Throwable $e) {
            \Log::warning("Media download failed for URL [{$url}]: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Format bytes to readable size
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 1) . ' ' . $units[$i];
    }
}
