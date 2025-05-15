<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'It works!']);
});


Route::middleware("auth:sanctum")->group(function(){
    Route::get("/packages", [ApiController::class, "getPackages"]);
    Route::get("/package", [ApiController::class, "packageInfo"]);
    Route::get("/node", [ApiController::class, "nodeInfo"]);
    Route::get("/address", [ApiController::class, "addressInfo"]);
});