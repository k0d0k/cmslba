<?php

namespace Modules\Audit\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Audit (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php), view-nya
 * (Resources/views) dan migrasi pusat (Database/Migrations) sehingga bersifat
 * mandiri.
 */
class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'audit');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
