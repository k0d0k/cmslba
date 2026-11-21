<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * Akun user selalu berada di database pusat, walau diakses dari relasi
     * model pada database mitra (tenant).
     */
    protected $connection = 'central';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'mitra_id',
        'cabang_id',
        'phone',
        'avatar',
        'locale',
        'skin',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (blank($user->slug)) {
                $user->slug = static::uniqueSlug($user->name ?? $user->email);
            }
        });
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: Str::random(8);
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    /** True bila user termasuk level pusat (principal). */
    public function isPrincipal(): bool
    {
        return $this->hasAnyRole(config('lbacloud.roles')
            ? collect(config('lbacloud.roles'))->filter(fn ($r) => $r['scope'] === 'principal')->keys()->all()
            : []);
    }

    /** Nama role pertama — dipakai untuk kolom/select pada menu User. */
    public function getRoleAttribute(): ?string
    {
        return $this->roles->first()?->name;
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }
}
