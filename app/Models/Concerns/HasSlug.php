<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Menambahkan kolom `slug` unik otomatis untuk setiap model.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (blank($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    public function generateUniqueSlug(?string $source = null): string
    {
        $source = $source ?: ($this->slug_source ?? 'name');
        $base = Str::slug((string) ($this->{$source} ?? '')) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
