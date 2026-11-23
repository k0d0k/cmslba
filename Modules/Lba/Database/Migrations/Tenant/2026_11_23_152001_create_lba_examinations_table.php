<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_examinations', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('patient_id', 26)->index();
            $table->char('branch_id', 26)->index();
            $table->char('appointment_id', 26)->nullable()->index();
            $table->string('operator_user_id')->nullable()->index();
            $table->string('mode')->default('manual');
            $table->string('status')->default('draft');
            $table->timestamp('captured_at')->nullable();
            $table->string('instrument')->nullable();
            $table->string('magnification')->nullable();
            $table->json('calibration')->nullable();
            $table->text('notes')->nullable();
            $table->char('consent_id', 26)->nullable()->index();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_examinations');
    }
};
