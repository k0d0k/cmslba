<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Master status janji temu pada database mitra (tenant).
 * Contoh isi: Terjadwal, Konfirmasi, Hadir, Tidak Hadir, Dibatalkan.
 */
class AppointmentStatus extends Model
{
    protected $connection = 'tenant';

    protected $table = 'appointments_status';

    protected $fillable = ['name'];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'status_id');
    }
}
