<?php

namespace App\Sevices;

class RouteHelper
{
    
    public static function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

       
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        
        $R = 6371;

        
        $distance = $R * $c;

        return $distance;
    }
}
