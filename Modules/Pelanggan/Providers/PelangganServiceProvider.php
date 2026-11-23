<?php

namespace Modules\Pelanggan\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Pelanggan (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) sehingga bersifat
 * mandiri. Modul ini tidak memiliki view sendiri (menggunakan view bersama
 * 'admin.crud') dan tidak memiliki migrasi central. Migrasi tenant berada di
 * Modules/Pelanggan/Database/Migrations/Tenant dan dijalankan ke database mitra
 * (tenant) oleh App\Services\TenantService.
 */
class PelangganServiceProvider extends ServiceProvider
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
