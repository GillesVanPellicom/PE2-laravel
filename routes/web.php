<?php

use App\Http\Controllers\airportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;

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
Route::get('/package/qr/{id}', [PackageController::class,'generateQRcode'])->name('package.generateqr');

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