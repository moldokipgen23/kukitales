<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("site_setting:$key", function () use ($key, $default) {
            return self::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting:$key");
    }

    protected static function booted(): void
    {
        static::saved(fn ($m) => Cache::forget("site_setting:{$m->key}"));
        static::deleted(fn ($m) => Cache::forget("site_setting:{$m->key}"));
    }
}
