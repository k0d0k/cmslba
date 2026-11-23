<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('brand_id')->nullable()->index();
            $table->char('category_id', 26)->nullable()->index();
            $table->string('sku');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs');
            $table->string('kind')->default('product');
            $table->char('base_currency', 3)->default('IDR');
            $table->decimal('base_price', 14, 2)->default(0);
            $table->boolean('track_inventory')->default(true);
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
