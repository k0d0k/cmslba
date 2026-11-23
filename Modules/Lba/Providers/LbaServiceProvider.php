<?php

namespace Modules\Lba\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Lba (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) sehingga bersifat
 * mandiri. View memakai view inti bersama (admin.crud). Migrasi modul berada di
 * Modules/Lba/Database/Migrations/Tenant dan dijalankan ke database mitra
 * (tenant) oleh App\Services\TenantService.
 */
class LbaServiceProvider extends ServiceProvider
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
