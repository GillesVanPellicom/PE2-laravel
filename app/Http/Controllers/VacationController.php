<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacation;
use Illuminate\Support\Facades\Auth;
use Laravel\Pail\ValueObjects\Origin\Console;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use App\Models\Notification;
use App\Models\MessageTemplate;
use App\Models\Employee;

class VacationController extends Controller
{
    public function getPendingVacations()
    {
        $pendingVacations = Vacation::where('approve_status', 'Pending')
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

        return response()->json($pendingVacations);
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
        $vacation->approve_status = $request->status; // Update the status based on the request
        $vacation->save();

        // Fetch the employee's user ID
        $employee = $vacation->employee;

        if ($employee) {
            // If the request is rejected, increment the employee's leave balance based on day_type
            if ($request->status === 'rejected') {
                $incrementValue = ($vacation->day_type === 'Whole Day') ? 1 : 0.5; // Restore full or half day
                $employee->increment('leave_balance', $incrementValue);
            }

            // Fetch the message template based on the status
            $templateId = $request->status === 'Approved' ? 1 : 2; // 1 for approved, 2 for rejected
            $template = MessageTemplate::find($templateId);

            if ($template) {
                // Ensure the user_id is correctly set for the notification
                $userId = $employee->user_id;

                if ($userId) {
                    // Save the notification
                    Notification::create([
                        'user_id' => $userId,
                        'message_template_id' => $template->id,
                        'is_read' => false, // Mark the notification as unread
                    ]);
                } else {
                    \Log::error("Failed to create notification: user_id is missing for employee", ['employee_id' => $employee->id]);
                }
            } else {
                \Log::error("Failed to fetch message template", ['template_id' => $templateId]);
            }
        } else {
            \Log::error("Failed to fetch employee for vacation", ['vacation_id' => $id]);
        }

        // Return the updated vacation data with the message
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
            'approve_status' => strtolower($vacation->approve_status), // Ensure the status is lowercase
            'color' => $statusColors[strtolower($vacation->approve_status)] ?? '#6C757D', // Default to gray if status is unknown
            'message' => $template->message ?? 'No message found', // Include the message from the template
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

    public function getSickLeaveNotifications()
    {
        // Fetch sick leave notifications where is_read is 0
        $notifications = Notification::whereHas('messageTemplate', function ($query) {
                $query->where('key', 'sick_leave_notification');
            })
            ->where('is_read', 0)
            ->with('user')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'employee_name' => optional($notification->user)->first_name . ' ' . optional($notification->user)->last_name,
                    'message' => $notification->messageTemplate->content,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($notifications);
    }

    public function markSickLeaveAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = 1;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return response()->json(['error' => 'You must be an employee to request a vacation'], 403);
        }

        $leaveBalance = $user->employee->leave_balance;
        $sickLeaveBalance = $user->employee->sick_leave_balance; // Fetch sick leave balance
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
        $sickDaysCount = 0; // Track the number of sick days added
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

                    $sickDaysCount++; // Increment the count of sick days added
                }

                $current->modify('+1 day');
            }
        }

        // Increment sick leave balance
        $user->employee->increment('sick_leave_balance', $sickDaysCount);

        // Deduct holiday balance
        $user->employee->decrement('leave_balance', $requestedDays);

        return response()->json([
            'message' => 'Requests saved successfully',
            'remainingHolidays' => $user->employee->leave_balance,
            'remainingSickLeave' => $user->employee->sick_leave_balance, // Include updated sick leave balance
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

    public function getDayDetails(Request $request)
    {
        $date = $request->input('date');

        $availableEmployees = Employee::whereDoesntHave('vacations', function ($query) use ($date) {
            $query->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date)
                  ->where('approve_status', 'approved');
        })->get(['id', 'user_id']);

        $sickEmployees = Vacation::where('vacation_type', 'Sick Leave')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('approve_status', 'approved')
            ->with('employee.user')
            ->get();

        $holidayEmployees = Vacation::where('vacation_type', 'Holiday')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('approve_status', 'approved')
            ->with('employee.user')
            ->get();

        $pendingRequestsCount = Vacation::where('approve_status', 'Pending') // Count pending requests
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->count();

        return response()->json([
            'available' => $availableEmployees,
            'sick' => $sickEmployees,
            'holiday' => $holidayEmployees,
            'pendingCount' => $pendingRequestsCount, // Return the count of pending requests
        ]);
    }

    public function getPendingRequestsForDay(Request $request)
    {
        $date = $request->input('date');

        $pendingRequests = Vacation::where('approve_status', 'Pending')
            ->where('start_date', $date)
            ->with('employee.user') // Include employee and user relationships
            ->get()
            ->map(function ($vacation) {
                return [
                    'id' => $vacation->vacation_id,
                    'employee_name' => optional($vacation->employee->user)->first_name . ' ' . optional($vacation->employee->user)->last_name,
                    'vacation_type' => $vacation->vacation_type,
                    'day_type' => $vacation->day_type,
                    'start_date' => $vacation->start_date,
                    'end_date' => $vacation->end_date,
                ];
            });

        return response()->json($pendingRequests);
    }

    public function getAvailabilityData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $dates = collect();
        $currentDate = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        while ($currentDate <= $end) {
            $dates->push($currentDate->format('Y-m-d'));
            $currentDate->modify('+1 day');
        }

        $availabilityData = $dates->map(function ($date) {
            $available = Employee::whereDoesntHave('vacations', function ($query) use ($date) {
                $query->where('start_date', '<=', $date)
                      ->where('end_date', '>=', $date)
                      ->where('approve_status', 'approved');
            })->count();

            $onHoliday = Vacation::where('vacation_type', 'Holiday')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->where('approve_status', 'approved')
                ->count();

            $sick = Vacation::where('vacation_type', 'Sick Leave')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->where('approve_status', 'approved')
                ->count();

            $pending = Vacation::where('approve_status', 'Pending') // Fetch pending requests
                ->where('start_date', $date)
                ->count();

            return [
                'date' => $date,
                'available' => $available,
                'onHoliday' => $onHoliday,
                'sick' => $sick,
                'pending' => $pending, // Include pending count as a new column
            ];
        });

        return response()->json($availabilityData);
    }

    public function sendEndOfYearNotifications()
    {
        $template = MessageTemplate::where('key', 'End_Of_Year')->first();

        if (!$template) {
            return response()->json(['message' => 'Message template not found.'], 404);
        }

        $employees = Employee::with('user')->get();

        foreach ($employees as $employee) {
            if ($employee->leave_balance > 0) {
                $message = "You have {$employee->leave_balance} remaining holidays left for this year, please use them before the end of the year. test";

                Notification::create([
                    'user_id' => $employee->user_id,
                    'message_template_id' => $template->id,
                    'is_read' => false, // Mark the notification as unread
                    'message' => $message, // Save the generated message
                ]);
            }
        }

        return response()->json(['message' => 'End-of-year notifications saved successfully.']);
    }

    public function markEmployeeAsSick($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        // Create a sick leave entry for today
        $today = now()->format('Y-m-d');
        Vacation::create([
            'employee_id' => $employee->id,
            'vacation_type' => 'Sick Leave',
            'start_date' => $today,
            'end_date' => $today,
            'approve_status' => 'Approved',
            'day_type' => 'Whole Day',
        ]);

        return response()->json(['message' => "{$employee->user->first_name} {$employee->user->last_name} has been marked as sick."]);
    }
}
