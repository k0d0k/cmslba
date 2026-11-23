<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('order_id', 26)->index();
            $table->char('product_id', 26)->nullable()->index();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->decimal('unit_price', 14, 2);
            $table->integer('quantity');
            $table->decimal('line_total', 14, 2);
            $table->char('currency', 3);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
