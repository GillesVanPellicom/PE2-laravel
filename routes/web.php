<?php


use Illuminate\Support\Facades\Route;

use Pnlinh\GoogleDistance\Facades\GoogleDistance;

use App\Http\Controllers\ChartController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\TrackPackageController;
use App\Http\Controllers\contractController;
use App\Http\Controllers\flightscontroller;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\airportController;


// ======================= Start Courier ====================== //

# => Courier Mobile app

Route::get('/courier', [CourierController::class, "index"])->name('courier');
Route::get('/courier/route', [CourierController::class, "route"])->name('courier.route');
Route::get('/courier/packages', [CourierController::class, "packages"])->name('courier.packages');
Route::get("/courier/scan", [CourierController::class, "scan"])->name("courier.scan");
Route::post("/courier/scanQr", [CourierController::class, "scanQr"])->name("courier.scanQr");

# Test Route
Route::get("/courier/generate/{id}", [PackageController::class, "generateQRcode"])->name("generateQR");

# <= END Courier Mobile App 

Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');

// ======================= End Courier ====================== //

// ======================= Start Distribution ====================== //

Route::get('/packagechart', [ChartController::class, 'getPackageData'])->name('package.chart');

// ======================= End Distribution ====================== //

// ======================= Start Employee ====================== //

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

Route::get('/employees', 'App\Http\Controllers\EmployeeController@index')->name('employees.index');

Route::get('/employees/create', 'App\Http\Controllers\EmployeeController@create')->name('employees.create');

Route::post('/employees', 'App\Http\Controllers\EmployeeController@store_employee')->name('employees.store_employee');

// ======================= End Employee ====================== //

// ======================= Start Pick Up Point ====================== //

Route::get('/pickup', [PackageController::class,'index'])->name('pickup.dashboard');

// ======================= End Pick Up Point ====================== //

// ======================= Start Airport ====================== //

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

// ======================= End Airport ====================== //

// ======================= Start Customer ====================== //

Route::get('/send-package', [PackageController::class, 'create'])->name('packages.send-package');

Route::post('/send-package', [PackageController::class, 'store'])->name('package.store');

//--------------------------------- Tracking Packages ---------------------------------//
Route::get('/track/{reference}', [TrackPackageController::class, 'track'])->name('track.package');
//--------------------------------- ENDTracking Packages ---------------------------------//

// ======================= End Customer ====================== //


