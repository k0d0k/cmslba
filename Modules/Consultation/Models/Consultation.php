<?php

namespace Modules\Consultation\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Konsultasi pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); patient_id & branch_id char(26).
 */
class Consultation extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'consultations';

    protected $fillable = [
        'patient_id', 'branch_id', 'staff_user_id', 'appointment_id', 'channel', 'type',
        'status', 'scheduled_at', 'started_at', 'ended_at', 'reason', 'summary', 'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Consultation $consultation) {
            if (blank($consultation->created_by) && auth()->check()) {
                $consultation->created_by = (string) auth()->id();
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(\Modules\Pelanggan\Models\Pelanggan::class, 'patient_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'branch_id');
    }

    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'staff_user_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(\Modules\Appointments\Models\Appointment::class, 'appointment_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConsultationMessage::class, 'consultation_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ConsultationStatusHistory::class, 'consultation_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(ConsultationAuditLog::class, 'consultation_id');
    }
}
