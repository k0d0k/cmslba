<?php

namespace App\Models;

use App\Services\WebsiteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Website/frontpage (platform bila mitra_id NULL, atau milik mitra).
 */
class Website extends Model
{
    protected $connection = 'central';

    protected $table = 'websites';

    protected $fillable = [
        'mitra_id',
        'name',
        'slug',
        'template',
        'status',
        'content',
        'published_at',
    ];

    protected $casts = [
        'content'      => 'array',
        'published_at' => 'datetime',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function isPlatform(): bool
    {
        return $this->mitra_id === null;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /** Media bersama (tidak per-bahasa). */
    public function media(): array
    {
        return $this->content['media'] ?? [];
    }

    /**
     * Konten untuk sebuah bahasa, dengan fallback ke bahasa default (id)
     * untuk bagian yang masih kosong.
     */
    public function contentFor(string $locale): array
    {
        $all = $this->content['locales'] ?? [];
        $default = array_replace_recursive(WebsiteService::blankLocale(), $all['id'] ?? []);

        if ($locale === 'id' || ! isset($all[$locale])) {
            return $default;
        }

        $mine = array_replace_recursive(WebsiteService::blankLocale(), $all[$locale]);

        return $this->preferNonEmpty($mine, $default);
    }

    /** Ambil nilai dari $primary bila terisi, selain itu dari $fallback. */
    protected function preferNonEmpty(array $primary, array $fallback): array
    {
        $out = [];
        foreach ($fallback as $key => $fallbackValue) {
            $primaryValue = $primary[$key] ?? null;

            if (is_array($fallbackValue)) {
                $out[$key] = is_array($primaryValue)
                    ? $this->preferNonEmpty($primaryValue, $fallbackValue)
                    : $fallbackValue;
            } else {
                $out[$key] = ($primaryValue !== null && $primaryValue !== '') ? $primaryValue : $fallbackValue;
            }
        }

        return $out;
    }
}
