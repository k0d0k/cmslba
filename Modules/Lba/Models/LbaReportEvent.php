<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat aksi pada laporan asesmen LBA (audit alur laporan).
 */
class LbaReportEvent extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_report_events';

    protected $fillable = [
        'assessment_id', 'report_version_id', 'action', 'actor_id', 'note', 'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LbaAssessment::class, 'assessment_id');
    }
}
