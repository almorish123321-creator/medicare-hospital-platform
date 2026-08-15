<?php

namespace Database\Factories;

use App\Models\Medication;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    public function definition(): array
    {
        return [
            'hospital_id' => Hospital::factory(),
            'name' => fake()->randomElement(['Amoxicillin 500mg', 'Paracetamol 500mg', 'Ibuprofen 400mg', 'Omeprazole 20mg', 'Aspirin 100mg']),
            'generic_name' => fake()->word(),
            'category' => fake()->randomElement(['Antibiotic', 'Analgesic', 'Anti-inflammatory', 'Antacid', 'Vitamin']),
            'stock_quantity' => fake()->numberBetween(10, 200),
            'unit' => fake()->randomElement(['tablets', 'capsules', 'bottles', 'sachets']),
            'price' => fake()->randomFloat(2, 5, 100),
            'expiry_date' => fake()->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d'),
            'status' => 'available',
        ];
    }
}
