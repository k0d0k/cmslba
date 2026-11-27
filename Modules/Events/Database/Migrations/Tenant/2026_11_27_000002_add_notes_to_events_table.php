<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom `notes` pada tabel events (database mitra).
 * Idempotent: hanya menambah bila kolom belum ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'notes')) {
                $table->text('notes')->nullable()->after('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
