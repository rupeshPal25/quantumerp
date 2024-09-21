<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    // Employers routes
    Route::get('admin/employers', [EmployerController::class, 'index']);
    Route::post('admin/employers', [EmployerController::class, 'store']);
    Route::get('admin/employers/{employer}', [EmployerController::class, 'show'])->name('employers.show');
    
    // Employees routes (to add employees to an employer)
    // Route::post('admin/employers/{employer}/employees', [EmployeeController::class, 'store']);
    // Route::post('admin/employers/{employer}/employees', [EmployeeController::class, 'addEmployee']);


    // Employee routes
    Route::post('admin/employers/{employer}/employees', [EmployeeController::class, 'addEmployee']);

});
