<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Versi laporan yang dihasilkan dari sebuah asesmen LBA.
 */
class LbaReportVersion extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_report_versions';

    protected $fillable = [
        'assessment_id', 'version', 'status', 'locale', 'brand_id',
        'brand_snapshot', 'branch_id', 'branch_snapshot', 'reviewer_id',
        'reviewer_snapshot', 'approved_at', 'released_at', 'released_by',
        'checksum', 'disk', 'object_key', 'mime_type', 'size_bytes',
        'generated_by', 'generated_at',
    ];

    protected $casts = [
        'version'           => 'integer',
        'brand_snapshot'    => 'array',
        'branch_snapshot'   => 'array',
        'reviewer_snapshot' => 'array',
        'approved_at'       => 'datetime',
        'released_at'       => 'datetime',
        'generated_at'      => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LbaAssessment::class, 'assessment_id');
    }
}
