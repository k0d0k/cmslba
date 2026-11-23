<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('patient_id', 26)->index();
            $table->char('branch_id', 26)->index();
            $table->string('staff_user_id')->nullable();
            $table->char('appointment_id', 26)->nullable()->index();
            $table->string('channel')->default('sina_chat');
            $table->string('type')->default('consultation');
            $table->string('status')->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('summary')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
