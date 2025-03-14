<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageListController extends Controller
{
    public function index()
    {
        $locations = Location::where('is_active', true)->get();

        $packagesByLocation = [];
        foreach ($locations as $location) {
            $packages = Package::where(function ($query) use ($location) {
                $query->where('origin_location_id', $location->id)
                      ->orWhere('current_location_id', $location->id)
                      ->orWhere('destination_location_id', $location->id);
            })->get();

            $packagesByLocation[$location->name] = $packages;
        }

        return view('packagelist', compact('packagesByLocation'));
    }
}
