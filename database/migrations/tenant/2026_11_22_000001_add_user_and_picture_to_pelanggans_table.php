<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Akun login pelanggan (tabel users di database pusat).
            $table->unsignedBigInteger('user_id')->nullable()->index()->after('cabang_id');
            // Foto pelanggan.
            $table->string('picture')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'picture']);
        });
    }
};
