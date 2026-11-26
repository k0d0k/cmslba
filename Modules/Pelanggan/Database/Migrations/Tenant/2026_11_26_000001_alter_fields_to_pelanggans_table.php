<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Gunakan nullable() sementara jika tabel pelanggans sudah berisi data lama
            $table->string('patient_number')->nullable()->unique()->after('user_id');
            $table->date('date_of_birth')->nullable();
            $table->string('sex')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country', 2)->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('archived_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Hapus index unique terlebih dahulu sebelum menghapus kolomnya
            $table->dropUnique(['patient_number']);
            
            $table->dropColumn([
                'patient_number',
                'date_of_birth',
                'sex',
                'city',
                'region',
                'country',
                'registered_at',
                'archived_at',
            ]);
        });
    }
};
