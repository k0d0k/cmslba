<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_analysis_jobs', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('examination_id', 26)->index();
            $table->char('image_id', 26)->index();
            $table->string('request_ref');
            $table->string('status')->default('pending');
            $table->string('provider');
            $table->string('model_name')->nullable();
            $table->string('model_version')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('error_code')->nullable();
            $table->boolean('billable')->default(0);
            $table->string('pricing_rule_ref')->nullable();
            $table->unsignedInteger('unit_price_credits')->nullable();
            $table->string('credits_state')->nullable();
            $table->string('idempotency_key');
            $table->string('requested_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_analysis_jobs');
    }
};
