<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'appointment_id' => Appointment::factory(),
            'doctor_id' => Doctor::factory(),
            'vital_signs' => [
                'blood_pressure' => fake()->randomElement(['120/80', '130/85', '110/70']),
                'temperature' => fake()->randomFloat(1, 36.0, 38.5),
                'weight' => fake()->randomFloat(1, 50, 100),
                'height' => fake()->randomFloat(1, 150, 190),
                'heart_rate' => fake()->numberBetween(60, 100),
            ],
            'symptoms' => fake()->optional()->text(),
            'diagnosis' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->text(),
        ];
    }
}
