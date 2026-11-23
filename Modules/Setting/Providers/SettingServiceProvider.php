<?php

namespace Modules\Setting\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Setting (Modular Native).
 *
 * Mendaftarkan route (Routes/web.php), view (Resources/views dengan namespace
 * 'setting'), dan migrasi pusat (Database/Migrations).
 */
class SettingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'setting');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
