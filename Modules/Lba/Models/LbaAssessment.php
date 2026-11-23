<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Asesmen LBA atas sebuah pemeriksaan (katalog indikator + status alur kerja).
 */
class LbaAssessment extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_assessments';

    protected $fillable = [
        'examination_id', 'catalog_version', 'catalog_version_id', 'mode',
        'status', 'program_phase', 'brand_id', 'brand_snapshot', 'assessed_by',
        'submitted_at', 'returned_at', 'reviewed_at', 'locked_at',
        'last_saved_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'brand_snapshot' => 'array',
        'submitted_at'   => 'datetime',
        'returned_at'    => 'datetime',
        'reviewed_at'    => 'datetime',
        'locked_at'      => 'datetime',
        'last_saved_at'  => 'datetime',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(LbaExamination::class, 'examination_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LbaAssessmentItem::class, 'assessment_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LbaAssessmentEvent::class, 'assessment_id');
    }

    public function evidenceLinks(): HasMany
    {
        return $this->hasMany(LbaAssessmentEvidenceLink::class, 'assessment_id');
    }

    public function reportVersions(): HasMany
    {
        return $this->hasMany(LbaReportVersion::class, 'assessment_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(LbaReview::class, 'assessment_id');
    }
}
