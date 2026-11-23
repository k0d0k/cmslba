<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tautan bukti (gambar/deteksi/anotasi) ke sebuah indikator asesmen LBA.
 */
class LbaAssessmentEvidenceLink extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_assessment_evidence_links';

    protected $fillable = [
        'assessment_id', 'indicator_key', 'evidence_type', 'evidence_id',
        'provenance', 'note', 'linked_by',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LbaAssessment::class, 'assessment_id');
    }
}
