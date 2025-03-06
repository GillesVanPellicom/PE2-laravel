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

Route::get('/send-package', [PackageController::class, 'create'])->name('packages.send-package');

Route::post('/send-package', [PackageController::class, 'store'])->name('package.store');

Route::get('/scan', function () {
    return view('scan');
})->name('scan.page');

Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');
