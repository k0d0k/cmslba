<?php

namespace Modules\Commerce\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Commerce (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) sehingga bersifat
 * mandiri. Migrasi modul berada di Modules/Commerce/Database/Migrations/Tenant
 * dan dijalankan ke database mitra (tenant) oleh App\Services\TenantService.
 */
class CommerceServiceProvider extends ServiceProvider
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
