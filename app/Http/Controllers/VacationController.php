<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacation;
use Illuminate\Support\Facades\Auth;
use Laravel\Pail\ValueObjects\Origin\Console;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use App\Models\Notification;
use App\Models\MessageTemplate;

class VacationController extends Controller
{
    public function getPendingVacations()
    {
        $vacations = Vacation::where('approve_status', 'pending')->get(); // Fixed missing method call

        $vacations->transform(function ($vacation) {
            $employee = optional($vacation->employee);
            $user = optional($employee->user);

            return [
                'id' => $vacation->vacation_id,
                'employee_name' => ($user->first_name ?? 'Unknown') . ' ' . ($user->last_name ?? ''),
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
                'approve_status' => $vacation->approve_status,
                'day_type' => $vacation->day_type, // Include day_type in the response
            ];
        });

        return response()->json($vacations);
    }

    public function getApprovedVacations()
    {
        $vacations = Vacation::where('approve_status', 'Approved')
            ->where('vacation_type', '!=', 'Sick Leave') // Exclude sick leave
            ->with(['employee.user']) // Ensure employee and user relationship is loaded
            ->get();

        $vacationData = $vacations->map(function ($vacation) {
            return [
                'id' => $vacation->vacation_id,
                'name' => optional($vacation->employee->user)->first_name . ' ' . optional($vacation->employee->user)->last_name,
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
                'day_type' => $vacation->day_type, // Include day_type in the response
                'vacation_type' => $vacation->vacation_type, // Include vacation type
            ];
        });

        // Debugging: Log data in Laravel (Check storage/logs/laravel.log)
        \Log::info('Approved Vacations:', $vacationData->toArray());

        return response()->json($vacationData);
    }

