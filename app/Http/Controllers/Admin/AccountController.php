<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Akun pengguna: profil & ganti password.
 */
class AccountController extends Controller
{
    public function profile()
    {
        return view('admin.account.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'   => ['nullable', 'string', 'max:50'],
            'locale'  => ['nullable', Rule::in(array_keys(config('lbacloud.locales')))],
            'skin'    => ['nullable', 'string'],
            'picture' => ['nullable', 'image', 'max:2048'],
        ]);

        // Unggah foto profil -> kolom `avatar` pada tabel users.
        if ($request->hasFile('picture')) {
            $data['avatar'] = $request->file('picture')->store('avatars', 'public');
        }
        unset($data['picture']);

        $user->forceFill($data)->save();

        if (! empty($data['locale'])) {
            $request->session()->put('locale', $data['locale']);
        }
        if (! empty($data['skin'])) {
            $request->session()->put('skin', $data['skin']);
        }

        return back()->with('success', __('messages.profile_updated'));
    }

    public function password()
    {
        return view('admin.account.password');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Cast 'hashed' pada model User akan meng-hash otomatis.
        auth()->user()->forceFill(['password' => $data['password']])->save();

        return back()->with('success', __('messages.password_updated'));
    }
}
