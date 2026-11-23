<?php

namespace Modules\Setting\Database\Seeders;

use Modules\Setting\Models\Skin;
use Illuminate\Database\Seeder;

class SkinSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('lbacloud.skins') as $code => $skin) {
            Skin::updateOrCreate(['code' => $code], [
                'label'     => $skin['label'],
                'primary'   => $skin['primary'],
                'sidebar'   => $skin['sidebar'],
                'is_active' => true,
            ]);
        }
    }
}
