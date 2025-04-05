<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;


class LocationController extends Controller
{
    //
    public static function getAddressString(Location $location){
        $address = $location->address->city->name . ", " . $location->address->street . " " . $location->address->house_number;
        if ($location->address->bus_number) {
            $address .= $location->address->bus_number;
        }
        return $address;

    }
}
