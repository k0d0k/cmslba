<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_credentials', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('practitioner_id', 26)->index();
            $table->string('credential_type');
            $table->string('jurisdiction')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            $table->string('evidence_disk')->nullable();
            $table->string('evidence_path')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_credentials');
    }
};
