<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_images', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('examination_id', 26)->index();
            $table->string('disk');
            $table->string('object_key');
            $table->string('original_name')->nullable();
            $table->string('checksum', 64);
            $table->string('mime_type');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->string('uploaded_by')->nullable();
            $table->string('source')->default('manual_import');
            $table->timestamp('captured_at')->nullable();
            $table->string('instrument_label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_images');
    }
};
