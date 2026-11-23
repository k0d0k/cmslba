<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Anotasi manual oleh manusia atas sebuah gambar pemeriksaan LBA.
 */
class LbaAnnotation extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_annotations';

    protected $fillable = [
        'examination_id', 'image_id', 'job_id', 'provenance', 'type',
        'geometry', 'label', 'notes', 'actor_id', 'revision',
    ];

    protected $casts = [
        'geometry' => 'array',
        'revision' => 'integer',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(LbaExamination::class, 'examination_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(LbaImage::class, 'image_id');
    }
}
