<?php

namespace Modules\Consultation\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan audit (perubahan data) pada sebuah konsultasi.
 */
class ConsultationAuditLog extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'consultation_audit_logs';

    protected $fillable = [
        'consultation_id', 'action', 'actor_id', 'changes', 'occurred_at',
    ];

    protected $casts = [
        'changes'     => 'array',
        'occurred_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }
}
