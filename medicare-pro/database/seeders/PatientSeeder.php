<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // Patient for the demo user (Mohammed Al-Salem)
        Patient::create([
            'user_id' => 7, // The patient user created in UserSeeder
            'blood_type' => 'A+',
            'allergies' => 'Penicillin, Dust mites',
            'chronic_diseases' => 'Type 2 Diabetes (controlled)',
            'emergency_contact_name' => 'Abdullah Al-Salem',
            'emergency_contact_phone' => '+966507777780',
            'date_of_birth' => '1985-03-15',
            'gender' => 'male',
            'address' => 'Riyadh, Al-Olaya District, Street 45',
        ]);

        $patients = [
            ['user_id' => 7, 'blood_type' => null, 'gender' => 'male', 'date_of_birth' => '1990-06-20'],
        ];

        // Create 10 more patients using the User factory
        for ($i = 1; $i <= 10; $i++) {
            $user = \App\Models\User::create([
                'name' => "Patient {$i}",
                'email' => "patient{$i}@example.com",
                'phone' => "+96650666666" . $i,
                'password' => bcrypt('password123'),
                'role' => 'patient',
                'hospital_id' => 1,
                'status' => 'active',
            ]);

            Patient::create([
                'user_id' => $user->id,
                'blood_type' => collect(['A+', 'A-', 'B+', 'B-', 'O+', 'O-'])->random(),
                'gender' => collect(['male', 'female'])->random(),
                'date_of_birth' => now()->subYears(rand(18, 70))->toDateString(),
            ]);
        }
    }
}
