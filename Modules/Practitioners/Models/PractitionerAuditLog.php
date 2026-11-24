<?php

namespace Modules\Practitioners\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan audit (perubahan data) pada seorang praktisi.
 */
class PractitionerAuditLog extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'practitioner_audit_logs';

    protected $fillable = [
        'practitioner_id', 'action', 'actor_id', 'changes', 'occurred_at',
    ];

    protected $casts = [
        'changes'     => 'array',
        'occurred_at' => 'datetime',
    ];

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'practitioner_id');
    }
}
