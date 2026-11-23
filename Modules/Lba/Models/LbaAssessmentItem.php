<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Butir indikator pada sebuah asesmen LBA.
 */
class LbaAssessmentItem extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_assessment_items';

    protected $fillable = [
        'assessment_id', 'indicator_key', 'catalog_version', 'source_label',
        'observation_label', 'terminology_status', 'terminology_approved_by',
        'terminology_approved_at', 'source_column', 'position', 'category',
        'notes', 'assessor_id', 'assessed_at', 'revision', 'provenance',
    ];

    protected $casts = [
        'terminology_approved_at' => 'datetime',
        'position'                => 'integer',
        'assessed_at'             => 'datetime',
        'revision'                => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LbaAssessment::class, 'assessment_id');
    }
}
