<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ganti tabel `subscriptions` lama dengan tabel `subscription` (struktur baru).
        Schema::dropIfExists('subscriptions');

        Schema::create('subscription', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('code');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('status')->default('active');
            $table->decimal('price_amount', 14, 2)->default(0);
            $table->string('currency', 3)->default('IDR');
            $table->string('billing_interval')->default('monthly');
            $table->unsignedInteger('trial_days')->default(0);
            $table->unsignedInteger('seat_limit')->nullable();
            $table->json('module_keys')->nullable();
            $table->timestamps();
        });

        // ID langganan kini ULID (char 26), sesuaikan kolom relasi di mitras.
        Schema::table('mitras', function (Blueprint $table) {
            $table->string('langganan_id', 26)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription');

        Schema::table('mitras', function (Blueprint $table) {
            $table->unsignedBigInteger('langganan_id')->nullable()->change();
        });
    }
};
