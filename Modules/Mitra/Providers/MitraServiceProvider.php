<?php

namespace Modules\Mitra\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Mitra (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php), view-nya
 * (Resources/views → namespace "mitra") dan migrasi pusatnya
 * (Database/Migrations, dijalankan pada koneksi `central`).
 */
class MitraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mitra');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
