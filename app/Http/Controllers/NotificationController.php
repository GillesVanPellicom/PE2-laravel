<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Method to fetch notifications for the AJAX request
    public function fetchNotifications()
    {
        // Fetch notifications for the logged-in user
        $notifications = Notification::where('user_id', auth()->id())
            ->with('messageTemplate', 'user')
            ->get();

        // Return notifications as JSON
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
    
}

