<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription', function (Blueprint $table) {
            $table->unsignedInteger('sina_credit_per_patient_per_period')->default(0)->after('seat_limit');
        });
    }

    public function down(): void
    {
        Schema::table('subscription', function (Blueprint $table) {
            $table->dropColumn('sina_credit_per_patient_per_period');
        });
    }
};
