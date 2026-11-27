<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Cabang\Models\Cabang;
use Modules\Events\Models\Event;
use Modules\Mitra\Models\Mitra;

/**
 * CRUD Events pada database mitra.
 *
 * View modul: events::crud (Resources/views/crud).
 * Route didaftarkan oleh EventsServiceProvider (Routes/web.php).
 */
class EventController extends BaseCrudController
{
    protected string $model = Event::class;
    protected string $view = 'events::crud';
    protected string $routeName = 'admin.events';
    protected string $menuKey = 'events';
    protected bool $hasCabang = false;

    protected function title(): string
    {
        return __('events.title');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'mitra_id', 'type' => 'relation', 'label' => __('common.mitra'), 'nullable' => true,
                'relation' => ['model' => Mitra::class, 'label' => 'name']],
            ['name' => 'cabang_id', 'type' => 'relation', 'label' => __('common.branch'), 'nullable' => true,
                'relation' => ['model' => Cabang::class, 'label' => 'name']],
            ['name' => 'name', 'type' => 'string', 'label' => __('common.name'), 'nullable' => false],
            ['name' => 'location', 'type' => 'string', 'label' => __('events.location'), 'nullable' => true],
            ['name' => 'start_date', 'type' => 'datetime', 'label' => __('events.start_date'), 'nullable' => false],
            ['name' => 'end_date', 'type' => 'datetime', 'label' => __('events.end_date'), 'nullable' => false],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => true, 'default' => true],
        ];
    }
}
