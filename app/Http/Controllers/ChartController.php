<?php
namespace App\Http\Controllers;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function getPackageData()
    {
        // Inkomende pakketten
        $incomingData = Package::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%d-%m-%Y") as day')
            ->whereIn('status', ['Pending'])
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();
    
        // Uitgaande pakketten
        $outgoingData = Package::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%d-%m-%Y") as day')
            ->whereIn('status', ['Delivered'])
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();
    
        return view('package-chart', [
            'incomingDays' => $incomingData->pluck('day'),
            'incomingCounts' => $incomingData->pluck('count'),
            'outgoingDays' => $outgoingData->pluck('day'),
            'outgoingCounts' => $outgoingData->pluck('count')
        ]);
    }
}
