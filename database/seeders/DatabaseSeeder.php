<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Data demo: 2 mitra (tenant) dengan multi cabang & pelanggan,
     * serta user untuk seluruh 7 role.
     */
    protected function mitras(): array
    {
        return [
            [
                'slug'    => 'mitra-demo',
                'name'    => 'Mitra Demo',
                'email'   => 'info@mitrademo.test',
                'phone'   => '021-5550001',
                'address' => 'Jl. Merdeka No. 1, Jakarta',
                'plan'    => 'pro',
                'cabangs' => [
                    ['name' => 'Cabang Pusat',   'code' => 'PST'],
                    ['name' => 'Cabang Bandung', 'code' => 'BDG'],
                    ['name' => 'Cabang Surabaya', 'code' => 'SBY'],
                ],
                'pelanggans' => [
                    ['Budi Santoso',  'budi@example.com',  '081234567001'],
                    ['Siti Aminah',   'siti@example.com',  '081234567002'],
                    ['Andi Wijaya',   'andi@example.com',  '081234567003'],
                    ['Dewi Lestari',  'dewi@example.com',  '081234567004'],
                    ['Rudi Hartono',  'rudi@example.com',  '081234567005'],
                ],
                'users' => [
                    ['name' => 'Pemilik Mitra Demo',  'email' => 'pemilik@demo.test',  'role' => 'pemilik_mitra'],
                    ['name' => 'Admin Mitra Demo',    'email' => 'admin@demo.test',    'role' => 'admin_mitra'],
                    ['name' => 'Staff Admin Demo',    'email' => 'staff@demo.test',    'role' => 'staff_admin'],
                    ['name' => 'Operator Mitra Demo', 'email' => 'operator@demo.test', 'role' => 'operator'],
                    ['name' => 'Keuangan Mitra Demo', 'email' => 'keuangan@demo.test', 'role' => 'keuangan_penjualan'],
                ],
            ],
            [
                'slug'    => 'klinik-sehat',
                'name'    => 'Klinik Sehat Bersama',
                'email'   => 'info@kliniksehat.test',
                'phone'   => '021-5550002',
                'address' => 'Jl. Sehat No. 10, Depok',
                'plan'    => 'basic',
                'cabangs' => [
                    ['name' => 'Klinik Pusat',   'code' => 'KLT'],
                    ['name' => 'Klinik Cibubur', 'code' => 'CBR'],
                ],
                'pelanggans' => [
                    ['Ahmad Fauzi',  'ahmad@example.com', '081234567101'],
                    ['Lina Marlina', 'lina@example.com',  '081234567102'],
                    ['Joko Susilo',  'joko@example.com',  '081234567103'],
                ],
                'users' => [
                    ['name' => 'Pemilik Klinik Sehat', 'email' => 'pemilik@klinik.test', 'role' => 'pemilik_mitra'],
                    ['name' => 'Staff Klinik Sehat',   'email' => 'staff@klinik.test',   'role' => 'staff_admin'],
                ],
            ],
        ];
    }

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SkinSeeder::class,
            MenuSeeder::class,
            RoleMenuSeeder::class,
            UserSeeder::class,
        ]);

        $tenant = app(TenantService::class);

        foreach ($this->mitras() as $data) {
            $mitra = Mitra::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name'    => $data['name'],
                    'email'   => $data['email'],
                    'phone'   => $data['phone'],
                    'address' => $data['address'],
                    'locale'  => 'id',
                    'status'  => 'active',
                    'plan'    => $data['plan'],
                ],
            );

            // Buat database mitra + jalankan migrasi khusus tenant
            $tenant->provision($mitra);
            $tenant->connect($mitra);

            $this->seedTenantData($data['cabangs'], $data['pelanggans']);

            // User milik mitra (semua role lingkup mitra)
            $owner = null;
            foreach ($data['users'] as $userData) {
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name'      => $userData['name'],
                        'password'  => Hash::make('password'),
                        'mitra_id'  => $mitra->id,
                        'locale'    => 'id',
                        'is_active' => true,
                    ],
                );
                $user->syncRoles([$userData['role']]);

                if ($userData['role'] === 'pemilik_mitra') {
                    $owner = $user;
                }
            }

            $mitra->update(['owner_user_id' => $owner?->id]);
        }

        $tenant->disconnect();
    }

    /** Isi data contoh (cabang & pelanggan) di database mitra. */
    protected function seedTenantData(array $cabangs, array $pelanggans): void
    {
        $conn = DB::connection('tenant');

        if ($conn->table('cabangs')->count() === 0) {
            foreach ($cabangs as $cabang) {
                $conn->table('cabangs')->insert([
                    'name'       => $cabang['name'],
                    'slug'       => Str::slug($cabang['name']),
                    'code'       => $cabang['code'],
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($conn->table('pelanggans')->count() === 0) {
            $cabangIds = $conn->table('cabangs')->orderBy('id')->pluck('id')->all();
            $total = count($cabangIds);

            foreach ($pelanggans as $i => [$name, $email, $phone]) {
                $conn->table('pelanggans')->insert([
                    'cabang_id'  => $cabangIds[$i % $total],
                    'name'       => $name,
                    'slug'       => Str::slug($name),
                    'email'      => $email,
                    'phone'      => $phone,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
