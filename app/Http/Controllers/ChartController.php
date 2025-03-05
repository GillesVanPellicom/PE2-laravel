<?php
namespace App\Http\Controllers;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function getPackageData()
    {
        // Haal de gegevens op, bijvoorbeeld het aantal pakketten per maand
        $data = Package::selectRaw('COUNT(*) as count, DAY(created_at) as day')
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();

        // Haal de maand en het aantal uit de data
        $day = $data->pluck('day');
        $counts = $data->pluck('count');

        // Stuur de data naar de view
        return view('package-chart', compact('day', 'counts'));
    }
}