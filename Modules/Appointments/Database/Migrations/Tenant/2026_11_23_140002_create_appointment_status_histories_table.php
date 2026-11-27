<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_status_histories', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('appointment_id', 26)->index();
            // Relasi ke master appointments_status (statuses.id).
            $table->unsignedBigInteger('from_status_id')->nullable()->index();
            $table->unsignedBigInteger('to_status_id')->nullable()->index();
            $table->string('changed_by')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('changed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_status_histories');
    }
};
