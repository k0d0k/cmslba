<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments_service_type')) {
            Schema::create('appointments_service_type', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        $now = now();
        foreach (['Konsultasi', 'Pemeriksaan LBA', 'Tindak Lanjut', 'Lainnya'] as $name) {
            if (! DB::table('appointments_service_type')->where('name', $name)->exists()) {
                DB::table('appointments_service_type')->insert([
                    'name'       => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments_service_type');
    }
};
