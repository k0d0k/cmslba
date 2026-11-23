<?php

namespace Modules\Lba\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pemeriksaan LBA pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); patient_id, branch_id, appointment_id, consent_id char(26).
 */
class LbaExamination extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'lba_examinations';

    protected $fillable = [
        'patient_id', 'branch_id', 'appointment_id', 'operator_user_id', 'mode',
        'status', 'captured_at', 'instrument', 'magnification', 'calibration',
        'notes', 'consent_id', 'created_by',
    ];

    protected $casts = [
        'calibration' => 'array',
        'captured_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(\Modules\Pelanggan\Models\Pelanggan::class, 'patient_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'branch_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(\Modules\Appointments\Models\Appointment::class, 'appointment_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(LbaImage::class, 'examination_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LbaExaminationEvent::class, 'examination_id');
    }

    public function analysisJobs(): HasMany
    {
        return $this->hasMany(LbaAnalysisJob::class, 'examination_id');
    }
}
