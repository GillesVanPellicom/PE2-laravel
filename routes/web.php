<?php

use App\Http\Controllers\airportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use Pnlinh\GoogleDistance\Facades\GoogleDistance;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\TrackPackageController;
use App\http\Controllers\AuthController;
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

Route::get('/send-package', [PackageController::class, 'create'])->name('packages.send-package');
Route::post('/send-package', [PackageController::class, 'store'])->name('package.store');

Route::get('/scan', function () {
    return view('scan');
});
Route::get('/courier/scan', function () {
    return view('courier.scan');
})->name('scan.page');

Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');

Route::get('/packagechart', [ChartController::class, 'getPackageData'])->name('package.chart');

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


// *** EMPLOYEES ***

Route::get('/employees', 'App\Http\Controllers\EmployeeController@index')->name('employees.index');

Route::get('/employees/create', 'App\Http\Controllers\EmployeeController@create')->name('employees.create');

Route::post('/employees', 'App\Http\Controllers\EmployeeController@store_employee')->name('employees.store_employee');

// *** END EMPLOYEES ***
use App\Http\Controllers\contractController;
use App\Http\Controllers\flightscontroller;









Route::get('/employees', function () {
    return view('employees');
})->name('employees');

Route::get('/packages', function () {
    return view('packages');
})->name('packages');




Route::get('/contract', [contractController::class, 'contractindex'])->name('contract');
Route::get('/contractcreate', [contractController::class, 'contractcreate'])->name('contractcreate');
Route::post('/contract', [contractController::class, 'store'])->name('contract.store');

Route::get('/flights', [FlightsController::class, 'flightindex'])->name('flights');
Route::get('/flightcreate', [flightscontroller::class, 'flightcreate'])->name('flightcreate');
Route::post('/flights', [flightscontroller::class, 'store'])->name('flight.store');

Route::get('/airport', [airportController::class, 'airportindex'])->name('airports');


//--------------------------------- Tracking Packages ---------------------------------//
Route::get('/track/{reference}', [TrackPackageController::class, 'track'])->name('track.package');
//--------------------------------- ENDTracking Packages ---------------------------------//