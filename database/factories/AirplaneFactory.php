<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class AirplaneFactory extends Factory
{

    public function definition(): array
    {

        return [
            
            'airline_id' => \App\Models\Airline::inRandomOrder()->first()->id,
            'model' => $this->faker->randomElement([
                'Boeing 737',
                'Boeing 747',
                'Boeing 777',
                'Boeing 787 Dreamliner',
                'Airbus A320',
                'Airbus A330',
                'Airbus A350',
                'Airbus A380',
                'Cessna 172',
                'Cessna Citation X',
                'Cessna Caravan',
                'Embraer E190',
                'Embraer E175',
                'Embraer Phenom 300',
                'Bombardier CRJ900',
                'Bombardier Q400',
                'Bombardier Global 7500',
                'Gulfstream G650',
                'Gulfstream G550',
                'Piper PA-28 Cherokee',
                'Piper PA-46 Malibu',
                'Lockheed Martin C-130 Hercules',
                'Lockheed Martin F-35 Lightning II',
                'Douglas DC-3',
                'Beechcraft King Air',
                'Beechcraft Bonanza',
                'Sikorsky S-76 (Helicopter)',
                'Antonov An-225 Mriya',
                'Antonov An-124 Ruslan',
                'McDonnell Douglas MD-80',
                'McDonnell Douglas MD-11',
                'Concorde',
                'De Havilland DHC-2 Beaver',
                'De Havilland Dash 8',
                'Pilatus PC-12',
                'Pilatus PC-24',
                'Learjet 75',
                'Learjet 60XR',
                'Dassault Falcon 2000',
                'Dassault Falcon 7X',
                'Hawker 800XP',
                'Bristow Sikorsky S-92 (Helicopter)',
                'Bell 206 (Helicopter)',
                'Bell 429 (Helicopter)',
                'Eurocopter EC130 (Helicopter)',
                'Eurofighter Typhoon',
                'Mitsubishi Regional Jet (MRJ90)',
            ]),
            'capacity' => $this->faker->numberBetween(10000, 200000),
            'status' => $this->faker->randomElement(['active', 'inactive']),

        ];
    }
}
