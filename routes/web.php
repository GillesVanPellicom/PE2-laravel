<?php

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

<<<<<<< HEAD
=======
Route::post('/package/update', [PackageController::class, 'updateStatus'])->name('package.update');

>>>>>>> ffdcb88737ee4e79a4f1f9df7937232efde3c71c
Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');
Route::get('/package/qr/{id}', [PackageController::class,'generateQRcode'])->name('package.generateqr');
