<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Event pada database mitra (tenant).
 * Berelasi ke mitra (database pusat) dan cabang (database mitra).
 */
class Event extends Model
{
    protected $connection = 'tenant';

    protected $table = 'events';

    protected $fillable = [
        'mitra_uuid', 'cabang_id', 'name', 'location',
        'start_date', 'end_date', 'notes', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'is_active'  => 'boolean',
    ];

    /** Relasi ke mitra pada database pusat (mitras.uuid). */
    public function mitra(): BelongsTo
    {
        return $this->belongsTo(\Modules\Mitra\Models\Mitra::class, 'mitra_uuid', 'uuid');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'cabang_id');
    }
}
