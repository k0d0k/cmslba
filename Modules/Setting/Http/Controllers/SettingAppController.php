<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Menu\Models\Menu;
use Modules\Setting\Models\Setting;
use Modules\Menu\Services\MenuService;
use Illuminate\Http\Request;

/**
 * Pengaturan aplikasi (form tunggal) — disimpan pada tabel `settings` grup `app`.
 * Berisi: nama aplikasi, logo, bahasa default, telepon, email, alamat, dan map.
 */
class SettingAppController extends Controller
{
    /** Cek hak akses aksi berdasarkan menu `setting_aplikasi` di database pusat. */
    protected function can(string $ability): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('admin_principal')) {
            return true;
        }
        $menu = Menu::where('key', 'setting_aplikasi')->first();

        return $menu ? app(MenuService::class)->userCan($user, $menu, $ability) : false;
    }

    protected function authorizeAbility(string $ability): void
    {
        abort_unless($this->can($ability), 403, __('messages.forbidden'));
    }

    public function edit()
    {
        $this->authorizeAbility('view');

        return view('setting::setting-app.form', [
            'canUpdate' => $this->can('update'),
            'settings'  => [
                'app_name'       => Setting::get('app_name', config('app.name'), 'app'),
                'logo'           => Setting::get('logo', null, 'app'),
                'default_locale' => Setting::get('default_locale', config('app.locale'), 'app'),
                'phone'          => Setting::get('phone', null, 'app'),
                'email'          => Setting::get('email', null, 'app'),
                'address'        => Setting::get('address', null, 'app'),
                'map'            => Setting::get('map', null, 'app'),
                'footer'         => Setting::get('footer', null, 'app'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $this->authorizeAbility('update');

        $data = $request->validate([
            'app_name'       => ['required', 'string', 'max:150'],
            'logo'           => ['nullable', 'image', 'max:2048'],
            'default_locale' => ['required', 'in:id,en,ms'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'email'          => ['nullable', 'email'],
            'address'        => ['nullable', 'string'],
            'map'            => ['nullable', 'string'],
            'footer'         => ['nullable', 'string'],
        ]);

        Setting::put('app_name', $data['app_name'], 'app');
        Setting::put('default_locale', $data['default_locale'], 'app');
        Setting::put('phone', $data['phone'] ?? null, 'app');
        Setting::put('email', $data['email'] ?? null, 'app');
        Setting::put('address', $data['address'] ?? null, 'app');
        Setting::put('map', $data['map'] ?? null, 'app');
        Setting::put('footer', $data['footer'] ?? null, 'app');

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads/logo', 'public');
            Setting::put('logo', $path, 'app');
        }

        return redirect()->route('admin.setting-app.index')->with('success', __('messages.updated'));
    }
}
