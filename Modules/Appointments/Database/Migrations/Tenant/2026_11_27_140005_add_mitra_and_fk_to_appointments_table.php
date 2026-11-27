<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi lanjutan untuk database mitra yang sudah lebih dulu dibuat
 * (menggantikan kolom string lama menjadi kolom relasi/FK ke tabel master):
 *   - appointments.mitra_uuid      -> mitras.uuid (database pusat)
 *   - appointments.status          -> status_id (FK appointments_status.id)
 *   - appointments.service_type    -> service_type_id (FK appointments_service_type.id)
 *   - appointment_status_histories.from_status -> from_status_id
 *   - appointment_status_histories.to_status   -> to_status_id
 *
 * Idempotent: aman dijalankan pada database baru maupun lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'mitra_uuid')) {
                $table->string('mitra_uuid')->nullable()->index();
            }
            if (! Schema::hasColumn('appointments', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->index();
            }
            if (! Schema::hasColumn('appointments', 'service_type_id')) {
                $table->unsignedBigInteger('service_type_id')->nullable()->index();
            }
            if (Schema::hasColumn('appointments', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('appointments', 'service_type')) {
                $table->dropColumn('service_type');
            }
        });

        Schema::table('appointment_status_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('appointment_status_histories', 'from_status_id')) {
                $table->unsignedBigInteger('from_status_id')->nullable()->index();
            }
            if (! Schema::hasColumn('appointment_status_histories', 'to_status_id')) {
                $table->unsignedBigInteger('to_status_id')->nullable()->index();
            }
            if (Schema::hasColumn('appointment_status_histories', 'from_status')) {
                $table->dropColumn('from_status');
            }
            if (Schema::hasColumn('appointment_status_histories', 'to_status')) {
                $table->dropColumn('to_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'mitra_uuid')) {
                $table->dropColumn('mitra_uuid');
            }
            if (Schema::hasColumn('appointments', 'status_id')) {
                $table->dropColumn('status_id');
            }
            if (Schema::hasColumn('appointments', 'service_type_id')) {
                $table->dropColumn('service_type_id');
            }
        });

        Schema::table('appointment_status_histories', function (Blueprint $table) {
            if (Schema::hasColumn('appointment_status_histories', 'from_status_id')) {
                $table->dropColumn('from_status_id');
            }
            if (Schema::hasColumn('appointment_status_histories', 'to_status_id')) {
                $table->dropColumn('to_status_id');
            }
        });
    }
};
