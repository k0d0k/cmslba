<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Deteksi objek pada gambar (hasil AI maupun lain).
 */
class LbaDetection extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_detections';

    protected $fillable = [
        'examination_id', 'image_id', 'job_id', 'provenance', 'label',
        'geometry', 'confidence', 'model_name', 'model_version', 'quality_flags',
    ];

    protected $casts = [
        'geometry'      => 'array',
        'confidence'    => 'float',
        'quality_flags' => 'array',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(LbaExamination::class, 'examination_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(LbaImage::class, 'image_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(LbaAnalysisJob::class, 'job_id');
    }
}
