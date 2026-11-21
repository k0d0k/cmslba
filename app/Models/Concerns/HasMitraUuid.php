<?php

namespace App\Models\Concerns;

/**
 * Menandai setiap baris tabel database mitra dengan uuid mitra pemiliknya.
 *
 * Kolom `mitra_uuid` diisi otomatis dari konteks mitra yang sedang aktif
 * (binding container `active_mitra`, dipasang oleh middleware ResolveTenant).
 */
trait HasMitraUuid
{
    public static function bootHasMitraUuid(): void
    {
        static::creating(function ($model) {
            if (blank($model->mitra_uuid) && app()->bound('active_mitra')) {
                $model->mitra_uuid = app('active_mitra')->uuid;
            }
        });
    }
}
