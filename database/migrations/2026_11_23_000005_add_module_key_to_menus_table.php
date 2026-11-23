<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kaitkan menu dengan modul paket langganan.
     * Menu dengan module_key hanya tampil bila modulnya ada di paket mitra.
     * Menu tanpa module_key = menu sistem (selalu tampil).
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('module_key')->nullable()->index()->after('resource');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('module_key');
        });
    }
};
