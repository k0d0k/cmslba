<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lba_assessment_events', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('assessment_id', 26)->index();
            $table->string('action');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->string('indicator_key')->nullable();
            $table->string('actor_id')->nullable()->index();
            $table->text('note')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lba_assessment_events');
    }
};
