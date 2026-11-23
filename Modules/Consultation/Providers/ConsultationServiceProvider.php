<?php

namespace Modules\Consultation\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Consultation (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) dan memakai view
 * inti bersama admin.crud. Migrasi modul berada di
 * Modules/Consultation/Database/Migrations dan dijalankan ke database mitra
 * (tenant) oleh App\Services\TenantService.
 */
class ConsultationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}
