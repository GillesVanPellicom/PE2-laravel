<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use App\Models\RouterNodes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CourierRouteSeeder extends Seeder
{
    public function run(): void
    {
        $distributionCenters = RouterNodes::where('id', 'LIKE', '@DC_%')
            ->pluck('id')
            ->toArray();

        if (empty($distributionCenters)) {
            throw new \Exception('Geen distributiecentra gevonden in de router_nodes tabel.');
        }
        
        $employees = User::role('courier')->get()->pluck('employee')->all();
        
        $antwerpCourierCount = 0;
        $targetAntwerpCouriers = 3;
        
        foreach($employees as $employee){
            // Haal de user op die bij deze employee hoort
            $user = User::find($employee->user_id);
            
            // Bepaal de locatie voor deze koerier
            if ($user && $user->first_name === 'Bob' && $user->last_name === 'Courier') {
                $location = '@DC_ANTWERP';
                $antwerpCourierCount++;
            } elseif ($antwerpCourierCount < $targetAntwerpCouriers) {
                $location = '@DC_ANTWERP';
                $antwerpCourierCount++;
            } else {
                $location = $distributionCenters[array_rand($distributionCenters)];
            }
            
            DB::table('courier_routes')->insert([
                'courier' => $employee->id,
                'start_location' => null,
                'current_location' => $location,
                'end_location' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
