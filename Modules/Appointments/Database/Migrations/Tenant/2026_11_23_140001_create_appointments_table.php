<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('patient_id', 26)->index();
            $table->char('branch_id', 26)->index();
            $table->string('staff_user_id')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('service_type');
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