    public function getVacations()
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return response()->json(['error' => 'You must be an employee to view vacations'], 403);
        }

        $vacations = Vacation::where('employee_id', $user->employee->id) // Filter by the logged-in employee's ID
            ->with('employee.user') // Include employee and user relationships
            ->get()
            ->map(function ($vacation) {
                return [
                    'id' => $vacation->vacation_id,
                    'start_date' => $vacation->start_date,
                    'end_date' => $vacation->end_date,
                    'vacation_type' => $vacation->vacation_type,
                    'approve_status' => $vacation->approve_status,
                    'day_type' => $vacation->day_type, // Add day_type to the response
                    'name' => optional($vacation->employee->user)->first_name . ' ' . optional($vacation->employee->user)->last_name, // Include employee name
                ];
            });

        return response()->json($vacations);
    }


    public function showAllVacations()
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return redirect()->route('workspace.employees.calendar')->with('error', 'You must be an employee to view holiday requests.');
        }

        // Exclude sick leave entries
        $vacations = Vacation::where('employee_id', $user->employee->id)
            ->where('vacation_type', '!=', 'Sick Leave') // Exclude sick leave
            ->get();

        return view('employees.holiday_request', compact('vacations'));
    }

    public function updateStatus(Request $request, $id)
    {
        \Log::info("Updating vacation", ['vacation_id' => $id, 'status' => $request->status]);

        $vacation = Vacation::where('vacation_id', $id)->firstOrFail();
        $vacation->approve_status = $request->status;
        $vacation->save();

        // Fetch the employee's user ID
        $employee = $vacation->employee;

        if ($employee) {
            // If the request is rejected, increment the employee's leave balance based on day_type
            if ($request->status === 'rejected') {
                $incrementValue = ($vacation->day_type === 'Whole Day') ? 1 : 0.5; // Restore full or half day
                $employee->increment('leave_balance', $incrementValue);
            }

            // Get the appropriate message template
            $templateKey = $request->status === 'approved' ? 'holiday_approved' : 'holiday_denied';
            $template = MessageTemplate::where('key', $templateKey)->first();

            if ($template) {
                // Save the notification
                Notification::create([
                    'user_id' => $employee->user_id,
                    'message_template_id' => $template->id,
                ]);
            }
        }

        // Return the updated vacation data with color for the frontend
        $statusColors = [
            'pending' => '#FFC107',  // Yellow
            'approved' => '#28A745', // Green
            'rejected' => '#DC3545', // Red
        ];

        return response()->json([
            'id' => $vacation->vacation_id,
            'start_date' => $vacation->start_date,
            'end_date' => $vacation->end_date,
            'vacation_type' => $vacation->vacation_type,
            'approve_status' => $vacation->approve_status,
            'color' => $statusColors[strtolower($vacation->approve_status)] ?? '#6C757D', // Default to gray if status is unknown
        ]);
    }

    public function getManagerNotifications()
    {
        // Fetch vacations with 'pending' status and vacation_type "Holiday"
        $vacations = Vacation::where('approve_status', 'pending')
            ->where('vacation_type', 'Holiday')
            ->with('employee.user') // Include employee and user relationships
            ->get();

        // Transform the data for the frontend
        $notifications = $vacations->map(function ($vacation) {
            return [
                'id' => $vacation->id,
                'employee_name' => $vacation->employee->user->first_name . ' ' . $vacation->employee->user->last_name,
                'vacation_type' => $vacation->vacation_type,
                'day_type' => $vacation->day_type,
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
            ];
        });

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return response()->json(['error' => 'You must be an employee to request a vacation'], 403);
        }

        $leaveBalance = $user->employee->leave_balance;
        $requestedDays = 0;

        // Process holidays
        foreach ($request->holidays as $date => $dayType) {
            $requestedDays += ($dayType === 'Whole Day') ? 1 : 0.5; // Count half-days as 0.5
        }

        if ($requestedDays > $leaveBalance) {
            return response()->json(['error' => 'Not enough remaining holidays'], 400);
        }

        $requestedDates = array_keys($request->holidays);

        $existingDates = Vacation::where('employee_id', $user->employee->id)
            ->whereIn('start_date', $requestedDates)
            ->pluck('start_date')
            ->map(fn($date) => date('Y-m-d', strtotime($date)))
            ->toArray();

        $newDates = array_diff($requestedDates, $existingDates);

        foreach ($newDates as $date) {
            Vacation::create([
                'employee_id' => $user->employee->id,
                'vacation_type' => 'Holiday',
                'start_date' => $date,
                'end_date' => $date,
                'approve_status' => 'Pending',
                'day_type' => $request->holidays[$date], // Save the day type (Whole Day, First Half, Second Half)
            ]);
        }

        // Process sick days
        foreach ($request->sickDays as $dateRange) {
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            $current = new \DateTime($startDate);
            $end = new \DateTime($endDate);

            while ($current <= $end) {
                $formattedDate = $current->format('Y-m-d');

                // Check if a sick leave already exists for the same day
                $existingSickLeave = Vacation::where('employee_id', $user->employee->id)
                    ->where('vacation_type', 'Sick Leave')
                    ->where('start_date', $formattedDate)
                    ->exists();

                if (!$existingSickLeave) {
                    Vacation::create([
                        'employee_id' => $user->employee->id,
                        'vacation_type' => 'Sick Leave',
                        'start_date' => $formattedDate,
                        'end_date' => $formattedDate,
                        'approve_status' => 'Approved',
                        'day_type' => 'Whole Day',
                    ]);

                    // Create a notification for the sick leave
                    $template = MessageTemplate::where('key', 'sick_leave_notification')->first();
                    if ($template) {
                        Notification::create([
                            'user_id' => $user->id,
                            'message_template_id' => $template->id,
                            'is_read' => false,
                        ]);

                        // Log notification creation
                        \Log::info('Sick leave notification created', [
                            'user_id' => $user->id,
                            'message_template_id' => $template->id,
                        ]);
                    } else {
                        // Log missing template
                        \Log::warning('Sick leave notification template not found');
                    }
                }

                $current->modify('+1 day');
            }
        }

        // Deduct holiday balance
        $user->employee->decrement('leave_balance', $requestedDays);

        return response()->json([
            'message' => 'Requests saved successfully',
            'remainingHolidays' => $user->employee->leave_balance
        ]);
    }

    public function getSickLeaves()
    {
        $sickLeaves = Vacation::where('vacation_type', 'Sick Leave')
            ->with('employee.user') // Ensure employee and user relationships are loaded
            ->get()
            ->map(function ($vacation) {
                return [
                    'id' => $vacation->vacation_id,
                    'employee_name' => optional($vacation->employee->user)->first_name . ' ' . optional($vacation->employee->user)->last_name,
                    'start_date' => $vacation->start_date,
                    'end_date' => $vacation->end_date,
                    'approve_status' => $vacation->approve_status,
                    'day_type' => $vacation->day_type,
                ];
            });

        return response()->json($sickLeaves);
    }

    public function saveVacation(Request $request)
    {
        $validated = $request->validate([
            'holidays' => 'required|array',
            'sickDays' => 'required|array',
        ]);

        foreach ($validated['holidays'] as $date => $type) {
            Vacation::create([
                'user_id' => Auth::id(),
                'date' => $date,
                'type' => $type,
                'status' => 'pending',
            ]);
        }

        foreach ($validated['sickDays'] as $sickDay) {
            Vacation::create([
                'user_id' => Auth::id(),
                'date' => $sickDay['start_date'],
                'type' => 'sick',
                'status' => 'pending',
            ]);
        }

        return response()->json(['message' => 'Vacation requests saved successfully.']);
    }
}
