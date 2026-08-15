<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'description' => ' Suitable for small clinics',
                'price' => 99.99,
                'duration_days' => 30,
                'max_doctors' => 5,
                'max_departments' => 3,
                'max_patients_per_month' => 500,
                'features' => ['basic_appointments', 'basic_reports', 'email_notifications'],
                'status' => 'active',
            ],
            [
                'name' => 'Professional',
                'description' => 'Ideal for medium-sized hospitals',
                'price' => 299.99,
                'duration_days' => 30,
                'max_doctors' => 20,
                'max_departments' => 10,
                'max_patients_per_month' => 2000,
                'features' => ['all_basic', 'queue_management', 'analytics', 'api_access', 'sms_notifications'],
                'status' => 'active',
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large hospitals and hospital chains',
                'price' => 799.99,
                'duration_days' => 30,
                'max_doctors' => 100,
                'max_departments' => 50,
                'max_patients_per_month' => 10000,
                'features' => ['all_professional', 'multi_branch', 'custom_integrations', 'priority_support', 'white_label'],
                'status' => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
