<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+966' . fake()->numerify('#########'),
            'password' => Hash::make('password'),
            'role' => 'patient',
            'status' => 'active',
            'language_preference' => 'ar',
            'hospital_id' => null,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn() => ['role' => 'super_admin']);
    }

    public function hospitalAdmin(): static
    {
        return $this->state(fn() => ['role' => 'hospital_admin']);
    }

    public function doctor(): static
    {
        return $this->state(fn() => ['role' => 'doctor']);
    }

    public function patient(): static
    {
        return $this->state(fn() => ['role' => 'patient']);
    }
}
