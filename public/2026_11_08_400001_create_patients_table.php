<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3 — tenant patients (Modules/Patients).
     *
     * Lives inside the tenant's isolated clinical database: several branches of
     * one tenant share this database, and a patient NEVER duplicates the tenant
     * identity (there is no tenant_id column — the tenant is implied by which
     * database the row lives in; ARCH-05). A patient's history therefore spans
     * every branch of the tenant.
     *
     * The clinic patient number is unique PER TENANT (the database is the
     * tenant boundary). There is no national-ID requirement: a national
     * identifier is never a mandatory or identifying column here.
     *
     * Archiving is non-destructive: `status` becomes archived and `archived_at`
     * records when; patient rows are never deleted.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('patient_number')->unique();
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('sex')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country', 2)->nullable();
            $table->ulid('registered_branch_id')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index('full_name');
            $table->index('phone');
            $table->index('status');
            $table->index('registered_branch_id');

            $table->foreign('registered_branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
