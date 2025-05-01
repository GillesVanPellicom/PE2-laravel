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
            ->whereIn('message_template_id', [1, 2]) // Include only message_template_id 1 and 2
            ->with('messageTemplate') // Include the message template relationship
            ->get();

        return response()->json($notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->messageTemplate->message ?? 'No message', // Fetch the message from the related message template
                'created_at' => $notification->created_at,
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
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id()) // Ensure the notification belongs to the logged-in user
            ->first();

        if ($notification) {
            $notification->is_read = true;
            $notification->save();

            return response()->json(['message' => 'Notification marked as read.']);
        }

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
}

