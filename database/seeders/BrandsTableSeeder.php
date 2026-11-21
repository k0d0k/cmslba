<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BrandsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('brands')->delete();
        
        \DB::table('brands')->insert(array (
            0 => 
            array (
                'id' => 1,
                'sku' => '001',
                'name' => 'Babul Syiffa',
                'is_active' => 'active',
                'slug' => 'babul-syiffa',
                'created_at' => '2026-11-21 14:54:28',
                'updated_at' => '2026-11-21 14:54:28',
            ),
            1 => 
            array (
                'id' => 2,
                'sku' => 'dfsd',
                'name' => 'sdf',
                'is_active' => 'Aktif',
                'slug' => 'sdf',
                'created_at' => '2026-11-21 14:59:04',
                'updated_at' => '2026-11-21 14:59:04',
            ),
        ));
        
        
    }
}