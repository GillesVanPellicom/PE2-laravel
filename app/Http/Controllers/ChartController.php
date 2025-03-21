<?php
namespace App\Http\Controllers;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function getPackageData(Request $request)
    {   
        $selectedYear = $request->input('year', Carbon::now()->year);
        $groupBy = $request->input('group_by', 'day'); // Standaard per dag

        $availableYears = Package::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')                
            ->pluck('year');
    
        if ($groupBy == 'week') {
            $dateFormat = "YEAR(created_at) as year, WEEK(created_at) as period";
            $groupByClause = "YEAR(created_at), WEEK(created_at)";
            $orderBy = "YEAR(created_at), WEEK(created_at)";
        } elseif ($groupBy == 'month') {
            $dateFormat = "YEAR(created_at) as year, MONTH(created_at) as period";
            $groupByClause = "YEAR(created_at), MONTH(created_at)";
            $orderBy = "YEAR(created_at), MONTH(created_at)";
        } else {
            $dateFormat = "DATE_FORMAT(created_at, '%d-%m-%Y') as period";
            $groupByClause = "period";
            $orderBy = "STR_TO_DATE(period, '%d-%m-%Y')";
        }

        // Inkomende pakketten
        $incomingData = Package::selectRaw("COUNT(*) as count, $dateFormat")
            ->whereYear('created_at', $selectedYear)
            ->whereIn('status', ['Pending'])
            ->groupByRaw($groupByClause)
            ->orderByRaw($orderBy) 
            ->get();
    
        // Uitgaande pakketten
        $outgoingData = Package::selectRaw("COUNT(*) as count, $dateFormat")
            ->whereYear('created_at', $selectedYear)    
            ->whereIn('status', ['Delivered'])
            ->groupByRaw($groupByClause)
            ->orderByRaw($orderBy) 
            ->get();
    

        if ($groupBy == 'week') {
            $incomingDays = $incomingData->map(fn($item) => "Week " . $item->period);
            $outgoingDays = $outgoingData->map(fn($item) => "Week " . $item->period);
        } elseif ($groupBy == 'month') {
            $incomingDays = $incomingData->map(fn($item) => "Maand " . $item->period);
            $outgoingDays = $outgoingData->map(fn($item) => "Maand " . $item->period);
        } else {
            $incomingDays = $incomingData->pluck('period');
            $outgoingDays = $outgoingData->pluck('period');
        }   
        
        return view('packagechart', [
            'incomingDays' => $incomingDays->values(),
            'incomingCounts' => $incomingData->pluck('count'),
            'outgoingDays' => $outgoingDays->values(),
            'outgoingCounts' => $outgoingData->pluck('count'),
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'groupBy' => $groupBy
        ]);
        
        
    }
}
