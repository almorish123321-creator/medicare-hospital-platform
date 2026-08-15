<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory()->doctor(),
            'department_id' => 1,
            'specialty' => fake()->randomElement(['General Medicine', 'Cardiology', 'Orthopedics', 'Pediatrics', 'Dermatology']),
            'qualification' => 'MBBS, MD',
            'experience_years' => fake()->numberBetween(1, 30),
            'consultation_fee' => fake()->randomFloat(2, 100, 500),
            'rating' => fake()->randomFloat(1, 3.5, 5.0),
            'total_reviews' => fake()->numberBetween(0, 500),
            'bio' => fake()->paragraph(),
            'is_available' => true,
            'schedule_settings' => [
                ['day' => 'saturday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_working' => true],
                ['day' => 'sunday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_working' => true],
                ['day' => 'monday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_working' => true],
                ['day' => 'tuesday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_working' => true],
                ['day' => 'wednesday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_working' => true],
                ['day' => 'thursday', 'start_time' => '09:00', 'end_time' => '13:00', 'is_working' => true],
                ['day' => 'friday', 'start_time' => '00:00', 'end_time' => '00:00', 'is_working' => false],
            ],
        ];
    }
}
