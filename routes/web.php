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

Route::post('/package/update', [PackageController::class, 'updateStatus'])->name('package.update');

