<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_detections', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('examination_id', 26)->index();
            $table->char('image_id', 26)->index();
            $table->char('job_id', 26)->index();
            $table->string('provenance')->default('ai');
            $table->string('label');
            $table->json('geometry');
            $table->double('confidence');
            $table->string('model_name')->nullable();
            $table->string('model_version')->nullable();
            $table->json('quality_flags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_detections');
    }
};
