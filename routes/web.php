<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ChartController;

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


// ======================= Pick Up Point====================== //
Route::get('/pickup', [PackageController::class,'index'])->name('pickup.dashboard');

// test for demo

Route::get('/manager-calendar', function () {
    return view('employees.manager_calendar');  // Adjust based on the folder structure
});

Route::get('/manager-calendar', function () {
    return view('employees.manager_calendar');
})->name('manager-calendar');
