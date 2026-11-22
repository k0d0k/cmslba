<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Konfigurasi penyedia AI (SINA AI) per mitra — disimpan pada database pusat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_provider_configs', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->foreignId('mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->string('provider');
            $table->string('label')->nullable();
            $table->string('base_url');
            $table->string('model');
            $table->text('api_key')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('enabled')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['mitra_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_provider_configs');
    }
};
