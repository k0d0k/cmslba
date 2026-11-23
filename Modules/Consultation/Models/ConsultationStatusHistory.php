<?php

namespace Modules\Consultation\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat perubahan status konsultasi (audit per status).
 */
class ConsultationStatusHistory extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'consultation_status_histories';

    protected $fillable = [
        'consultation_id', 'from_status', 'to_status', 'changed_by', 'note', 'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }
}
