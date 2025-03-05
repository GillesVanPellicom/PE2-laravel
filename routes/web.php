<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ParcelController;
use Pnlinh\GoogleDistance\Facades\GoogleDistance;

Route::get('/', function () {
    return view('index');
})->name('index.page');

Route::get('/route', function () {
    return view('route');
})->name('route.page');

Route::get('/packages', function () {
    return view('packages');
})->name('packages.page');

Route::controller(ParcelController::class)->group(function () {
    Route::get('/send-parcel', 'create')->name('parcel.create');
    Route::post('/send-parcel', 'store')->name('parcel.store');
});

Route::get('/scan', function () {
    return view('scan');
})->name('scan.page');

Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');
