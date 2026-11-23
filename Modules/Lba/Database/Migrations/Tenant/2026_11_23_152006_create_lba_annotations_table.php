<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_annotations', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('examination_id', 26)->index();
            $table->char('image_id', 26)->index();
            $table->string('job_id')->nullable()->index();
            $table->string('provenance')->default('human');
            $table->string('type');
            $table->json('geometry');
            $table->string('label')->nullable();
            $table->text('notes')->nullable();
            $table->string('actor_id')->nullable()->index();
            $table->unsignedInteger('revision')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_annotations');
    }
};
