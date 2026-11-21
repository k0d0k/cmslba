<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::updateOrCreate(
            ['code' => 'P001'],
            [
                'id'               => '01m7n3gjxv8dtc4je85r72x9a3',
                'name'             => 'Platinum',
                'description'      => 'Paket Platinum',
                'status'           => 'active',
                'price_amount'     => 12000000.00,
                'currency'         => 'IDR',
                'billing_interval' => 'yearly',
                'trial_days'       => 7,
                'seat_limit'       => 10,
                'sina_credit_per_patient_per_period' => 0,
                'module_keys'      => [
                    'appointments',
                    'commerce',
                    'consultations',
                    'demo.showcase',
                    'knowledge_base',
                    'lba',
                    'patients',
                    'practitioners',
                ],
            ],
        );
    }
}
