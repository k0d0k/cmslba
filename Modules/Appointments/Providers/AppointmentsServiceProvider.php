<?php

namespace Modules\Appointments\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Appointments (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) dan view-nya
 * (Resources/views) sehingga bersifat mandiri. Migrasi modul berada di
 * Modules/Appointments/Database/Migrations dan dijalankan ke database mitra
 * (tenant) oleh App\Services\TenantService.
 */
class AppointmentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'appointments');
    }
}
