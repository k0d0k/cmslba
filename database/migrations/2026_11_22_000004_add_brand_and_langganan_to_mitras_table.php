<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->index()->after('plan');
            $table->unsignedBigInteger('langganan_id')->nullable()->index()->after('brand_id');
        });
    }

    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn(['brand_id', 'langganan_id']);
        });
    }
};
