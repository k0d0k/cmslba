<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat aksi pada sebuah asesmen LBA (audit alur kerja).
 */
class LbaAssessmentEvent extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_assessment_events';

    protected $fillable = [
        'assessment_id', 'action', 'from_status', 'to_status', 'indicator_key',
        'actor_id', 'note', 'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LbaAssessment::class, 'assessment_id');
    }
}
