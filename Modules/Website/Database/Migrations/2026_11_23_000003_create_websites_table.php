<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website / frontpage per pemilik:
 *  - mitra_id NULL  => website platform (dikelola Principal)
 *  - mitra_id terisi => website milik mitra tertentu (dikelola mitra)
 *
 * Konten disimpan sebagai JSON multi-bahasa (id, en, ms) + media bersama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('mitra_id')->nullable()->constrained('mitras')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('template')->default('default');
            $table->string('status')->default('draft'); // draft | published
            $table->json('content')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique('mitra_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
