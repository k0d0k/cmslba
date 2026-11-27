<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
            // mitra_uuid disembunyikan pada form & diisi otomatis dari mitra aktif.
            ['name' => 'mitra_uuid', 'type' => 'relation', 'label' => __('common.mitra'), 'nullable' => true,
                'hide_in_form' => true,
                'relation' => ['name' => 'mitra', 'model' => Mitra::class, 'label' => 'name']],
            // Pilihan cabang dibatasi hanya cabang milik mitra yang sedang aktif.
            ['name' => 'cabang_id', 'type' => 'relation', 'label' => __('common.branch'), 'nullable' => true,
                'relation' => ['model' => Cabang::class, 'label' => 'name',
                    'query' => fn ($query) => $this->scopeCabangToActiveMitra($query)]],
            ['name' => 'name', 'type' => 'string', 'label' => __('common.name'), 'nullable' => false],
            ['name' => 'location', 'type' => 'string', 'label' => __('events.location'), 'nullable' => true],
            ['name' => 'start_date', 'type' => 'datetime', 'label' => __('events.start_date'), 'nullable' => false],
            ['name' => 'end_date', 'type' => 'datetime', 'label' => __('events.end_date'), 'nullable' => false],
            ['name' => 'notes', 'type' => 'text', 'label' => __('common.note'), 'nullable' => true],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => true, 'default' => true],
        ];
    }

    protected function values(Request $request): array
    {
        $data = parent::values($request);

        // mitra_uuid selalu mengikuti mitra yang sedang aktif (tidak dari input form).
        $data['mitra_uuid'] = $this->currentMitra()?->uuid;

        return $data;
    }

    /** Batasi pilihan cabang hanya milik mitra yang sedang aktif. */
    protected function scopeCabangToActiveMitra(Builder $query): Builder
    {
        $uuid = $this->currentMitra()?->uuid;

        if ($uuid) {
            $query->where('mitra_uuid', $uuid);
        }

        return $query;
    }

    /** Mitra aktif (binding `active_mitra`) atau mitra milik user yang login. */
    protected function currentMitra(): ?Mitra
    {
        if (app()->bound('active_mitra')) {
            return app('active_mitra');
        }

        $user = $this->user();

        return $user?->mitra_id ? Mitra::find($user->mitra_id) : null;
    }
}
