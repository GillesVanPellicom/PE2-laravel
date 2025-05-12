<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\InvoicePayment;
use App\Models\Invoice;

class InvoicePaymentFactory extends Factory
{
    protected $model = InvoicePayment::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10, 500), // Adjust as needed
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
}
