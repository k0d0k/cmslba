<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Konfigurasi penyedia AI (SINA AI) milik sebuah mitra (database pusat).
 */
class AiProviderConfig extends Model
{
    protected $connection = 'central';

    protected $table = 'ai_provider_configs';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'mitra_id',
        'provider',
        'label',
        'base_url',
        'model',
        'api_key',
        'is_default',
        'enabled',
        'settings',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'enabled'    => 'boolean',
        'settings'   => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiProviderConfig $config) {
            $config->id ??= (string) Str::ulid();
        });
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }
}
