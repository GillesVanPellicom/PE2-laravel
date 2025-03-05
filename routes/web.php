<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// *** EMPLOYEES ***

Route::get('/employees', 'App\Http\Controllers\EmployeeController@index')->name('employees.index');

Route::get('/employees/create', 'App\Http\Controllers\EmployeeController@create')->name('employees.create');

Route::post('/employees', 'App\Http\Controllers\EmployeeController@store_employee')->name('employees.store_employee');

// *** END EMPLOYEES ***