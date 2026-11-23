<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat top-up kredit SINA AI per mitra (database pusat).
 * Menyimpan snapshot harga per kredit saat transaksi agar perhitungan tetap utuh
 * walau harga global diubah di kemudian hari.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_topups', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->foreignId('mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->char('wallet_id', 26);
            $table->foreign('wallet_id')->references('id')->on('ai_wallets')->cascadeOnDelete();
            $table->unsignedBigInteger('credits');
            $table->decimal('price_per_credit', 14, 2)->default(0);
            $table->decimal('total_amount', 16, 2)->default(0);
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['mitra_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_topups');
    }
};
