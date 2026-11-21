<?php

namespace App\Models\Tenant;

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
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active'  => 'boolean',
    ];

    public $slug_source = 'name';

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    /** Akun login pelanggan pada tabel users (database pusat). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
