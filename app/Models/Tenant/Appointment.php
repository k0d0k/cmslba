<?php

namespace App\Models\Tenant;

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
        'patient_id', 'branch_id', 'staff_user_id', 'starts_at', 'ends_at',
        'service_type', 'status', 'notes', 'created_by',
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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'patient_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'branch_id');
    }

    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'staff_user_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(AppointmentStatusHistory::class, 'appointment_id');
    }
}
