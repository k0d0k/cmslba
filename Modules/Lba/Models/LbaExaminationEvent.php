<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat perubahan status pemeriksaan LBA (audit per status).
 */
class LbaExaminationEvent extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_examination_events';

    protected $fillable = [
        'examination_id', 'from_status', 'to_status', 'actor_id', 'note', 'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(LbaExamination::class, 'examination_id');
    }
}
