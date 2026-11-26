<?php

namespace Modules\Pelanggan\Models;

use App\Models\Concerns\HasMitraUuid;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelanggan extends Model
{
    use HasSlug;
    use HasMitraUuid;

    protected $connection = 'tenant';

    protected $table = 'pelanggans';

    protected $fillable = [
        'mitra_uuid', 'cabang_id', 'user_id', 'name', 'slug', 'email', 'phone',
        'picture', 'address', 'birth_date', 'notes', 'is_active',
        // Data pasien/pelanggan (dipakai form kustom admin.pelanggan.form).
        'patient_number', 'sex', 'date_of_birth', 'city', 'region', 'country',
        'registered_at', 'archived_at',
    ];

    protected $casts = [
        'birth_date'    => 'date',
        'date_of_birth' => 'date',
        'registered_at' => 'datetime',
        'archived_at'   => 'datetime',
        'is_active'     => 'boolean',
    ];

    public $slug_source = 'name';

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'cabang_id');
    }

    /** Akun login pelanggan pada tabel users (database pusat). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'user_id');
    }

    /**
     * Id role akun user yang terhubung (dipakai untuk mengisi pilihan role
     * pada form edit). Null bila belum ada akun / tanpa role.
     */
    public function getRoleAttribute(): ?int
    {
        return $this->user?->roles?->first()?->id;
    }
}
