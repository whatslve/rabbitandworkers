<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use \App\Http\Controllers\NotificationController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/all', [OrderController::class, 'index']);
Route::get('/notifications/all', [NotificationController::class, 'index']);
