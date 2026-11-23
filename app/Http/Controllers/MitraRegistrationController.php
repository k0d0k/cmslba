<?php

namespace App\Http\Controllers;

use Modules\Mitra\Models\Mitra;
use Modules\User\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Pendaftaran mitra (tenant) publik.
 * Membuat: user pemilik, database mitra baru (lbacloud_mitra_xxx),
 * migrasi + cabang awal, lalu frontpage di /{nama-mitra}.
 */
class MitraRegistrationController extends Controller
{
    public function show()
    {
        return view('auth.register-mitra');
    }

    public function register(Request $request, TenantService $tenant)
    {
        $data = $request->validate([
            'mitra_name' => ['required', 'string', 'max:150'],
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'locale'     => ['nullable', 'in:id,en,ms'],
        ]);

        $mitra = Mitra::create([
            'name'   => $data['mitra_name'],
            'slug'   => Str::slug($data['mitra_name']),
            'locale' => $data['locale'] ?? 'id',
            'status' => 'active',
        ]);

        // Provisioning database mitra
        $tenant->provision($mitra);

        $tenant->connect($mitra);
        DB::connection('tenant')->table('cabangs')->insert([
            'name' => 'Cabang Pusat', 'slug' => 'cabang-pusat', 'code' => 'PST',
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Buat user pemilik mitra
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'mitra_id'  => $mitra->id,
            'locale'    => $data['locale'] ?? 'id',
            'is_active' => true,
        ]);
        $user->assignRole(Role::firstOrCreate(['name' => 'pemilik_mitra', 'guard_name' => 'web']));

        $mitra->update(['owner_user_id' => $user->id]);

        return redirect()->route('frontpage.mitra', $mitra->slug)
            ->with('success', __('messages.mitra_registered', ['db' => $mitra->db_name]));
    }
}
