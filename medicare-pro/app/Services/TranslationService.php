<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    public function get(string $key, string $group = 'common', string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        return Cache::remember("trans_{$group}_{$key}_{$locale}", 86400, function () use ($key, $group, $locale) {
            $trans = Translation::where('key', $key)->where('group', $group)->first();
            return $trans ? $trans->$locale : null;
        });
    }

    public function set(string $key, string $group, string $ar, string $en): Translation
    {
        $translation = Translation::updateOrCreate(
            ['key' => $key, 'group' => $group],
            ['ar' => $ar, 'en' => $en]
        );
        Cache::forget("trans_{$group}_{$key}_ar");
        Cache::forget("trans_{$group}_{$key}_en");
        return $translation;
    }

    public function getGroup(string $group, string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        return Translation::where('group', $group)->pluck($locale, 'key')->toArray();
    }

    public function clearCache(): void
    {
        // Clear all translation caches
    }
}
