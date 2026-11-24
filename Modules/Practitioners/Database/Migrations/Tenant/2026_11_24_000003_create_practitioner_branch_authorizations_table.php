<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_branch_authorizations', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('practitioner_id', 26)->index();
            $table->char('branch_id', 26)->index();
            $table->string('status')->default('active');
            $table->string('authorized_by')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_branch_authorizations');
    }
};
