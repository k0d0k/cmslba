<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Master jenis layanan janji temu pada database mitra (tenant).
 * Contoh isi: Konsultasi, Pemeriksaan LBA, Tindak Lanjut, Lainnya.
 */
class AppointmentServiceType extends Model
{
    protected $connection = 'tenant';

    protected $table = 'appointments_service_type';

    protected $fillable = ['name'];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'service_type_id');
    }
}
