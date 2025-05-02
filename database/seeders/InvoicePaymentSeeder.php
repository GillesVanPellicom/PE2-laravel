<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Database\Seeder;

class InvoicePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
 
                InvoicePayment::create([
                    'reference' => 'PAY-1', 
                    'amount' => fake()->randomFloat(2, 100, 1000),
                ]);
                InvoicePayment::create([
                    'reference' => 'PAY-2', 
                    'amount' => fake()->randomFloat(2, 100, 1000),
                ]);
    }
}