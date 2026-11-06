<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * List previously uploaded images from public storage
     */
    public function index(Request $request): JsonResponse
    {
        $folderFilter = $request->input('folder', 'all');
        $search = strtolower(trim($request->input('search', '')));

        $directories = [
            'products' => 'Products',
            'banners' => 'Banners',
            'categories' => 'Categories',
        ];

        if ($folderFilter !== 'all' && isset($directories[$folderFilter])) {
            $directories = [$folderFilter => $directories[$folderFilter]];
        }

        $items = [];
        $disk = Storage::disk('public');

        foreach ($directories as $folder => $label) {
            if (!$disk->exists($folder)) {
                continue;
            }

            $files = $disk->allFiles($folder);

            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                    continue;
                }

                $filename = basename($file);
                if (!empty($search) && !str_contains(strtolower($filename), $search)) {
                    continue;
                }

                try {
                    $lastModified = $disk->lastModified($file);
                    $size = $disk->size($file);
                } catch (\Throwable $e) {
                    $lastModified = time();
                    $size = 0;
                }

                $items[] = [
                    'path' => $file,
                    'url' => asset('storage/' . ltrim($file, '/')),
                    'name' => $filename,
                    'folder' => $label,
                    'size' => $this->formatBytes($size),
                    'timestamp' => $lastModified,
                    'date' => date('d M Y, h:i A', $lastModified),
                ];
            }
        }

        // Sort descending by timestamp (newest first)
        usort($items, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
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

            // Sync to public/storage if it's a real directory (cPanel fallback)
            $this->syncToPublicDirectory($path);

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
            if (!preg_match('/^(https?:)?\/\//i', $url)) {
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

            return response()->json([
                'success' => false,
                'message' => 'প্রদত্ত লিংক থেকে ছবিটি ডাউনলোড করা সম্ভব হয়নি। অনুগ্রহ করে নিশ্চিত করুন লিংকটি একটি সরাসরি ছবির লিংক (Direct Image URL)।',
            ], 422);
        }

        return response()->json([
            'success' => false,
            'message' => 'কোনো ফাইল বা ইমেজ ইউআরএল প্রদান করা হয়নি।',
        ], 400);
    }

    /**
     * Download an external image URL and store it locally
     * Robust cURL downloader with browser headers, redirect following, and image MIME verification
     */
    public function downloadAndSaveUrl(string $url, string $folder = 'products'): ?string
    {
        try {
            $url = trim($url);
            if (str_starts_with($url, '//')) {
                $url = 'https:' . $url;
            }

            $parsed = parse_url($url);
            $host = $parsed['host'] ?? '';

            $headers = [
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            ];
            if (!empty($host)) {
                $headers[] = "Referer: https://{$host}/";
            }

            $contents = null;
            $httpCode = 0;

            // 1. Primary Downloader: cURL
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_CONNECTTIMEOUT => 7,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTPHEADER => $headers,
                ]);
                $contents = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            }

            // 2. Fallback Downloader: stream_context
            if (!$contents || $httpCode >= 400) {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'follow_location' => 1,
                        'header' => implode("\r\n", $headers),
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
                $contents = @file_get_contents($url, false, $context);
            }

            if (!$contents || strlen($contents) < 50) {
                return null;
            }

            // 3. MIME-Type Detection & Image Integrity Verification
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $contents);
            finfo_close($finfo);

            // Reject HTML error pages or non-image payloads
            if (!str_starts_with($mime, 'image/') && $mime !== 'application/octet-stream') {
                \Log::warning("Rejected non-image payload from URL [{$url}], mime detected: {$mime}");
                return null;
            }

            $mimeMap = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                'image/svg+xml' => 'svg',
                'image/avif' => 'avif',
            ];

            // Resolve extension from mime or URL path
            $ext = $mimeMap[$mime] ?? null;
            if (!$ext) {
                $pathExt = strtolower(pathinfo($parsed['path'] ?? '', PATHINFO_EXTENSION));
                $ext = in_array($pathExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif']) ? $pathExt : 'jpg';
            }

            $filename = trim($folder, '/') . '/' . Str::random(40) . '.' . $ext;

            // Save to standard Laravel public storage disk
            Storage::disk('public')->put($filename, $contents);

            // Sync to public/storage if it's a real directory
            $this->syncToPublicDirectory($filename, $contents);

            return $filename;
        } catch (\Throwable $e) {
            \Log::warning("MediaController URL download error for [{$url}]: " . $e->getMessage());
        }

        return null;
    }

    /**
     * If public/storage is a real directory (common on shared cPanel hosting without symlinks),
     * keep it synchronized so the web server can serve files directly.
     */
    public function syncToPublicDirectory(string $relativePath, ?string $contents = null): void
    {
        try {
            $publicStorage = public_path('storage');
            if (!is_link($publicStorage) && is_dir($publicStorage)) {
                $dest = $publicStorage . '/' . ltrim($relativePath, '/');
                $destDir = dirname($dest);
                if (!is_dir($destDir)) {
                    @mkdir($destDir, 0755, true);
                }
                if ($contents !== null) {
                    @file_put_contents($dest, $contents);
                } else {
                    $source = storage_path('app/public/' . ltrim($relativePath, '/'));
                    if (file_exists($source)) {
                        @copy($source, $dest);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Non-critical, fallback route handles delivery
        }
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
