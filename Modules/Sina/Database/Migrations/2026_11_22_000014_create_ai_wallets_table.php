<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dompet kredit SINA AI per mitra — satu baris untuk setiap mitra.
 * Saldo awal berasal dari paket langganan (kredit/pasien/periode × kursi),
 * dan dapat ditambah (top-up) sebelum masa berlangganan berakhir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_wallets', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->foreignId('mitra_id')->unique()->constrained('mitras')->cascadeOnDelete();
            $table->unsignedBigInteger('balance_credits')->default(0);
            $table->unsignedBigInteger('granted_credits')->default(0);
            $table->unsignedBigInteger('topped_up_credits')->default(0);
            $table->unsignedBigInteger('used_credits')->default(0);
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->timestamp('last_granted_at')->nullable();
            $table->timestamp('last_topped_up_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_wallets');
    }
};
