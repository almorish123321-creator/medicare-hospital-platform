<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'hospital_id' => 1,
            'name' => fake()->randomElement(['Cardiology', 'Orthopedics', 'Pediatrics', 'Dermatology', 'ENT', 'General Medicine']),
            'description' => fake()->sentence(),
            'icon' => fake()->word(),
            'status' => 'active',
        ];
    }
}
