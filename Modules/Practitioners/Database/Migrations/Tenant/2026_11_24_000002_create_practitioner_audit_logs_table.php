<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practitioner_audit_logs', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('practitioner_id', 26)->index();
            $table->string('action');
            $table->string('actor_id')->nullable();
            $table->json('changes')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitioner_audit_logs');
    }
};
