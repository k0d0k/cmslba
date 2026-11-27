<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Kolom yang ditambahkan oleh migrasi ini. */
    protected array $columns = [
        'patient_number',
        'date_of_birth',
        'sex',
        'city',
        'region',
        'country',
        'registered_at',
        'archived_at',
    ];

    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Hanya tambahkan kolom yang belum ada supaya migrasi aman dijalankan
            // ulang (idempotent) dan tidak error "Duplicate column name".
            if (! Schema::hasColumn('pelanggans', 'patient_number')) {
                $table->string('patient_number')->nullable()->unique()->after('user_id');
            }
            if (! Schema::hasColumn('pelanggans', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (! Schema::hasColumn('pelanggans', 'sex')) {
                $table->string('sex')->nullable();
            }
            if (! Schema::hasColumn('pelanggans', 'city')) {
                $table->string('city')->nullable();
            }
            if (! Schema::hasColumn('pelanggans', 'region')) {
                $table->string('region')->nullable();
            }
            if (! Schema::hasColumn('pelanggans', 'country')) {
                $table->string('country', 2)->nullable();
            }
            if (! Schema::hasColumn('pelanggans', 'registered_at')) {
                $table->timestamp('registered_at')->nullable();
            }
            if (! Schema::hasColumn('pelanggans', 'archived_at')) {
                $table->timestamp('archived_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('pelanggans', 'patient_number')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                // Hapus index unique terlebih dahulu sebelum menghapus kolomnya.
                $table->dropUnique(['patient_number']);
            });
        }

        Schema::table('pelanggans', function (Blueprint $table) {
            $existing = array_values(array_filter(
                $this->columns,
                fn ($column) => Schema::hasColumn('pelanggans', $column)
            ));

            if ($existing) {
                $table->dropColumn($existing);
            }
        });
    }
};
