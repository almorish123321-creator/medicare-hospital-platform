<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'group', 'ar', 'en'];

    public static function getTranslation(string $key, string $group = 'common', string $locale = 'ar'): ?string
    {
        return Cache::remember("translation_{$group}_{$key}_{$locale}", 3600, function () use ($key, $group, $locale) {
            $translation = self::where('key', $key)->where('group', $group)->first();
            return $translation ? $translation->$locale : null;
        });
    }

    public static function clearCache(): void
    {
        Cache::flush();
    }
}