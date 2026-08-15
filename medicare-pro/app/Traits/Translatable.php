<?php

namespace App\Traits;

trait Translatable
{
    public function getTranslation(string $field, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->getAttribute($field);

        if (is_array($value)) {
            return $value[$locale] ?? $value[config('app.locale', 'ar')] ?? '';
        }

        return $value ?? '';
    }

    public function setTranslation(string $field, string $locale, string $value): void
    {
        $translations = $this->getAttribute($field);
        if (!is_array($translations)) {
            $translations = [];
        }
        $translations[$locale] = $value;
        $this->setAttribute($field, $translations);
    }

    public function getTranslatableFields(): array
    {
        return $this->translatable ?? [];
    }
}
