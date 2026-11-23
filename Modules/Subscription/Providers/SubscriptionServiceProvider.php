<?php

namespace Modules\Subscription\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Subscription (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php), view-nya
 * (Resources/views dengan namespace `subscription`), dan migrasi pusatnya
 * (Database/Migrations).
 */
class SubscriptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'subscription');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
