<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('weight_classes')->insert([
            [
                'name' => 'Small',
                'weight_min' => 0,
                'weight_max' => 2,
                'price' => 1.99,
            ],
            [
                'name' => 'Medium',
                'weight_min' => 2,
                'weight_max' => 5,
                'price' => 2.99,
            ],
            [
                'name' => 'Large',
                'weight_min' => 5,
                'weight_max' => 10,
                'price' => 3.99,
            ]
        ]);
    }
}
