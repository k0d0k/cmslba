<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SkinSeeder::class,
            MenuSeeder::class,
            RoleMenuSeeder::class,
            UserSeeder::class,
        ]);

        // ---- Mitra demo + database mitra terpisah ----
        $mitra = Mitra::firstOrCreate(
            ['slug' => 'mitra-demo'],
            [
                'name'      => 'Mitra Demo',
                'locale'    => 'id',
                'status'    => 'active',
                'plan'      => 'pro',
            ],
        );

        $tenant = app(TenantService::class);
        $tenant->provision($mitra);
        $tenant->connect($mitra);

        // Data contoh di database mitra
        if (DB::connection('tenant')->table('cabangs')->count() === 0) {
            DB::connection('tenant')->table('cabangs')->insert([
                ['name' => 'Cabang Pusat',  'slug' => 'cabang-pusat',  'code' => 'PST', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Cabang Bandung', 'slug' => 'cabang-bandung', 'code' => 'BDG', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (DB::connection('tenant')->table('pelanggans')->count() === 0) {
            $cabangId = DB::connection('tenant')->table('cabangs')->value('id');
            foreach ([
                ['Budi Santoso', 'budi@example.com', '081234567001'],
                ['Siti Aminah', 'siti@example.com', '081234567002'],
                ['Andi Wijaya', 'andi@example.com', '081234567003'],
            ] as $i => [$name, $email, $phone]) {
                DB::connection('tenant')->table('pelanggans')->insert([
                    'cabang_id'  => $cabangId,
                    'name'       => $name,
                    'slug'       => \Illuminate\Support\Str::slug($name),
                    'email'      => $email,
                    'phone'      => $phone,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ---- User milik mitra demo ----
        $pemilik = User::updateOrCreate(
            ['email' => 'pemilik@demo.test'],
            [
                'name'      => 'Pemilik Mitra Demo',
                'slug'      => 'pemilik-mitra-demo',
                'password'  => Hash::make('password'),
                'mitra_id'  => $mitra->id,
                'locale'    => 'id',
                'is_active' => true,
            ],
        );
        $pemilik->syncRoles(['pemilik_mitra']);

        $staff = User::updateOrCreate(
            ['email' => 'staff@demo.test'],
            [
                'name'      => 'Staff Mitra Demo',
                'slug'      => 'staff-mitra-demo',
                'password'  => Hash::make('password'),
                'mitra_id'  => $mitra->id,
                'locale'    => 'id',
                'is_active' => true,
            ],
        );
        $staff->syncRoles(['staff_admin']);

        $mitra->update(['owner_user_id' => $pemilik->id]);
    }
}
