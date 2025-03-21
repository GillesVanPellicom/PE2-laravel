<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PackageListController extends Controller
{
    public function index(Request $request)
    {

        $locations = Location::where('is_active', true)->orderBy('id')->get();
        $query = Package::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereNotIn('status', ['Delivered', 'Cancelled']);
        }

        if ($request->filled('date')) {
            $selectedDate = Carbon::parse($request->date)->toDateString();
            $query->whereRaw("DATE(created_at) = ?", [$selectedDate]);
        }

        $packagesByLocation = $locations->mapWithKeys(function ($location) use ($query) {
            $packages = (clone $query)->where('current_location_id', $location->id)->get();

            return $packages->isNotEmpty() ? [$location->description => $packages] : [];
        });

        return view('packagelist', compact('packagesByLocation'));
    }
}
