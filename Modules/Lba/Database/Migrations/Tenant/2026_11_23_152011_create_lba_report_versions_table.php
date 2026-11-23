<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_report_versions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('assessment_id', 26)->index();
            $table->unsignedInteger('version');
            $table->string('status')->default('generated');
            $table->string('locale', 8);
            $table->string('brand_id')->nullable()->index();
            $table->json('brand_snapshot')->nullable();
            $table->string('branch_id')->nullable()->index();
            $table->json('branch_snapshot')->nullable();
            $table->string('reviewer_id')->nullable()->index();
            $table->json('reviewer_snapshot')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->string('released_by')->nullable();
            $table->string('checksum', 64);
            $table->string('disk');
            $table->string('object_key');
            $table->string('mime_type')->default('application/pdf');
            $table->unsignedBigInteger('size_bytes');
            $table->string('generated_by')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_report_versions');
    }
};
