<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_assessments', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('examination_id', 26)->index();
            $table->string('catalog_version');
            $table->string('catalog_version_id')->nullable()->index();
            $table->string('mode');
            $table->string('status')->default('draft');
            $table->string('program_phase')->default('not_specified');
            $table->string('brand_id')->nullable()->index();
            $table->json('brand_snapshot')->nullable();
            $table->string('assessed_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('last_saved_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_assessments');
    }
};
