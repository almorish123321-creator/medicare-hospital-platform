<?php

namespace Database\Factories;

use App\Models\PrescriptionItem;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionItemFactory extends Factory
{
    protected $model = PrescriptionItem::class;

    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::factory(),
            'medication_name' => fake()->randomElement(['Amoxicillin 500mg', 'Paracetamol 500mg', 'Ibuprofen 400mg', 'Omeprazole 20mg', 'Metformin 500mg']),
            'dosage' => fake()->randomElement(['500mg twice daily', '1 tablet every 8 hours', '1 capsule daily', '10ml three times daily']),
            'duration' => fake()->randomElement(['5 days', '7 days', '10 days', '14 days', '30 days']),
            'instructions' => fake()->optional()->sentence(),
        ];
    }
}
