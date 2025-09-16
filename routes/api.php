<?php

use App\Http\Controllers\ProdyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacultyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/faculties', [FacultyController::class, 'index'])->name('api.faculties');
Route::get('/faculties/{id}', [ProdyController::class, 'index'])->name('api.prody');
