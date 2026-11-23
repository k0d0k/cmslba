<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Konfigurasi payment gateway (Midtrans, Doku, iPaymu, PayPal, dsb).
 * Disimpan pada database pusat dan dipakai untuk integrasi tagihan mitra.
 * Kredensial disimpan terenkripsi (di-cast `encrypted` pada model).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('provider')->unique();   // kunci penyedia: midtrans, doku, ipaymu, paypal
            $table->string('label');
            $table->string('mode')->default('sandbox'); // sandbox | live
            $table->boolean('is_active')->default(true);
            $table->string('currency')->nullable();

            // Kredensial (terenkripsi saat disimpan).
            $table->text('server_key')->nullable();
            $table->text('client_key')->nullable();
            $table->text('api_key')->nullable();
            $table->text('virtual_account')->nullable();
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('webhook_id')->nullable();

            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
