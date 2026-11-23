<?php

namespace Modules\Sina\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Sina (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php), view-nya
 * (Resources/views) dan migrasi pusat (Database/Migrations) sehingga bersifat
 * mandiri.
 */
class SinaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'sina');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
