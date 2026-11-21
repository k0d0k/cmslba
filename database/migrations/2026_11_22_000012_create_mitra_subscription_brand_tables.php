<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_subscriptions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('reference')->unique();
            $table->foreignId('mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->char('subscriptions_id', 26);
            $table->foreign('subscriptions_id')->references('id')->on('subscription');
            $table->string('status')->default('pending');
            $table->unsignedInteger('seats')->default(1);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['mitra_id', 'status']);
        });

        Schema::create('mitra_brands', function (Blueprint $table) {
            $table->foreignId('mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->primary(['mitra_id', 'brand_id']);
        });

        $this->migrateLegacySelections();
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_brands');
        Schema::dropIfExists('mitra_subscriptions');
    }

    private function migrateLegacySelections(): void
    {
        $now = now();

        foreach (DB::table('mitras')->get(['id', 'brand_id', 'langganan_id']) as $mitra) {
            if ($mitra->brand_id && DB::table('brands')->where('id', $mitra->brand_id)->exists()) {
                DB::table('mitra_brands')->insertOrIgnore([
                    'mitra_id' => $mitra->id,
                    'brand_id' => $mitra->brand_id,
                    'is_primary' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if (! $mitra->langganan_id) {
                continue;
            }

            $package = DB::table('subscription')->where('id', $mitra->langganan_id)->first();
            if (! $package) {
                continue;
            }

            $periodEnd = match ($package->billing_interval) {
                'yearly' => $now->copy()->addYear(),
                'weekly' => $now->copy()->addWeek(),
                'daily' => $now->copy()->addDay(),
                default => $now->copy()->addMonth(),
            };
            $trialEnd = (int) $package->trial_days > 0
                ? $now->copy()->addDays((int) $package->trial_days)
                : null;

            DB::table('mitra_subscriptions')->insert([
                'id' => (string) Str::ulid(),
                'reference' => 'sub_' . Str::lower(Str::random(24)),
                'mitra_id' => $mitra->id,
                'subscriptions_id' => $package->id,
                'status' => $trialEnd ? 'trialing' : 'active',
                'seats' => 1,
                'starts_at' => $now,
                'trial_ends_at' => $trialEnd,
                'current_period_start' => $now,
                'current_period_end' => $periodEnd,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
