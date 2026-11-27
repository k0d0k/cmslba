<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rename kolom events.mitra_id -> events.mitra_uuid.
 *
 * mitra_uuid menyimpan uuid mitra dan berelasi ke tabel `mitras`
 * pada database pusat (mitras.uuid), sama seperti cabangs.mitra_uuid.
 * Idempotent: aman dijalankan pada database yang belum/sudah migrasi.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom mitra_uuid (uuid mitra) bila belum ada.
        if (! Schema::hasColumn('events', 'mitra_uuid')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('mitra_uuid')->nullable()->index()->after('id');
            });
        }

        // Hapus kolom lama mitra_id bila masih ada.
        if (Schema::hasColumn('events', 'mitra_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('mitra_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'mitra_uuid')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('mitra_uuid');
            });
        }

        if (! Schema::hasColumn('events', 'mitra_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->unsignedBigInteger('mitra_id')->nullable()->index()->after('id');
            });
        }
    }
};
