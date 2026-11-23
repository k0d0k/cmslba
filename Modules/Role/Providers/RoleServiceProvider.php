<?php

namespace Modules\Role\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Role (Modular Native).
 *
 * Mendaftarkan route (Routes/web.php), view (Resources/views dengan namespace
 * 'role'), dan migrasi pusat (Database/Migrations).
 */
class RoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'role');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
