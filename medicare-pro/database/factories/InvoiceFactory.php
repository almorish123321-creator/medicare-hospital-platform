<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 50, 500);
        $tax = round($amount * 0.15, 2);

        return [
            'patient_id' => Patient::factory(),
            'appointment_id' => null,
            'hospital_id' => Hospital::factory(),
            'amount' => $amount,
            'discount' => 0,
            'tax' => $tax,
            'total_amount' => round($amount + $tax, 2),
            'status' => 'pending',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }
}
