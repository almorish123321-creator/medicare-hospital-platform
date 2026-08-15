<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalFactory extends Factory
{
    protected $model = Hospital::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Hospital',
            'address' => fake()->address(),
            'phone' => '+966' . fake()->numerify('#########'),
            'email' => fake()->unique()->companyEmail(),
            'latitude' => fake()->latitude(24, 25),
            'longitude' => fake()->longitude(45, 47),
            'status' => 'active',
            'default_language' => 'ar',
        ];
    }
}
