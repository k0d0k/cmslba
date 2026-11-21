<?php

namespace App\Models\Tenant;

use App\Models\Concerns\HasMitraUuid;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    use HasSlug;
    use HasMitraUuid;

    protected $connection = 'tenant';

    protected $table = 'cabangs';

    protected $fillable = ['mitra_uuid', 'name', 'slug', 'code', 'address', 'phone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
    

    public $slug_source = 'name';

    public function pelanggans(): HasMany
    {
        return $this->hasMany(Pelanggan::class, 'cabang_id');
    }
}
