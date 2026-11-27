<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Janji temu pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); patient_id & branch_id char(26).
 */
class Appointment extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'appointments';

    protected $fillable = [
        'mitra_uuid', 'patient_id', 'branch_id', 'staff_user_id', 'starts_at', 'ends_at',
        'service_type_id', 'status_id', 'notes', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment) {
            if (blank($appointment->created_by) && auth()->check()) {
                $appointment->created_by = (string) auth()->id();
            }
        });
    }

    /** Relasi ke mitra pada database pusat (mitras.uuid). */
    public function mitra(): BelongsTo
    {
        return $this->belongsTo(\Modules\Mitra\Models\Mitra::class, 'mitra_uuid', 'uuid');
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

    public function status(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'status_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(AppointmentServiceType::class, 'service_type_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(AppointmentStatusHistory::class, 'appointment_id');
    }
}
