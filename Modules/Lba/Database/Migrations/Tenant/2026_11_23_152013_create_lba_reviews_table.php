<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_reviews', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('assessment_id', 26)->index();
            $table->string('reviewer_id')->index();
            $table->json('reviewer_snapshot')->nullable();
            $table->string('decision');
            $table->text('notes')->nullable();
            $table->timestamp('decided_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_reviews');
    }
};
