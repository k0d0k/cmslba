<?php

namespace Modules\User\Database\Seeders;

use Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $principal = User::updateOrCreate(
            ['email' => 'admin@lbacloud.test'],
            [
                'name'      => 'Admin Principal',
                'slug'      => 'admin-principal',
                'password'  => Hash::make('password'),
                'locale'    => 'id',
                'is_active' => true,
            ],
        );
        $principal->syncRoles(['admin_principal']);

        $user = User::updateOrCreate(
            ['email' => 'user@lbacloud.test'],
            [
                'name'      => 'User Principal',
                'slug'      => 'user-principal',
                'password'  => Hash::make('password'),
                'locale'    => 'id',
                'is_active' => true,
            ],
        );
        $user->syncRoles(['user_principal']);
    }
}
