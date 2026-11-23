<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

/**
 * Manajemen user pada tabel `users` (database pusat) beserta role-nya.
 * Role yang tersedia diambil dari role yang sudah ada (spatie/laravel-permission).
 */
class UserController extends BaseCrudController
{
    protected string $model = User::class;
    protected string $routeName = 'admin.users';
    protected string $menuKey = 'users';

    protected function title(): string
    {
        return __('common.user');
    }

    public function __construct(protected RoleService $roleService)
    {
    }

    /** Daftar role yang sudah ada: [nama => label]. */
    protected function roleOptions(): array
    {
        return $this->roleService->labels();
    }

    protected function fields(): array
    {
        return [
            ['name' => 'avatar', 'type' => 'image', 'label' => __('common.picture'), 'nullable' => true],
            ['name' => 'name', 'type' => 'string', 'label' => __('common.name'), 'nullable' => false],
            ['name' => 'email', 'type' => 'email', 'label' => __('common.email'), 'nullable' => false, 'unique' => true],
            ['name' => 'password', 'type' => 'password', 'label' => __('common.password'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'phone', 'type' => 'string', 'label' => __('common.phone'), 'nullable' => true],
            ['name' => 'role', 'type' => 'select', 'label' => __('common.role'), 'nullable' => true, 'options' => $this->roleOptions()],
            ['name' => 'mitra_id', 'type' => 'relation', 'label' => __('common.mitra'), 'nullable' => true, 'relation' => ['model' => \App\Models\Mitra::class, 'label' => 'name']],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => true, 'default' => true],
        ];
    }

    protected function query()
    {
        $query = parent::query()->with('roles');

        // User non-principal hanya melihat user pada mitra-nya sendiri.
        $user = $this->user();
        if ($user && ! $user->isPrincipal()) {
            $query->where('mitra_id', $user->mitra_id);
        }

        return $query;
    }

    protected function rules(?int $ignoreId = null): array
    {
        $rules = parent::rules($ignoreId);

        // Password wajib saat membuat user baru, opsional saat mengubah (kosong = tidak diganti).
        $rules['password'] = $ignoreId ? 'nullable|string|min:8' : 'required|string|min:8';

        return $rules;
    }

    public function store(Request $request)
    {
        $this->authorizeAbility('create');
        $request->validate($this->rules());

        $data = $this->values($request);
        unset($data['role']); // bukan kolom; ditangani lewat syncRoles.

        $user = User::create($data);

        if ($role = $request->input('role')) {
            $user->syncRoles([$role]);
        }

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.created'));
    }

    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');

        $record = User::where('slug', $slug)->firstOrFail();
        $request->validate($this->rules($record->id));

        $data = $this->values($request);
        unset($data['role']);
        if (blank($data['password'] ?? null)) {
            unset($data['password']); // biarkan password lama.
        }

        $record->update($data);

        if ($role = $request->input('role')) {
            $record->syncRoles([$role]);
        }

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }
}
