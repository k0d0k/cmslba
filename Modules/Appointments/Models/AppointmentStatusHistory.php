<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat perubahan status janji temu (audit per status).
 */
class AppointmentStatusHistory extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'appointment_status_histories';

    protected $fillable = [
        'appointment_id', 'from_status_id', 'to_status_id', 'changed_by', 'note', 'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'to_status_id');
    }
}
