<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Pelanggan\Mail\PelangganPasswordReset;
use Modules\Pelanggan\Models\Pelanggan;
use Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PelangganController extends BaseCrudController
{
    protected string $model = Pelanggan::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.pelanggan';
    protected string $menuKey = 'pelanggan';
    protected bool $hasCabang = true;

    protected function title(): string
    {
        return __('common.customer');
    }

    protected function mitraRoleOptions(): array
    {
        $meta = app(\Modules\Role\Services\RoleService::class)->allMeta();

        return Role::whereBetween('id', [3, 8])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (Role $r) => [$r->id => $meta[$r->name]['label'] ?? $r->name])
            ->all();
    }

    /**
     * Semua field didaftarkan di sini agar validasi ($this->rules()) 
     * dan pengambilan input ($this->values($request)) di BaseCrudController tetap berjalan otomatis.
     */
    protected function fields(): array
    {
        return [
            ['name' => 'picture', 'type' => 'image', 'label' => __('common.picture'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'patient_number', 'type' => 'string', 'label' => 'No. Pasien / Pelanggan', 'nullable' => true],
            ['name' => 'name', 'type' => 'string', 'label' => __('common.name'), 'nullable' => false],
            [
                'name'     => 'sex',
                'type'     => 'select',
                'label'    => 'Jenis Kelamin',
                'options'  => [
                    'male'   => 'Male',
                    'female' => 'Female',
                ],
                'nullable' => false,
            ],
            ['name' => 'date_of_birth', 'type' => 'date', 'label' => 'Tanggal Lahir', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'email', 'type' => 'email', 'label' => __('common.email'), 'nullable' => true],
            ['name' => 'phone', 'type' => 'string', 'label' => __('common.phone'), 'nullable' => true],
            ['name' => 'city', 'type' => 'string', 'label' => 'Kota', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'region', 'type' => 'string', 'label' => 'Provinsi / Wilayah', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'country', 'type' => 'string', 'label' => 'Kode Negara', 'nullable' => true, 'hide_in_table' => true],
            [
                'name'     => 'country',
                'type'     => 'select',
                'label'    => 'Kode Negara',
                'options'  => [
                    'id'   => 'ID',
                    'my' => 'MY'
                ],
                'nullable' => false,'hide_in_table' => true,
            ],
            ['name' => 'registered_at', 'type' => 'datetime', 'label' => 'Tanggal Registrasi', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'archived_at', 'type' => 'datetime', 'label' => 'Tanggal Arsip', 'nullable' => true, 'hide_in_table' => true],
            
            // Pilihan role (id 3 s.d. 8). Saat disimpan, akun user dibuat otomatis.
            ['name' => 'role', 'type' => 'select', 'label' => __('common.user_account'), 'nullable' => true, 'options' => $this->mitraRoleOptions(), 'hide_in_table' => true],
            
            // Ditampilkan di tabel: akun user yang sudah terhubung ke pelanggan.
            ['name' => 'user_id', 'type' => 'relation', 'label' => __('common.user_account'), 'nullable' => true, 'hide_in_form' => true, 'relation' => [
                'model' => User::class,
                'label' => 'name_with_role',
                'with'  => ['roles'],
                'query' => function ($query) {
                    $user = auth()->user();
                    if ($user && ! $user->isPrincipal() && $user->mitra_id) {
                        $query->where('mitra_id', $user->mitra_id);
                    }
                    return $query;
                },
            ]],
            ['name' => 'cabang_id', 'type' => 'relation', 'label' => __('common.branch'), 'nullable' => true, 'relation' => ['model' => \Modules\Cabang\Models\Cabang::class, 'label' => 'name']],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => false],
        ];
    }

    /**
     * Gunakan form khusus modul Pelanggan untuk tampilan create & edit
     */
    public function create()
    {
        $this->authorizeAbility('create');

        return view('admin.pelanggan.form', [
            'title'       => $this->title(),
            'routeName'   => $this->routeName,
            'record'      => null,
            'roleOptions' => $this->mitraRoleOptions(),
            'cabangs'     => \Modules\Cabang\Models\Cabang::all(),
        ]);
    }

    public function edit($slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();

        return view('admin.pelanggan.form', [
            'title'       => $this->title(),
            'routeName'   => $this->routeName,
            'record'      => $record,
            'roleOptions' => $this->mitraRoleOptions(),
            'cabangs'     => \Modules\Cabang\Models\Cabang::all(),
        ]);
    }

    // ... biarkan method store(), update(), createUserForPelanggan(), resetPassword(), dan rowActions() tetap seperti semula ...


    public function store(Request $request)
    {
        $this->authorizeAbility('create');
        $request->validate($this->rules());

        $data = $this->values($request);
        $roleId = $data['role'] ?? null;
        unset($data['role'], $data['user_id']);

        if ($this->hasCabang && empty($data['cabang_id'])) {
            $data['cabang_id'] = session('active_cabang_id') ?: $this->user()?->cabang_id;
        }

        $pelanggan = ($this->model)::create($data);

        if ($roleId) {
            $pelanggan->user_id = $this->createUserForPelanggan($pelanggan, (int) $roleId)->id;
            $pelanggan->save();
        }

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.created'));
    }

    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();

        $request->validate($this->rules($record->id));

        $data = $this->values($request);
        $roleId = $data['role'] ?? null;
        unset($data['role'], $data['user_id']);

        $record->update($data);

        if ($roleId) {
            $role = Role::find((int) $roleId);
            if ($user = $record->user) {
                // Sudah punya akun: cukup sinkronkan role-nya.
                if ($role) {
                    $user->syncRoles([$role->name]);
                }
            } else {
                $record->user_id = $this->createUserForPelanggan($record, (int) $roleId)->id;
                $record->save();
            }
        }

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }

    /**
     * Buat akun user (database pusat) untuk pelanggan sesuai role mitra terpilih:
     * password default + `mitra_id` (relasi user ke mitra).
     */
    protected function createUserForPelanggan(Pelanggan $pelanggan, int $roleId): User
    {
        $email = $pelanggan->email;
        if (blank($email) || User::withTrashed()->where('email', $email)->exists()) {
            $email = Str::slug($pelanggan->name ?: 'pelanggan') . '+' . Str::lower(Str::random(6)) . '@pelanggan.local';
        }

        $user = User::create([
        // Identitas pengguna
        'name'           => $pelanggan->name,
        'email'          => $email,
        'password'       => config('lbacloud.default_user_password', 'password'),

        // Data pelanggan
        'picture'        => $pelanggan->picture,
        'patient_number' => $pelanggan->patient_number,
        'sex'            => $pelanggan->sex,
        'date_of_birth'  => $pelanggan->date_of_birth,
        'phone'          => $pelanggan->phone,
        'city'           => $pelanggan->city,
        'region'         => $pelanggan->region,
        'country'        => $pelanggan->country,
        'address'        => $pelanggan->address,
        'notes'        => $pelanggan->notes,
        'cabang_id'      => $pelanggan->cabang_id,

        // Konteks akun
        'mitra_id'       => app()->bound('active_mitra')
            ? app('active_mitra')->id
            : $this->user()?->mitra_id,

        'locale'         => app()->getLocale(),
        'is_active'      => true,
    ]);


        if ($role = Role::find($roleId)) {
            $user->syncRoles([$role->name]);
        }

        return $user;
    }

    /**
     * Reset password akun pelanggan: buat password acak, simpan, lalu kirim ke email.
     */
    public function resetPassword($slug)
    {
        $this->authorizeAbility('update');

        $record = ($this->model)::where('slug', $slug)->firstOrFail();
        $user = $record->user;

        if (! $user) {
            return back()->with('error', __('messages.pelanggan_no_user'));
        }
        if (blank($user->email)) {
            return back()->with('error', __('messages.pelanggan_no_email'));
        }

        $password = Str::password(10);
        $user->forceFill(['password' => $password])->save();

        Mail::to($user->email)->send(new PelangganPasswordReset($user, $password));

        return back()->with('success', __('messages.pelanggan_password_reset', ['email' => $user->email]));
    }

    protected function rowActions($row): string
    {
        if (! $this->can('update') || ! $row->user_id) {
            return '';
        }

        return '<form method="POST" action="' . route($this->routeName . '.reset-password', $row->slug) . '" class="js-confirm" data-confirm="' . e(__('messages.confirm_reset_password')) . '">'
            . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
            . '<button type="submit" class="btn btn-sm btn-secondary">' . __('common.reset_password') . '</button>'
            . '</form>';
    }
}
