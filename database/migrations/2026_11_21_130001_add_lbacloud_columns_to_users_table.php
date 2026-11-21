<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->unsignedBigInteger('mitra_id')->nullable()->index()->after('password');
            $table->unsignedBigInteger('cabang_id')->nullable()->index()->after('mitra_id');
            $table->string('phone')->nullable()->after('cabang_id');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('locale', 5)->default('id')->after('avatar');
            $table->string('skin')->default('default')->after('locale');
            $table->boolean('is_active')->default(true)->after('skin');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'mitra_id', 'cabang_id', 'phone', 'avatar',
                'locale', 'skin', 'is_active', 'last_login_at', 'deleted_at',
            ]);
        });
    }
};
