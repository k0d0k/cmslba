<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Mitra extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'db_name',
        'domain',
        'email',
        'phone',
        'address',
        'logo',
        'skin',
        'locale',
        'status',
        'plan',
        'brand_id',
        'langganan_id',
        'owner_user_id',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Mitra $mitra) {
            if (blank($mitra->uuid)) {
                $mitra->uuid = (string) Str::uuid();
            }
            if (blank($mitra->slug)) {
                $mitra->slug = Str::slug($mitra->name);
            }
            if (blank($mitra->db_name)) {
                // contoh: lbacloud_mitra_9f1c2d3e
                $short = substr(str_replace('-', '', $mitra->uuid), 0, 8);
                $mitra->db_name = config('lbacloud.tenant_db_prefix') . $short;
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function langganan(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'langganan_id');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'mitra_brands')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(MitraSubscription::class)->latest('created_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
