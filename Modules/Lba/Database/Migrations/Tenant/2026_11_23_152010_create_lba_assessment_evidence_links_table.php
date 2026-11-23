<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_assessment_evidence_links', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('assessment_id', 26)->index();
            $table->string('indicator_key');
            $table->string('evidence_type');
            $table->string('evidence_id')->index();
            $table->string('provenance')->default('human');
            $table->text('note')->nullable();
            $table->string('linked_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_assessment_evidence_links');
    }
};
