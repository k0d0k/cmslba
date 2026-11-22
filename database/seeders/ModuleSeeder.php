<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

/**
 * Registry modul bawaan. Dipakai sebagai sumber pilihan modul pada form langganan.
 */
class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['code' => 'appointments',  'name' => 'Appointments'],
            ['code' => 'consultations', 'name' => 'Consultations'],
            ['code' => 'knowledge_base', 'name' => 'KnowledgeBase'],
            ['code' => 'demo.showcase', 'name' => 'DemoShowcase'],
            ['code' => 'patients',      'name' => 'Patients'],
            ['code' => 'lba',           'name' => 'LBA'],
            ['code' => 'practitioners', 'name' => 'Practitioners'],
            ['code' => 'commerce',      'name' => 'Commerce'],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['code' => $module['code']],
                ['name' => $module['name'], 'is_active' => true],
            );
        }
    }
}
