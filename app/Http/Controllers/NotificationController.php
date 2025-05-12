<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Vacation;
use App\Models\MessageTemplate;
use App\Models\Employee;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Method to fetch notifications for the AJAX request
    public function fetchNotifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', 0) // Fetch only unread notifications
            ->with('messageTemplate') // Include related message template
            ->get();

        return response()->json($notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->messageTemplate->message ?? 'No message',
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'is_read' => $notification->is_read,
            ];
        }));
    }

    public function showCalendar()
    {
        // Fetch notifications for the employee (you can adjust this based on your requirements)
        $notifications = Notification::where('user_id', auth()->id())
            ->with('messageTemplate', 'user')
            ->get();

        // Return the calendar view along with the notifications
        return view('employees.calendar', compact('notifications'));
    }

    // Method to mark a notification as read
    public function markAsRead($id)
    {
        \Log::info('Mark as read called', ['id' => $id, 'user_id' => auth()->id()]); // Debugging log

        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id()) // Ensure the notification belongs to the logged-in user
            ->first();

        if ($notification) {
            $notification->is_read = true;
            $notification->save();

            \Log::info('Notification marked as read', ['id' => $id]); // Debugging log
            return response()->json(['message' => 'Notification marked as read.']);
        }

        \Log::warning('Notification not found or unauthorized', ['id' => $id, 'user_id' => auth()->id()]); // Debugging log
        return response()->json(['error' => 'Notification not found or unauthorized.'], 404);
    }

    public function fetchSickDayNotifications()
    {
        $notifications = Notification::where('message_template_id', 4) // Ensure correct message_template_id
            ->where('is_read', 0) // Fetch only unread notifications
            ->with(['messageTemplate', 'user']) // Include related message template and user
            ->get();

        return response()->json($notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => str_replace(
                    '{employee_name}',
                    optional($notification->user)->first_name . ' ' . optional($notification->user)->last_name, // Replace placeholder with employee name
                    $notification->messageTemplate->message ?? 'No message'
                ),
                'created_at' => $notification->created_at,
            ];
        }));
    }

    public function markSickLeaveAsRead($id)
    {
        $notification = Notification::find($id); // Ensure the ID corresponds to a notification
        if ($notification) {
            $notification->is_read = true; // Mark the notification as read
            $notification->save();
            return response()->json(['message' => 'Sick leave notification marked as read.']);
        }
        return response()->json(['error' => 'Notification not found.'], 404);
    }

    public function sendEndOfYearNotifications()
    {
        \Log::info('sendEndOfYearNotifications called');

        $template = MessageTemplate::where('key', 'End_Of_Year')->first();

        if (!$template) {
            \Log::error('Message template not found');
            return response()->json(['message' => 'Message template not found.'], 404);
        }

        $employees = Employee::with('user')->get();

        foreach ($employees as $employee) {
            if ($employee->leave_balance > 0) {
                \Log::info('Debugging notification', [
                    'template_message' => $template->message,
                    'leave_balance' => $employee->leave_balance,
                    'replaced_message' => str_replace('{leave_balance}', $employee->leave_balance, $template->message),
                ]);
                
                $message = str_replace('{leave_balance}', $employee->leave_balance, $template->message);
                
        
                Notification::create([
                    'user_id' => $employee->user_id,
                    'message_template_id' => $template->id,
                    'is_read' => false,
                    'message' => $message,
                ]);
        
                \Log::info('Notification created', [
                    'user_id' => $employee->user_id,
                    'message' => $message,
                ]);
            }
        }
        

        return response()->json(['message' => 'End-of-year notifications created successfully.']);
    }

    public function fetchEndOfYearNotifications()
    {
        $notifications = Notification::whereHas('messageTemplate', function ($query) {
                $query->where('key', 'End_Of_Year');
            })
            ->with(['messageTemplate', 'user']) // Include related message template and user
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'employee_name' => optional($notification->user)->first_name . ' ' . optional($notification->user)->last_name,
                    'message' => $notification->message,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($notifications);
    }
}

