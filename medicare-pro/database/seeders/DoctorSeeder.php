<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Dr. Khalid Al-Mansour',
                'email' => 'dr.khalid@medicare.com',
                'phone' => '+966507777771',
                'department_id' => 1,
                'specialty' => 'General Practitioner',
                'qualification' => 'MBBS, MD - General Medicine',
                'experience_years' => 15,
                'consultation_fee' => 200.00,
                'bio' => 'Experienced general practitioner with 15 years in internal medicine.',
            ],
            [
                'name' => 'Dr. Nawal Ibrahim',
                'email' => 'dr.nawal@medicare.com',
                'phone' => '+966507777772',
                'department_id' => 2,
                'specialty' => 'Cardiologist',
                'qualification' => 'MBBS, MD - Cardiology, FACC',
                'experience_years' => 20,
                'consultation_fee' => 500.00,
                'bio' => 'Board-certified cardiologist specializing in interventional cardiology.',
            ],
            [
                'name' => 'Dr. Fahad Al-Otaibi',
                'email' => 'dr.fahad@medicare.com',
                'phone' => '+966507777773',
                'department_id' => 3,
                'specialty' => 'Orthopedic Surgeon',
                'qualification' => 'MBBS, MS - Orthopedics',
                'experience_years' => 12,
                'consultation_fee' => 350.00,
                'bio' => 'Specialized in joint replacement and sports injuries.',
            ],
            [
                'name' => 'Dr. Mona Al-Zahrani',
                'email' => 'dr.mona@medicare.com',
                'phone' => '+966507777774',
                'department_id' => 4,
                'specialty' => 'Pediatrician',
                'qualification' => 'MBBS, DCH - Pediatrics',
                'experience_years' => 10,
                'consultation_fee' => 250.00,
                'bio' => 'Dedicated pediatrician with special interest in neonatal care.',
            ],
            [
                'name' => 'Dr. Yasser Al-Harbi',
                'email' => 'dr.yasser@medicare.com',
                'phone' => '+966507777775',
                'department_id' => 5,
                'specialty' => 'Dermatologist',
                'qualification' => 'MBBS, MD - Dermatology',
                'experience_years' => 8,
                'consultation_fee' => 300.00,
                'bio' => 'Expert in cosmetic dermatology and skin disorders.',
            ],
        ];

        foreach ($doctors as $doc) {
            $user = User::create([
                'name' => $doc['name'],
                'email' => $doc['email'],
                'phone' => $doc['phone'],
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'hospital_id' => 1,
                'status' => 'active',
                'language_preference' => 'ar',
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'department_id' => $doc['department_id'],
                'specialty' => $doc['specialty'],
                'qualification' => $doc['qualification'],
                'experience_years' => $doc['experience_years'],
                'consultation_fee' => $doc['consultation_fee'],
                'rating' => rand(35, 50) / 10.0,
                'total_reviews' => rand(10, 200),
                'bio' => $doc['bio'],
                'is_available' => true,
                'schedule_settings' => [
                    ['day' => 'saturday', 'start_time' => '09:00', 'end_time' => '13:00', 'is_working' => true],
                    ['day' => 'saturday', 'start_time' => '16:00', 'end_time' => '20:00', 'is_working' => true],
                    ['day' => 'sunday', 'start_time' => '09:00', 'end_time' => '13:00', 'is_working' => true],
                    ['day' => 'sunday', 'start_time' => '16:00', 'end_time' => '20:00', 'is_working' => true],
                    ['day' => 'monday', 'start_time' => '09:00', 'end_time' => '13:00', 'is_working' => true],
                    ['day' => 'tuesday', 'start_time' => '09:00', 'end_time' => '13:00', 'is_working' => true],
                    ['day' => 'wednesday', 'start_time' => '09:00', 'end_time' => '13:00', 'is_working' => true],
                    ['day' => 'thursday', 'start_time' => '09:00', 'end_time' => '13:00', 'is_working' => true],
                    ['day' => 'friday', 'start_time' => '00:00', 'end_time' => '00:00', 'is_working' => false],
                ],
            ]);
        }
    }
}
