<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;

Route::get('/', function () {
    return view('index');
})->name('index.page');

Route::get('/route', function () {
    return view('route');
})->name('route.page');

Route::get('/packages', function () {
    return view('packages');
})->name('packages.page');

Route::get('/scan', function () {
    return view('scan');
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
