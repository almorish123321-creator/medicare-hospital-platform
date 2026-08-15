<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\MedicalRecord;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition(): array
    {
        return [
            'medical_record_id' => MedicalRecord::factory(),
            'doctor_id' => Doctor::factory(),
            'patient_id' => Patient::factory(),
            'diagnosis' => fake()->sentence(),
            'instructions' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pending', 'dispensed']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function dispensed(): static
    {
        return $this->state(fn () => ['status' => 'dispensed']);
    }
}
