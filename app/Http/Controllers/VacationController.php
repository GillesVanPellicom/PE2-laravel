<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacation;
use Illuminate\Support\Facades\Auth;
use Laravel\Pail\ValueObjects\Origin\Console;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class VacationController extends Controller
{
    public function getPendingVacations()
    {
        $vacations = Vacation::where('approve_status', 'pending')
            ->with(['employee.user']) // Ensure employee model has a `user()` relationship
            ->get();

        $vacations->transform(function ($vacation) {
            $employee = optional($vacation->employee);
            $user = optional($employee->user);

            return [
                'id' => $vacation->vacation_id,
                'employee_name' => ($user->first_name ?? 'Unknown') . ' ' . ($user->last_name ?? ''),
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
                'approve_status' => $vacation->approve_status,
            ];
        });

        return response()->json($vacations);
    }

    public function getApprovedVacations()
    {
        $vacations = Vacation::where('approve_status', 'Approved')
            ->with(['employee.user']) // Ensure employee and user relationship is loaded
            ->get();
    
        $vacationData = $vacations->map(function ($vacation) {
            return [
                'id' => $vacation->vacation_id,
                'name' => optional($vacation->employee->user)->first_name . ' ' . optional($vacation->employee->user)->last_name,
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
            ];
        });
    
        // Debugging: Log data in Laravel (Check storage/logs/laravel.log)
        \Log::info('Approved Vacations:', $vacationData->toArray());
    
        return response()->json($vacationData);
    }
    

    public function showAllVacations()
    {
        $vacations = Vacation::all();
        return view('employees.holiday_request', compact('vacations'));
    }

    public function updateStatus(Request $request, $id)
    {
        \Log::info("Updating vacation", ['vacation_id' => $id, 'status' => $request->status]);

        $vacation = Vacation::where('vacation_id', $id)->firstOrFail(); // Use vacation_id
        $vacation->approve_status = $request->status;
        $vacation->save();

        return response()->json(['message' => "Vacation status updated to {$request->status}"]);
    }


    public function getManagerNotifications()
    {
        $vacations = Vacation::where('approve_status', 'pending')->with('employee')->get();
        return response()->json($vacations);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return response()->json(['error' => 'You must be an employee to request a vacation'], 403);
        }

        $leaveBalance = $user->employee->leave_balance;
        $requestedDays = count($request->holidays);

        if ($requestedDays > $leaveBalance) {
            return response()->json(['error' => 'Not enough remaining holidays'], 400);
        }

        $requestedDates = array_map(fn($date) => date('Y-m-d', strtotime($date)), array_keys($request->holidays));

        $existingDates = Vacation::where('employee_id', $user->employee->id)
            ->whereIn('start_date', $requestedDates)
            ->pluck('start_date')
            ->map(fn($date) => date('Y-m-d', strtotime($date)))
            ->toArray();

        $newDates = array_diff($requestedDates, $existingDates);

        if (empty($newDates)) {
            return response()->json(['error' => 'All selected days are already requested'], 400);
        }

        foreach ($newDates as $date) {
            Vacation::create([
                'employee_id' => $user->employee->id,
                'vacation_type' => 'holiday',
                'start_date' => $date,
                'end_date' => $date,
                'approve_status' => 'pending',
            ]);
        }

        $user->employee->decrement('leave_balance', count($newDates));

        return response()->json([
            'message' => 'Holiday requests saved successfully',
            'remainingHolidays' => $user->employee->leave_balance
        ]);
    }
}
