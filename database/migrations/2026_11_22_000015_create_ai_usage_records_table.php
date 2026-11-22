<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat pemakaian kredit SINA AI per mitra (mengacu ke dompet kredit).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_records', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->foreignId('mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->char('wallet_id', 26);
            $table->foreign('wallet_id')->references('id')->on('ai_wallets')->cascadeOnDelete();
            $table->string('job_reference');
            $table->string('provider');
            $table->string('model_name')->nullable();
            $table->string('model_version')->nullable();
            $table->string('pricing_rule_ref')->nullable();
            $table->unsignedInteger('unit_price_credits')->default(0);
            $table->unsignedInteger('credit_units')->default(0);
            $table->string('idempotency_key')->unique();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['mitra_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_records');
    }
};
