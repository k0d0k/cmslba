<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MitrasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('mitras')->delete();
        
        \DB::table('mitras')->insert(array (
            0 => 
            array (
                'id' => 1,
                'uuid' => '863aebba-a764-499e-9ce2-417f848497b0',
                'name' => 'Mitra Demo',
                'slug' => 'mitra-demo',
                'db_name' => 'lbacloud_mitra_863aebba',
                'domain' => NULL,
                'email' => 'info@mitrademo.test',
                'phone' => '021-5550001',
                'address' => 'Jl. Merdeka No. 1, Jakarta',
                'logo' => NULL,
                'skin' => 'default',
                'locale' => 'id',
                'status' => 'active',
                'plan' => 'pro',
                'owner_user_id' => 3,
                'expired_at' => NULL,
                'created_at' => '2026-11-21 14:42:49',
                'updated_at' => '2026-11-21 14:42:51',
            ),
            1 => 
            array (
                'id' => 2,
                'uuid' => '57fefa69-c348-4c04-9c42-8ae110beabbd',
                'name' => 'Klinik Sehat Bersama',
                'slug' => 'klinik-sehat',
                'db_name' => 'lbacloud_mitra_57fefa69',
                'domain' => NULL,
                'email' => 'info@kliniksehat.test',
                'phone' => '021-5550002',
                'address' => 'Jl. Sehat No. 10, Depok',
                'logo' => NULL,
                'skin' => 'default',
                'locale' => 'id',
                'status' => 'active',
                'plan' => 'basic',
                'owner_user_id' => 8,
                'expired_at' => NULL,
                'created_at' => '2026-11-21 14:42:51',
                'updated_at' => '2026-11-21 14:42:53',
            ),
        ));
        
        
    }
}