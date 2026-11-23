<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Review/keputusan atas sebuah asesmen LBA.
 */
class LbaReview extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_reviews';

    protected $fillable = [
        'assessment_id', 'reviewer_id', 'reviewer_snapshot', 'decision',
        'notes', 'decided_at',
    ];

    protected $casts = [
        'reviewer_snapshot' => 'array',
        'decided_at'        => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LbaAssessment::class, 'assessment_id');
    }
}
