<?php

namespace Modules\CrudGenerator\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul CrudGenerator (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php), view-nya
 * (Resources/views) dan migrasi pusat (Database/Migrations) sehingga bersifat
 * mandiri.
 */
class CrudGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'crudgenerator');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
