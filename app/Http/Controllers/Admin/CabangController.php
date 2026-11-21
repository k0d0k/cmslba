<?php

namespace App\Http\Controllers\Admin;

use App\Models\Mitra;
use Illuminate\Http\Request;

/**
 * Contoh modul CRUD yang sudah tersedia (dibuat manual),
 * sama seperti hasil CRUD Generator.
 *
 * Alur `mitra_uuid` pada tabel cabang (database tenant):
 * 1. Login sebagai principal  -> saat create/edit bisa memilih nama mitra
 *    (opsi diambil dari tabel `mitras` di database pusat).
 * 2. Login selain principal   -> hanya muncul satu mitra (sesuai user->mitra_id),
 *    nilainya dikunci & dipaksa di server.
 * 3. Selain principal         -> daftar hanya menampilkan cabang milik mitranya
 *    (sesuai hak akses menu/view dari database pusat).
 */
class CabangController extends BaseCrudController
{
    protected string $model = \App\Models\Tenant\Cabang::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.cabang';
    protected string $title = 'Cabang';
    protected string $menuKey = 'cabang';
    protected bool $hasCabang = false;
    protected array $fields = [
        ['name' => 'name', 'type' => 'string', 'label' => 'Nama Cabang', 'nullable' => false],
        ['name' => 'code', 'type' => 'string', 'label' => 'Kode', 'nullable' => true],
        ['name' => 'phone', 'type' => 'string', 'label' => 'Telepon', 'nullable' => true],
        ['name' => 'address', 'type' => 'text', 'label' => 'Alamat', 'nullable' => true],
        ['name' => 'is_active', 'type' => 'boolean', 'label' => 'Aktif', 'nullable' => false],
    ];

    /** Cache definisi field agar query mitra hanya dijalankan sekali per request. */
    private ?array $resolvedFields = null;

    protected function fields(): array
    {
        if ($this->resolvedFields !== null) {
            return $this->resolvedFields;
        }

        $fields = $this->fields;
        $isPrincipal = $this->isPrincipal();
        $mitra = $this->currentMitra();

        // Principal: semua mitra. Selain principal: hanya mitra miliknya sendiri.
        $options = $isPrincipal
            ? Mitra::query()->orderBy('name')->pluck('name', 'uuid')->all()
            : ($mitra ? [$mitra->uuid => $mitra->name] : []);

        array_unshift($fields, [
            'name'     => 'mitra_uuid',
            'type'     => 'select',
            'label'    => __('common.mitra'),
            'nullable' => ! $isPrincipal,
            'options'  => $options,
            'disabled' => ! $isPrincipal,
            'default'  => $isPrincipal ? null : ($mitra?->uuid),
        ]);

        return $this->resolvedFields = $fields;
    }

    protected function query()
    {
        $query = parent::query();

        // Selain principal: batasi hanya cabang milik mitra user yang login.
        if (! $this->isPrincipal()) {
            $uuid = $this->currentMitra()?->uuid;
            if ($uuid) {
                $query->where('mitra_uuid', $uuid);
            }
        }

        return $query;
    }

    protected function values(Request $request): array
    {
        $data = parent::values($request);

        // Selain principal: paksa mitra_uuid ke mitra user (abaikan input form).
        if (! $this->isPrincipal()) {
            $data['mitra_uuid'] = $this->currentMitra()?->uuid;
        }

        return $data;
    }

    protected function isPrincipal(): bool
    {
        return $this->user()?->isPrincipal() ?? false;
    }

    /** Mitra aktif (dari binding `active_mitra`) atau mitra milik user yang login. */
    protected function currentMitra(): ?Mitra
    {
        if (app()->bound('active_mitra')) {
            return app('active_mitra');
        }

        $user = $this->user();

        return $user?->mitra_id ? Mitra::find($user->mitra_id) : null;
    }
}
