<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Menu extends Model
{
    protected $fillable = [
        'parent_id',
        'key',
        'title',
        'icon',
        'type',
        'route_name',
        'url',
        'permission_name',
        'target',
        'sort',
        'is_active',
        'is_visible',
        'is_crud',
        'resource',
    ];

    protected $casts = [
        'title'      => 'array',
        'is_active'  => 'boolean',
        'is_visible' => 'boolean',
        'is_crud'    => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Menu $menu) {
            if (blank($menu->key)) {
                $menu->key = Str::slug($menu->title['id'] ?? $menu->title['en'] ?? 'menu-' . Str::random(4));
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    /** Judul sesuai locale aktif. */
    public function label(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $title = $this->title ?: [];

        return $title[$locale] ?? $title['id'] ?? $title['en'] ?? $this->key;
    }

    /** Peran yang diberi akses + ability CRUD-nya. */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu', 'menu_id', 'role_id')
            ->withPivot(['can_view', 'can_create', 'can_update', 'can_delete', 'can_export'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function resolveUrl(): ?string
    {
        if ($this->route_name && \Route::has($this->route_name)) {
            return route($this->route_name);
        }

        return $this->url ?: null;
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }
}
