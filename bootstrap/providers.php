<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,

    /*
    |--------------------------------------------------------------------------
    | Modul Aplikasi (Modular Native: PSR-4 + ServiceProvider)
    |--------------------------------------------------------------------------
    | Setiap modul mendaftarkan sendiri route, view, dan migrasinya.
    | Satu modul = satu folder di Modules/<Nama>/ dengan Providers/<Nama>ServiceProvider.
    */
    Modules\Appointments\Providers\AppointmentsServiceProvider::class,
    Modules\Audit\Providers\AuditServiceProvider::class,
    Modules\Brand\Providers\BrandServiceProvider::class,
    Modules\Cabang\Providers\CabangServiceProvider::class,
    Modules\Commerce\Providers\CommerceServiceProvider::class,
    Modules\Consultation\Providers\ConsultationServiceProvider::class,
    Modules\CrudGenerator\Providers\CrudGeneratorServiceProvider::class,
    Modules\Dashboard\Providers\DashboardServiceProvider::class,
    Modules\Events\Providers\EventsServiceProvider::class,
    Modules\Lba\Providers\LbaServiceProvider::class,
    Modules\Menu\Providers\MenuServiceProvider::class,
    Modules\Mitra\Providers\MitraServiceProvider::class,
    Modules\ModuleRegistry\Providers\ModuleRegistryServiceProvider::class,
    Modules\PaymentProvider\Providers\PaymentProviderServiceProvider::class,
    Modules\Pelanggan\Providers\PelangganServiceProvider::class,
    Modules\Practitioners\Providers\PractitionersServiceProvider::class,
    Modules\Role\Providers\RoleServiceProvider::class,
    Modules\Setting\Providers\SettingServiceProvider::class,
    Modules\Sina\Providers\SinaServiceProvider::class,
    Modules\Subscription\Providers\SubscriptionServiceProvider::class,
    Modules\User\Providers\UserServiceProvider::class,
    Modules\Website\Providers\WebsiteServiceProvider::class,
];
