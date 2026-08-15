<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => \App\Models\Patient::factory(),
            'doctor_id' => \App\Models\Doctor::factory(),
            'hospital_id' => 1,
            'department_id' => 1,
            'appointment_date' => fake()->date(),
            'appointment_time' => fake()->time('H:i'),
            'queue_number' => fake()->numberBetween(1, 100),
            'status' => fake()->randomElement(['pending', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show']),
            'type' => fake()->randomElement(['booked', 'walk_in']),
            'symptoms' => fake()->optional()->text(),
            'notes' => fake()->optional()->text(),
        ];
    }
}