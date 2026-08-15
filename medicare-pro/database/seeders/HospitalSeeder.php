<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = [
            [
                'name' => 'MediCare General Hospital',
                'name_en' => 'MediCare General Hospital',
                'address' => '123 King Fahd Road, Riyadh',
                'address_en' => '123 King Fahd Road, Riyadh',
                'phone' => '+966112345678',
                'email' => 'info@medicare-riyadh.com',
                'latitude' => '24.7135517',
                'longitude' => '46.6752957',
                'status' => 'active',
                'subscription_plan_id' => 2,
                'subscription_expires_at' => now()->addYear(),
                'default_language' => 'ar',
            ],
            [
                'name' => 'MediCare Jeddah Medical Center',
                'name_en' => 'MediCare Jeddah Medical Center',
                'address' => '456 Palestine Street, Jeddah',
                'address_en' => '456 Palestine Street, Jeddah',
                'phone' => '+966129876543',
                'email' => 'info@medicare-jeddah.com',
                'latitude' => '21.543333',
                'longitude' => '39.172222',
                'status' => 'active',
                'subscription_plan_id' => 1,
                'subscription_expires_at' => now()->addMonths(6),
                'default_language' => 'ar',
            ],
        ];

        foreach ($hospitals as $hospital) {
            Hospital::create($hospital);
        }
    }
}
