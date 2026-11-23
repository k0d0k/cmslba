<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gambar pemeriksaan LBA (objek tersimpan di disk).
 */
class LbaImage extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_images';

    protected $fillable = [
        'examination_id', 'disk', 'object_key', 'original_name', 'checksum',
        'mime_type', 'width', 'height', 'size_bytes', 'uploaded_by', 'source',
        'captured_at', 'instrument_label',
    ];

    protected $casts = [
        'width'       => 'integer',
        'height'      => 'integer',
        'captured_at' => 'datetime',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(LbaExamination::class, 'examination_id');
    }
}
