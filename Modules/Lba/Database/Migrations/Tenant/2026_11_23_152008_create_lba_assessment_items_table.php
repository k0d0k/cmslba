<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_assessment_items', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('assessment_id', 26)->index();
            $table->string('indicator_key');
            $table->string('catalog_version');
            $table->text('source_label');
            $table->string('observation_label')->nullable();
            $table->string('terminology_status')->nullable();
            $table->string('terminology_approved_by')->nullable();
            $table->timestamp('terminology_approved_at')->nullable();
            $table->string('source_column')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('category')->default('unassessed');
            $table->text('notes')->nullable();
            $table->string('assessor_id')->nullable()->index();
            $table->timestamp('assessed_at')->nullable();
            $table->unsignedInteger('revision')->default(1);
            $table->string('provenance')->default('human');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_assessment_items');
    }
};
