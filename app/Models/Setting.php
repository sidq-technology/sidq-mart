<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get($key, $default = null)
    {
        try {
            $settings = Cache::rememberForever('app_settings_all', function () {
                return self::pluck('value', 'key')->toArray();
            });

            $value = $settings[$key] ?? $default;

            if (in_array($key, ['site_logo', 'site_favicon']) && !empty($value)) {
                return self::formatAssetUrl($value);
            }

            return $value;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('app_settings_all');

        return $setting;
    }

    public static function getAll()
    {
        $all = Cache::rememberForever('app_settings_all', function () {
            return self::pluck('value', 'key')->toArray();
        });

        if (isset($all['site_logo'])) {
            $all['site_logo'] = self::formatAssetUrl($all['site_logo']);
        }
        if (isset($all['site_favicon'])) {
            $all['site_favicon'] = self::formatAssetUrl($all['site_favicon']);
        }

        return $all;
    }

    public static function formatAssetUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // If it contains localhost or 127.0.0.1, extract path and use current request domain
        if (str_contains($value, 'localhost') || str_contains($value, '127.0.0.1')) {
            $path = parse_url($value, PHP_URL_PATH);
            return asset(ltrim($path, '/'));
        }

        // If it starts with http:// or https://
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            if (request()->isSecure() && str_starts_with($value, 'http://')) {
                return preg_replace('/^http:/i', 'https:', $value);
            }
            return $value;
        }

        // Relative path (e.g. 'images/sidq-mart-logo.svg' or 'storage/settings/abc.jpg')
        return asset(ltrim($value, '/'));
    }
}
