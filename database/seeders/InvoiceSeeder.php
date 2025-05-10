<?php

namespace Database\Seeders;

use App\Models\InvoicePayment;
use Faker\Factory;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    // database/seeders/InvoiceSeeder.php
    public function run()
    {
        $faker = Factory::create();

        Invoice::factory()
            ->count(30)
            ->create()
            ->each(function($invoice) use ($faker) {
                $packageIds = Package::inRandomOrder()->limit(rand(5,10))->pluck('id');
                foreach ($packageIds as $packageId) {
                    DB::table('packages_in_invoice')->insert([
                        'invoice_id' => $invoice->id,
                        'package_id' => $packageId
                    ]);
                }

                    InvoicePayment::factory()->create([
                        'reference' => $invoice->reference,
                    ]);
            });
    }
}
