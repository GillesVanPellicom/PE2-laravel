<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function showCalendar()
    {
        // Fetch notifications for the employee (you can adjust this based on your requirements)
        $notifications = Notification::where('user_id', auth()->id())
            ->with('messageTemplate', 'user')
            ->get();

        return view('employees.calendar', compact('notifications'));
    } 
}



    
    


