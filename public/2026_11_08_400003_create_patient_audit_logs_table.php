<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3 — minimal patient audit trail (Modules/Patients).
     *
     * A deliberately small, append-only record of who did what to a patient
     * profile (created / updated / archived / consent recorded). It stores the
     * acting identity (a central user id, no cross-database foreign key) and a
     * JSON snapshot of the changed attributes. The audit row survives a patient
     * row being force-deleted, so patient history is never silently lost.
     */
    public function up(): void
    {
        Schema::create('patient_audit_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('patient_id')->nullable();
            $table->string('action');
            $table->string('actor_id')->nullable();
            $table->json('changes')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index('patient_id');
            $table->index('action');

            $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_audit_logs');
    }
};
