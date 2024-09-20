<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/employers', [App\Http\Controllers\EmployerController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::get('admin/employers', [EmployerController::class, 'index']);
    Route::post('admin/employers', [EmployerController::class, 'store']);
    Route::get('admin/employers/{employer}', [EmployerController::class, 'show']);
    Route::post('admin/employers/{employer}/employees', [EmployeeController::class, 'store']);
});

