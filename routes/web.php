<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Login
Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('auth.authenticate');

// Register
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'store'])->name('auth.store');

// Update
Route::post('/update', [AuthController::class, 'update'])->name('auth.update');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Customers
Route::get('/customers', function () {
    if (!Auth::check()) {
        return redirect()->route('auth.login');
    }
    return view('customers');
})->name('customers');

Route::get('/courier', function () {
    return view('courier.index');
})->name('index.page');

Route::get('/courier/route', function () {
    return view('courier.route');
})->name('route.page');

Route::get('/courier/packages', function () {
    return view('courier.packages');
})->name('packages.page');

Route::get('/courier/scan', function () {
    return view('courier.scan');
})->name('scan.page');

Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');


// ======================= Employee====================== //

Route::get('/calendar', function () {
    return view('employees.calendar');
});

Route::get('/holiday-requests', function () {
    return view('employees.holiday_request');
});

// test for demo 

Route::get('/manager-calendar', function () {
    return view('employees.manager_calendar');  // Adjust based on the folder structure
});

Route::get('/manager-calendar', function () {
    return view('employees.manager_calendar');
})->name('manager-calendar');
