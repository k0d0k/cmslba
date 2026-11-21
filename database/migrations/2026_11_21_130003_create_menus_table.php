<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('key')->unique();
            $table->json('title');                       // {"id":"..","en":"..","ms":".."}
            $table->string('icon')->nullable();
            $table->enum('type', ['group', 'link', 'header'])->default('link');
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('permission_name')->nullable();
            $table->string('target')->default('_self');
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_crud')->default(false);
            $table->string('resource')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
