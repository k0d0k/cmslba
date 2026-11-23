<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Dashboard (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) dan view-nya
 * (Resources/views) sehingga bersifat mandiri. Modul ini tidak memiliki migrasi.
 */
class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'dashboard');
    }
}
