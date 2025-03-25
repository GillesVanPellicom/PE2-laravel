<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function showCalendar()
    {
        // Fetch notifications for the employee (you can adjust this based on your requirements)
        return view('employees.calendar', ['notifications' => Notification::all()]);
    } 
}



    
    


