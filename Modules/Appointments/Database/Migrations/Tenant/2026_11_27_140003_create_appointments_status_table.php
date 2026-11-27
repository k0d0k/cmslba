<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments_status')) {
            Schema::create('appointments_status', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        $now = now();
        foreach (['Terjadwal', 'Konfirmasi', 'Hadir', 'Tidak Hadir', 'Dibatalkan'] as $name) {
            if (! DB::table('appointments_status')->where('name', $name)->exists()) {
                DB::table('appointments_status')->insert([
                    'name'       => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments_status');
    }
};
