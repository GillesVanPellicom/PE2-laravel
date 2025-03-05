<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;

Route::get('/courrier', function () {
    return view('courrier.index');
})->name('index.page');

Route::get('/courrier/route', function () {
    return view('courrier.route');
})->name('route.page');

Route::get('/courrier/packages', function () {
    return view('courrier.packages');
})->name('packages.page');

Route::get('/courrier/scan', function () {
    return view('courrier.scan');
})->name('scan.page');

Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');
Route::get('/package/qr/{id}', [PackageController::class,'generateQRcode'])->name('package.generateqr');