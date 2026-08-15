<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hospital Admin for Hospital 1
        User::create([
            'name' => 'Dr. Ahmed Al-Rashid',
            'email' => 'admin@medicare-riyadh.com',
            'phone' => '+966501111111',
            'password' => Hash::make('password123'),
            'role' => 'hospital_admin',
            'hospital_id' => 1,
            'status' => 'active',
            'language_preference' => 'ar',
        ]);

        // Hospital Admin for Hospital 2
        User::create([
            'name' => 'Dr. Fatima Hassan',
            'email' => 'admin@medicare-jeddah.com',
            'phone' => '+966502222222',
            'password' => Hash::make('password123'),
            'role' => 'hospital_admin',
            'hospital_id' => 2,
            'status' => 'active',
            'language_preference' => 'ar',
        ]);

        // Receptionists
        User::create([
            'name' => 'Sara Mohammed',
            'email' => 'receptionist@medicare-riyadh.com',
            'phone' => '+966503333333',
            'password' => Hash::make('password123'),
            'role' => 'receptionist',
            'hospital_id' => 1,
            'status' => 'active',
            'language_preference' => 'ar',
        ]);

        // Nurses
        User::create([
            'name' => 'Nurse Layla Ali',
            'email' => 'nurse@medicare-riyadh.com',
            'phone' => '+966504444444',
            'password' => Hash::make('password123'),
            'role' => 'nurse',
            'hospital_id' => 1,
            'status' => 'active',
            'language_preference' => 'ar',
        ]);

        // Pharmacists
        User::create([
            'name' => 'Pharmacist Omar Khalid',
            'email' => 'pharmacist@medicare-riyadh.com',
            'phone' => '+966505555555',
            'password' => Hash::make('password123'),
            'role' => 'pharmacist',
            'hospital_id' => 1,
            'status' => 'active',
            'language_preference' => 'ar',
        ]);

        // Demo Patient
        User::create([
            'name' => 'Mohammed Al-Salem',
            'email' => 'patient@medicare.com',
            'phone' => '+966506666666',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'hospital_id' => 1,
            'status' => 'active',
            'language_preference' => 'ar',
        ]);
    }
}
