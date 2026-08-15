<?php

namespace Database\Factories;

use App\Models\QueueLog;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class QueueLogFactory extends Factory
{
    protected $model = QueueLog::class;

    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'queue_number' => fake()->numberBetween(1, 50),
            'status' => fake()->randomElement(['waiting', 'in_progress', 'completed', 'skipped']),
            'estimated_wait_time' => fake()->numberBetween(5, 60),
            'called_at' => fake()->optional()->dateTime(),
        ];
    }

    public function waiting(): static
    {
        return $this->state(fn () => [
            'status' => 'waiting',
            'called_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => 'in_progress',
            'called_at' => now(),
        ]);
    }
}
