<?php

namespace App\Models\Tenant;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelanggan extends Model
{
    use HasSlug;

    protected $connection = 'tenant';

    protected $table = 'pelanggans';

    protected $fillable = [
        'cabang_id', 'name', 'slug', 'email', 'phone',
        'address', 'birth_date', 'notes', 'is_active',
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
}
