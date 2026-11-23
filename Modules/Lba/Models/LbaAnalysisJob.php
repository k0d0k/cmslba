<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pekerjaan analisis (AI) atas sebuah gambar pada pemeriksaan LBA.
 */
class LbaAnalysisJob extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_analysis_jobs';

    protected $fillable = [
        'examination_id', 'image_id', 'request_ref', 'status', 'provider',
        'model_name', 'model_version', 'attempts', 'error_code', 'billable',
        'pricing_rule_ref', 'unit_price_credits', 'credits_state',
        'idempotency_key', 'requested_by', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'attempts'           => 'integer',
        'billable'           => 'boolean',
        'unit_price_credits' => 'integer',
        'started_at'         => 'datetime',
        'completed_at'       => 'datetime',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(LbaExamination::class, 'examination_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(LbaImage::class, 'image_id');
    }
}
