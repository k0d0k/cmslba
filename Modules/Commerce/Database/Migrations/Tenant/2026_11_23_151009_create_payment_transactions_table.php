<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('order_id', 26)->index();
            $table->char('invoice_id', 26)->nullable()->index();
            $table->string('provider');
            $table->string('provider_reference')->nullable();
            $table->string('method')->nullable();
            $table->char('currency', 3);
            $table->decimal('amount', 14, 2);
            $table->string('status')->default('initiated');
            $table->string('idempotency_key');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
