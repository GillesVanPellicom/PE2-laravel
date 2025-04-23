<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Vacation;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Method to fetch notifications for the AJAX request
    public function fetchNotifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', 0) // Fetch only unread notifications
            ->whereHas('messageTemplate', function ($query) {
                $query->where('key', '!=', 'sick_leave_notification'); // Exclude sick leave notifications
            })
            ->with('messageTemplate', 'user')
            ->get();

        return response()->json($notifications);
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
        $notification = Notification::find($id);
        if ($notification) {
            $notification->is_read = true;
            $notification->save();
            return response()->json(['message' => 'Notification marked as read.']);
        }
        return response()->json(['error' => 'Notification not found.'], 404);
    }

    public function fetchSickDayNotifications()
    {
        $notifications = Notification::whereHas('messageTemplate', function ($query) {
                $query->where('key', 'sick_leave_notification'); // Ensure the correct message template key is used
            })
            ->where('is_read', false) // Fetch only unread notifications
            ->with(['messageTemplate', 'user']) // Include message template and user relationships
            ->get();

        return response()->json($notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => str_replace(
                    ['{employee_name}', '{date}'],
                    [
                        optional($notification->user)->first_name . ' ' . optional($notification->user)->last_name, // Use the user's name
                        $notification->created_at->format('Y-m-d') // Use the notification's creation date
                    ],
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
}

