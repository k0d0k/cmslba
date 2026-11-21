<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crud_definitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_name');
            $table->string('table_name');
            $table->string('connection')->default('tenant');
            $table->unsignedBigInteger('menu_id')->nullable()->index();
            $table->string('icon')->nullable();
            $table->json('fields');
            $table->json('roles')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crud_definitions');
    }
};
