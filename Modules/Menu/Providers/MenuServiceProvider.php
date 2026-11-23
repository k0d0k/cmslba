<?php

namespace Modules\Menu\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Menu (Modular Native).
 *
 * Mendaftarkan route (Routes/web.php), view (Resources/views dengan namespace
 * 'menu'), dan migrasi pusat (Database/Migrations).
 */
class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'menu');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
