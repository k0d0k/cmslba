<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jejak audit (audit trail): mencatat seluruh aktivitas penting sejak login
 * hingga logout, termasuk perubahan data (create/update/delete) dan error/bug
 * yang terjadi di aplikasi.
 *
 * Disimpan di koneksi `central` agar aktivitas lintas mitra terpusat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Pelaku (nullable: aksi gagal / tamu seperti percobaan login).
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();   // snapshot agar tetap terbaca walau user dihapus
            $table->string('user_email')->nullable();

            // Konteks mitra saat aksi terjadi.
            $table->unsignedBigInteger('mitra_id')->nullable()->index();

            // Jenis kejadian: login, logout, login_failed, view, request, error, dst.
            $table->string('event', 40)->index();
            $table->string('module', 80)->nullable();       // key menu/modul
            $table->text('description')->nullable();        // ringkasan yang mudah dibaca

            // Subjek yang terkena aksi (mis. model yang diubah).
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();

            // Detail teknis request.
            $table->string('method', 10)->nullable();
            $table->string('route_name')->nullable();
            $table->text('url')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Data tambahan (payload yang sudah disanitasi, info error, dsb).
            $table->json('properties')->nullable();

            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
