<?php

namespace Modules\Practitioners\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Practitioners (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) dan memakai view
 * inti bersama admin.crud. Migrasi modul berada di
 * Modules/Practitioners/Database/Migrations/Tenant dan dijalankan ke database
 * mitra (tenant) oleh App\Services\TenantService.
 */
class PractitionersServiceProvider extends ServiceProvider
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
