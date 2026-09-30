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

            return $settings[$key] ?? $default;
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
        return Cache::rememberForever('app_settings_all', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }
}
